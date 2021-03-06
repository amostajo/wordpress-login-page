<?php

namespace Amostajo\Wordpress\LoginPageAddon\Controllers;

use WP_Error;
use Exception;
use WPMVC\Log;
use WPMVC\Cache;
use WPMVC\Request;
use WPMVC\MVC\Controller;
use Amostajo\Wordpress\LoginPageAddon\LoginPage;

/**
 * Login operations.
 *
 * @author Alejandro Mostajo
 * @license MIT
 * @package Amostajo\Wordpress\PostPickerAddon
 * @version 2.0.0
 */
class SignupController extends Controller
{
    /**
     * Displays registration page.
     * @since 1.0
     */
    public function register()
    {
        wp_enqueue_script( 'addon-loginpage' );
        return $this->view->get( 'addons.loginpage.signup', [
            'token'         => LoginPage::generate_token(),
            'action'        => admin_url( 'admin-ajax.php?action=addon_signup' ),
            'redirect_to'   => Request::input( 'redirect_to' ),
        ] );
    }

    /**
     * Performs ajax signup.
     * @since 1.0
     * @since 2.0.0 Added actions.
     */
    public function ajax()
    {
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
            if ( Request::input( '_token' ) != LoginPage::get_token() ) {
                throw new Exception( 'Invalid security token.' );
            }

            $userdata = $this->generate_userdata();

            // Run validations
            $errors = $this->validate( $userdata );
            do_action(
                'register_post',
                $userdata['user_login'],
                $userdata['user_email'],
                $errors
            );
            $errors = apply_filters(
                'registration_errors',
                $errors,
                $userdata['user_login'],
                $userdata['user_email']
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

            // Insert other
            $userdata['ID'] = wp_insert_user( $userdata );

            do_action( 'user_register', $userdata['ID'] );

            do_action( 'addon_loginpage_after_signup', $userdata );

            $message = 'Your account has been created!'
                . '<div><a href="'
                . LoginPage::get_redirect_to()
                . '">> back to page I was reading</a></div>'
                . '<a href="' . home_url( '/wp-login.php' ) . '?redirect_to='
                . LoginPage::get_redirect_to()
                . '">> login now</a>';

            echo json_encode( [
                'message'   => apply_filters( 'addon_loginpage_signup_message', $message ),
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
     * Generates user based on request input.
     * @since 1.0
     *
     * @return array
     */
    protected function generate_userdata()
    {
        $userdata = [
            'user_login'    => sanitize_user( Request::input( 'user_login' ) ),
            'user_email'    => Request::input( 'user_email' ),
            'user_pass'     => Request::input( 'user_pass' ),
            'user_url'      => Request::input( 'user_url', '' ),
            'first_name'    => Request::input( 'first_name', '' ),
            'last_name'     => Request::input( 'last_name', '' ),
            'display_name'  => Request::input( 'display_name', Request::input( 'user_login' ) ),
            'repeat_pass'   => Request::input( 'repeat_pass' ),
        ];
        return apply_filters( 'addon_loginpage_signup_userdata', $userdata );
    }

    /**
     * Validates user input.
     * @since 1.0
     *
     * @param array $user User input
     */
    protected function validate( $user )
    {
        $errors = new WP_Error();

        if ( empty( $user['user_login'] ) ) {
            $errors->add(
                'empty_username',
                __( 'Field <strong>Username</strong> can not be empty.' )
            );
        }

        if ( empty( $user['user_email'] ) ) {
            $errors->add(
                'empty_email',
                __( 'Field <strong>Email</strong> can not be empty.' )
            );
        }

        if ( empty( $user['user_pass'] ) ) {
            $errors->add(
                'empty_password',
                __( 'Field <strong>Password</strong> can not be empty.' )
            );
        }

        if ( empty( $user['repeat_pass'] ) ) {
            $errors->add(
                'empty_repeat_password',
                __( 'Field <strong>Password (repeat)</strong> can not be empty.' )
            );
        }

        if ( $user['user_pass'] !== $user['repeat_pass'] ) {
            $errors->add(
                'unmatch_passwords',
                __( 'Fields <strong>Password</strong> and <strong>Password (repeat)</strong> must match.' )
            );
        }

        if ( !validate_username( $user['user_login'] ) ) {
            $errors->add(
                'invalid_username',
                __( 'Field <strong>Username</strong> is invalid. It uses illegal characters.' )
            );
        } else if ( username_exists( $user['user_login'] ) ) {
            $errors->add(
                'username_exists',
                __( 'The selected <strong>Username</strong> is already in use, please choose another one.' )
            );
        }

        if ( !is_email( $user['user_email'] ) ) {
            $errors->add(
                'invalid_email',
                __( 'Field <strong>Email</strong> is invalid.' )
            );
        } else if ( email_exists( $user['user_email'] ) ) {
            $errors->add(
                'email_exists',
                __( 'The selected <strong>Email</strong> is already in use, please choose another one.' )
            );
        }

        return $errors;
    }
}