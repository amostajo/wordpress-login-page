<?php

namespace Amostajo\Wordpress\LoginPageAddon;

use WPMVC\Addon;
use WPMVC\Request;

/**
 * Login Page add-on.
 * For Wordpress Plugin or Wordpress Theme templates.
 *
 * @author Alejandro Mostajo
 * @license MIT
 * @package Amostajo\Wordpress\LoginPageAddon
 * @version 2.1.1
 */
class LoginPage extends Addon
{
    /**
     * WP Add-on Login Page Token
     * @since 1.0
     * @var string
     */
    const SECURITY_TOKEN = 'wp_aolpt';

    /**
     * Init Wordpress HOOKS
     * @since 1.0
     */
    public function init()
    {
        add_action( 'init', [ &$this, 'enable_login' ] );
        add_filter( 'lostpassword_url', [ &$this, 'lost_password_url' ], 10, 2 );
        add_action( 'wp_ajax_nopriv_addon_login', [ &$this, 'ajax_login' ] );
        add_action( 'wp_ajax_nopriv_addon_signup', [ &$this, 'ajax_signup' ] );
        add_action( 'wp_ajax_nopriv_addon_lostpassword', [ &$this, 'ajax_lostpassword' ] );
        add_action( 'wp_ajax_nopriv_addon_resetpassword', [ &$this, 'ajax_resetpassword' ] );
    }

    /**
     * Enables custom login.
     * @since 1.0
     * @since 2.0.1 Dependency script added.
     * @since 2.0.2 Bug fixes.
     * @since 2.0.4 Fixed assets_url call.
     * @since 2.0.5 Load assets with relative path on vendor.
     * @since 2.1.0 Uses addon_assets_url()
     * @since 2.1.1 Only load component if vue is disabled.
     */
    public function enable_login()
    {
        // Check on additional unwanted runs.
        if ( ( defined('DOING_AJAX') && DOING_AJAX )
            || Request::input( 'wc-ajax' )
            || Request::input( 'error' )
        )
            return;
        // Register script
        $vue = false;
        if ( $this->main->config->get( 'autoenqueue.addons.vue' ) === true
            || $this->main->config->get( 'autoenqueue.addons.vue' ) === null
        ) {
            wp_register_script(
                'addon-loginpage-vendor',
                addon_assets_url( 'js/vendor.min.js' , __FILE__ ),
                [ 'jquery' ],
                '2.1.0',
                true
            );
            $vue = true;
        }
        wp_register_script(
            'addon-loginpage',
            addon_assets_url( $vue ? 'js/app.min.js' : 'js/component.min.js', __FILE__ ),
            $vue
                ? [ 'addon-loginpage-vendor' ]
                : $this->main->config->get( 'autoenqueue.app-key' )
                    ? [ 'jquery', $this->main->config->get( 'autoenqueue.app-key' ) ]
                    : [ 'jquery' ],
            '2.1.0',
            true
        );
        // Enable pages
        global $pagenow;
        switch ( $pagenow ) {
            case 'wp-register.php':
                $this->mvc->call( 'SignupController@register' );
                die;
            case 'wp-login.php':
                switch ( Request::input( 'action' ) ) {
                    case 'register':
                        $this->mvc->call( 'SignupController@register' );
                        die;
                    case 'lostpassword':
                        $this->mvc->call( 'PasswordController@lost' );
                        die;
                    case 'rp':
                        $this->mvc->call( 'PasswordController@reset' );
                        die;
                }
                $this->mvc->call( 'LoginController@login' );
                die;
        }
    }

    /**
     * Performs ajax login.
     * @since 1.0
     */
    public function ajax_login()
    {
        $this->mvc->call( 'LoginController@ajax' );
    }

    /**
     * Performs ajax signup.
     * @since 1.0
     */
    public function ajax_signup()
    {
        $this->mvc->call( 'SignupController@ajax' );
    }

    /**
     * Performs ajax for lost password.
     * @since 1.0
     */
    public function ajax_lostpassword()
    {
        $this->mvc->call( 'PasswordController@ajax' );
    }

    /**
     * Performs ajax for lost password.
     * @since 1.0
     */
    public function ajax_resetpassword()
    {
        $this->mvc->call( 'PasswordController@ajax_reset' );
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
        return $this->mvc->action( 'LoginController@lost_password_url', $lostpassword_url, $redirect );
    }

    /**
     * Returns redirect to url.
     * @since 1.0
     *
     * @return string
     */
    public static function get_redirect_to()
    {
        // At query string
        $redirect_to = Request::input( 'redirect_to', home_url( '/' ) );
        // At SESSION
        if ( isset( $_SESSION[ 'redirect_me_back' ] ) )
            $redirect_to = $_SESSION[ 'redirect_me_back' ];
        // Apply filters
        return apply_filters(
            'addon_loginpage_redirect_to',
            $redirect_to
        );
    }

    /**
     * Generates new security token.
     * @since 1.0
     *
     * @return string
     */
    public static function generate_token()
    {
        $token = uniqid();
        if( !session_id() ) {
            session_start();
        }
        $_SESSION[ self::SECURITY_TOKEN ] = $token;
        return $token;
    }

    /**
     * Returns session token.
     * @since 1.0
     *
     * @return string
     */
    public static function get_token()
    {
        if( !session_id() ) {
            session_start();
        }
        return array_key_exists( self::SECURITY_TOKEN, $_SESSION )
            ? $_SESSION[ self::SECURITY_TOKEN ]
            : uniqid();
    }
}