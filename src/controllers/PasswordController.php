<?php

namespace Amostajo\Wordpress\LoginPageAddon\Controllers;

use WP_Error;
use Exception;
use Amostajo\WPPluginCore\Log;
use Amostajo\WPPluginCore\Cache;
use Amostajo\LightweightMVC\Controller;
use Amostajo\LightweightMVC\Request;
use Amostajo\Wordpress\LoginPageAddon\LoginPage;

/**
 * Password related operations.
 *
 * @author Alejandro Mostajo
 * @license MIT
 * @package Amostajo\Wordpress\PostPickerAddon
 * @version 1.0
 */
class PasswordController extends Controller
{
    /**
     * Displayes lost password page.
     * @since 1.0
     */
    public function lost()
    {
        wp_enqueue_script( 'addon-loginpage' );
        return $this->view->get( 'addons.loginpage.lostpassword', [
            'token'         => LoginPage::generate_token(),
            'action'        => admin_url( 'admin-ajax.php?action=addon_lostpassword' ),
            'redirect_to'   => Request::input( 'redirect_to' ),
        ] );
    }

    /**
     * Resets.
     * @since 1.0
     */
    public function reset()
    {
        $input = [
            'user_login'    => Request::input( 'ref' ),
            'key'           => Request::input( 'key' )
        ];

        $user = $errors = null;

        if ( !$this->is_reset_valid( $input, $user, $errors ) ) {
            global $wp_query;
            $wp_query->set_404();
            status_header( 404 );
            nocache_headers();
            return;
        }

        wp_enqueue_script( 'addon-loginpage' );
        return $this->view->get( 'addons.loginpage.resetpassword', [
            'token'         => LoginPage::generate_token(),
            'action'        => admin_url( 'admin-ajax.php?action=addon_resetpassword' ),
            'user'          => $user,
        ] );
    }

