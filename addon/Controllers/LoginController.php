<?php

namespace Amostajo\Wordpress\LoginPageAddon\Controllers;

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
 * @version 2.0.2
 */
class LoginController extends Controller
{
    /**
     * Displays login page.
     * @since 1.0
     */
    public function login()
    {
        $action = Request::input( 'action', 'login' );

        nocache_headers();

        do_action( 'login_init' );

        switch ( $action ) {
            case 'logout':
                return $this->do_logout();
            case 'login':
                return $this->do_login();
        }
    }

    /**
     * Performs ajax login.
     * @since 1.0
     * @since 2.0.0 Added actions.
     * @since 2.0.2 Added localization.
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
                throw new Exception( __( 'Invalid request method.' ) );
            }

            // Token validation
            if ( $input[ 'token' ] != LoginPage::get_token() ) {
                throw new Exception( __( 'Invalid security token.' ) );
            }

            $this->authenticate(
                $input[ 'user_login' ],
                $input[ 'user_password' ],
                $input[ 'remember' ]
            );

            do_action( 'addon_loginpage_after_login' );

            echo json_encode( [
                'continue_loading'  => true,
                'redirect_to'       => LoginPage::get_redirect_to(),
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
     * Filters lost password url.
     * @since 1.0
     *
     * @param string $lostpassword_url Current lost password URL.
     * @param string $redirect         Redirect to.
     *
     * @return string
     */
    public function lost_password_url( $lostpassword_url, $redirect )
    {
        return home_url(
            '/wp-login.php?action=lostpassword&redirect_to=' . LoginPage::get_redirect_to()
        );
    }

    /**
     * Performs logout operation.
     * @since 1.0
     */
    protected function do_logout()
    {
        wp_clear_auth_cookie();
        do_action( 'wp_logout' );
        wp_safe_redirect( home_url( '/' ) );
    }

    /**
     * Performs login operation.
     * @since 1.0
     */
    protected function do_login()
    {
        // Redirect to.
        $redirect_to = LoginPage::get_redirect_to();
        // Cookie authentication
        if ( $this->cookie_authenticate() ) {
            wp_safe_redirect( LoginPage::get_redirect_to() );
        } else {
            wp_enqueue_script( 'addon-loginpage' );
            return $this->view->get( 'addons.loginpage.login', [
                'token'         => LoginPage::generate_token(),
                'action'        => admin_url( 'admin-ajax.php?action=addon_login' ),
                'redirect_to'   => Request::input( 'redirect_to' ),
            ] );
        }
    }

    /**
     * Performs authentication operation.
     * @since 1.0
     *
     * @param string $user_login   User login name or email.
     * @param string $user_pass    User password.
     * @param bool   $remember     Flag that indicates if session should be remembered.
     */
    protected function authenticate($user_login, $user_pass, $remember = false)
    {
        if ( empty( $user_login ) )
            throw new Exception( __( 'Username cannot be empty.' ), 1001 );

        if ( empty( $user_pass ) )
            throw new Exception( __( 'Password cannot be empty.' ), 1002 );

        // Login
        $user = wp_signon(
            [
                'user_login'    => $user_login,
                'user_password' => $user_pass,
                'remember'      => $remember,
            ],
            get_option( 'addon_loginpage_securecookie', false )
        );

        if ( is_wp_error( $user ) ) {
            throw new Exception( $user->get_error_message(), 1003 );
        }
    }

    /**
     * Performs authentication using cookies.
     * Returns flag indicating if oparetion was successful.
     * @since 1.0
     *
     * @return bool
     */
    protected function cookie_authenticate()
    {
        if ( !empty( $_COOKIE[ USER_COOKIE ] ) 
            && !empty( $_COOKIE[ PASS_COOKIE ] )
        ) {
            try {
                $this->authenticate(
                    $_COOKIE[ USER_COOKIE ],
                    $_COOKIE[ PASS_COOKIE ],
                    true
                );
                return true;
            } catch ( Exception $e ) {
                // TODO
            }
        }
        return false;
    }
}