    /**
     * Performs ajax login.
     * @since 1.0
     */
    public function ajax()
    {
        $input = [
            'token'         => Request::input( '_token' ),
            'user_login'    => Request::input( 'user_login' ),
            'user_password' => Request::input( 'user_password' ),
            'remember'      => Request::input( 'remember', false ),
        ];

        header( 'Content-Type: application/json' );
        if ( get_option( 'addon_login_crossdomain', false ) ) {
            header( 'Access-Control-Allow-Origin: *' );
        }
        try {
            // Method validation
            if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
                throw new Exception( 'Invalid request method.' );
            }

            // Token validation
            if ( $input[ 'token' ] != LoginPage::get_token() ) {
                throw new Exception( 'Invalid security token.' );
            }

            $user = $errors = null;
            $this->request( $user, $errors );

            // Send new password.
            $this->send_reset( $user, $errors  );

            if ( !empty( $errors->errors ) ) {

                $raw = [];
                foreach ( $errors->get_error_messages() as $message ) {
                    $raw[] = $message;
                }

                echo json_encode( [
                    'errors'    => $raw,
                    300
                ] );
                wp_die();
            }

            $message = 'An email with instructions on to how to reset your password has been sent to your email address.'
                . '<br>Please check your inbox to continue.';

            echo json_encode( [
                'message'   => apply_filters( 'addon_loginpage_forgotpassword_message', $message ),
                200
            ] ); 

        } catch( Exception $e ) {
            echo json_encode( [
                'errors' => [ $e->getMessage() ],
                300
            ] ); 
        }
        wp_die();
    }

    /**
     * Performs ajax login.
     * @since 1.0
     */
    public function ajax_reset()
    {
        $input = [
            'token'         => Request::input( '_token' ),
            'ID'            => Request::input( 'ID' ),
            'user_pass'     => Request::input( 'user_pass' ),
            'repeat_pass'   => Request::input( 'repeat_pass' ),
        ];

        header( 'Content-Type: application/json' );
        if ( get_option( 'addon_login_crossdomain', false ) ) {
            header( 'Access-Control-Allow-Origin: *' );
        }
        try {
            // Method validation
            if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
                throw new Exception( 'Invalid request method.' );
            }

            // Token validation
            if ( $input[ 'token' ] != LoginPage::get_token() ) {
                throw new Exception( 'Invalid security token.' );
            }

            $user = get_user_by( 'id', $input[ 'ID' ] );

            $errors = new WP_Error;

            if ( empty( $user ) )
                $errors->add(
                    'invalid_user',
                    'The <strong>User</strong> is invalid.'
                );
            if ( empty( $input[ 'user_pass' ] ) )
                $errors->add(
                    'empty_password',
                    'Field <strong>Password</strong> cannot be empty.'
                );
            if ( empty( $input[ 'repeat_pass' ] ) )
                $errors->add(
                    'empty_repeat_password',
                    'Field <strong>Password (repeat)</strong> can not be empty.'
                );
            if ( $input[ 'user_pass' ] !== $input[ 'repeat_pass' ] )
                $errors->add(
                    'unmatch_passwords',
                    'Fields <strong>Password</strong> and <strong>Password (repeat)</strong> must match.'
                );

            if ( !empty( $errors->errors ) ) {

                $raw = [];
                foreach ( $errors->get_error_messages() as $message ) {
                    $raw[] = $message;
                }

                echo json_encode( [
                    'errors'    => $raw,
                    300
                ] );
                wp_die();
            }

            // Change password
            wp_set_password( $input[ 'user_pass' ], $user->ID );
            do_action( 'password_reset', $user, $input[ 'user_pass' ] );
            wp_cache_delete( $user->user_login, 'userlogins' );

            $message = 'Password changed successfully. <a href="'
                . home_url( '/wp-login.php' )
                . '">Would you like to try to login?</a>';

            echo json_encode( [
                'message'   => apply_filters( 'addon_loginpage_resetpassword_message', $message ),
                200
            ] ); 

        } catch( Exception $e ) {
            echo json_encode( [
                'errors' => [ $e->getMessage() ],
                300
            ] ); 
        }
        wp_die();
    }

    /**
     * Sends reset link to user.
     * @since 1.0
     *
     * @param object $user WP_User.
     */
    protected function send_reset( $user, &$errors )
    {
        if ( !empty( $errors->errors ) || empty( $user ) ) return;
        // Begin
        global $wpdb;
        // Reset activation key
        $key = wp_generate_password( 20, false );
        do_action( 'retrieve_password_key', $user->user_login, $key );
        $wpdb->update(
            $wpdb->users,
            [
                'user_activation_key' => $key
            ],
            [
                'ID' => $user->ID
            ]
        );
        if ( !wp_mail(
                $user->user_email,
                apply_filters( 'retrieve_password_title' , 'Password reset instructions' ),
                apply_filters(
                    'retrieve_password_message',
                    $this->view->get( 'addons.loginpage.emails.resetpassword', [
                        'user'          => $user,
                        'key'           => $key,
                        'reset_link'    => home_url( '/wp-login.php?action=rp&key=' . $key . '&ref=' . $user->user_login )
                    ] ),
                    $key
                )
            )
        ) {
            $errors->add(
                'mail_disabled',
                '<strong>Whoops!</strong>, something went wrong and the reset email could not be sent.'
                    . '<br>Please contact support.'
            );
        }
    }

    /**
     * Requests user information.
     * @since 1.0
     *
     * @param object $user By reference user.
     * @param object $user By reference errors.
     */
    protected function request( &$user, &$errors )
    {
        $errors = new WP_Error();

        $value = Request::input( 'user_login' );

        if ( empty( $value ) ) {

            $errors->add(
                'empty_user_login',
                'Field <strong>Username or Email</strong> cannot be empty.'
            );

        } else if ( preg_match('/\@/', $value ) ) {

            if ( !is_email( $value ) ) {
                $errors->add(
                    'invalid_email',
                    'Field <strong>Email</strong> is invalid.'
                );
            } else if ( email_exists( $value ) ) {
                $user = get_user_by( 'email', $value );
            } else {
                $errors->add(
                    'email_unknown',
                    'Unknown <strong>Email</strong>.'
                );
            }

        } else {

            if ( username_exists( $value ) ) {
                $user = get_user_by( 'login', $value );
            } else { 
                $errors->add(
                    'username_unknown',
                    'Unknown <strong>Username</strong>.'
                );
            }

        }

        if ( empty( $user ) ) return;

        // Last actions and validations
        do_action( 'lostpassword_post' );
        do_action( 'retrieve_password', $user->user_login );
        if ( !apply_filters( 'allow_password_reset', true, $user->ID ) ) {
            $errors->add(
                'no_password_reset',
                'Password reset is not allowed for this user.'
            );
        }
    }

    /**
     * Validates if the reset is valid.
     * Returns flag.
     * @since 1.0
     *
     * @param array  $input  Input from request.
     * @param object $user   By reference user.
     * @param object $errors By reference WP_Error.
     *
     * @return bool
     */
    protected function is_reset_valid( $input, &$user, &$errors )
    {
        $errors = new WP_Error;
        if ( empty( $input[ 'key' ] )
            || empty( $input[ 'user_login' ] )
        ) {
            $errors->add(
                'empty_reset',
                'Reset link is invalid. Empty references.'
            );
        } else {
            global $wpdb;
            $user = $wpdb->get_row( $wpdb->prepare(
                'SELECT * FROM ' . $wpdb->users . '
                WHERE user_login = %s
                    AND user_activation_key = %s',
                sanitize_user( $input[ 'user_login' ] ),
                $input[ 'key' ]
            ) );

            if ( !$user )
                $errors->add(
                    'invalid_user',
                    'This <strong>reset link</strong> does not appear to be valid.'
                );
        }

        $errors = apply_filters(
            'reset_password_errors',
            $errors,
            $input,
            $user
        );

        return empty( $errors->errors );
    }
}