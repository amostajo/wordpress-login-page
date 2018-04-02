# Wordpress Login Page ADD-ON

[![Latest Stable Version](https://poser.pugx.org/amostajo/wordpress-login-page/v/stable)](https://packagist.org/packages/amostajo/wordpress-login-page)
[![Total Downloads](https://poser.pugx.org/amostajo/wordpress-login-page/downloads)](https://packagist.org/packages/amostajo/wordpress-login-page)
[![License](https://poser.pugx.org/amostajo/wordpress-login-page/license)](https://packagist.org/packages/amostajo/wordpress-login-page)

Add-on package for [Wordpress MVC](http://www.wordpress-mvc.com/).

**Login Page** add-on provides fully customizable ajax login, sign up, and password reset pages for wordpress. Everything out-of-the-box!

- [Installation](#installation)
    - [Configure in Template](#configure-in-template)
- [Usage](#usage)
    - [Customization](#customization)
    - [Hooks](#hooks)
- [Coding Guidelines](#coding-guidelines)
- [Copyright](#copyright)

## Installation

This package requires [Composer](https://getcomposer.org/).

Add it in your `composer.json` file located on your template's root folder:

```json
"amostajo/wordpress-login-page": "2.*.*"
```

Then run

```bash
composer install
```

or

```bash
composer update
```

to download package and dependencies.

### Configure in Template

Add the following string line in your `addons` array option located at your template's config file.

```php
    'Amostajo\Wordpress\LoginPageAddon\LoginPage',
```

This should be added to:
* `app\Config\app.php` on Wordpress MVC.

## Usage

Once installed and configured, this add-on will change your login, signup and reset password pages without you having to do anything.

### Customization

All views (templates) located at the `assets/views` folder can be customized in your theme.

Copy and paste them in your theme's views folder ( with same folder structure), like:

```
[addon-folder]
    /assets
        /views
            /addons
                /loginpage
                    /emails
                        resetpassword.php
                    login.php
                    lostpassword.php
                    resetpassword.php
                    signup.php
```

In your theme:
```
[theme-folder]
    /assets
        /views
            /addons
                /loginpage
                    /emails
                        resetpassword.php
                    login.php
                    lostpassword.php
                    resetpassword.php
                    signup.php
```

You can modify the HTML and add as many CSS classes as you please to fit your theme. Though there are a couple of things to consider:

* Maintain `@submit.prevent`, `v-model`, `v-show`, and `v-for` attributes; otherwise you will lose all processing functionality.
* Maintain `<?php ?>` tags, since they will echo important data in these views.

### Hooks

Custom hooks to use (apart from those standard from Wordpress, like `user_register` and `wp_login` to name a few).

**FILTER**: `addon_loginpage_redirect_to`
Filters redirect to url.

```php
add_filter( 'addon_loginpage_redirect_to', 'filter_redirect' );

function filter_redirect($redirect)
{
    // Modification
    $redirect = home_url( '/my-account.php' );

    // Array is expected in return
    return $redirect;
}
```

**FILTER**: `addon_loginpage_signup_userdata`
Filters the userdata obtained from *sign up form* request.
Useful if you need to add more fields to your signup form.

```php
add_filter( 'addon_loginpage_signup_userdata', 'filter_signup_userdata' );

function filter_signup_userdata($userdata)
{
    // Add additional fields
    $userdata[ 'user_nicename' ] = Request::input( 'user_nicename' );
    $userdata[ 'address' ] = Request::input( 'address' );

    // Array is expected in return
    return $userdata;
}
```

**FILTER**: `registration_errors`
Filters sign up (registrations) errors.
Useful if you need to remove or add validations.

```php
add_filter( 'registration_errors', 'filter_signup_errors' );

function filter_signup_errors($errors, $user_login, $user_email)
{
    // Adding custom validations
    if ( strlen( Request::input( 'user_pass' ) ) >= 8 ) {
        $errors->add(
            'password_length',
            'Field <strong>Password</strong> should contain at least 8 characters.'
        );
    }
    if ( !Request::input( 'address' ) ) {
        $errors->add(
            'empty_address',
            'Field <strong>Address</strong> can not be empty.'
        );
    }

    // WP_Error
    return $errors;
}
```

**FILTER**: `addon_loginpage_signup_message`
Filters message shown to user once registration is completed.

```php
add_filter( 'addon_loginpage_signup_message', 'filter_signup_message' );

function filter_signup_message($message)
{
    return 'Thanks for registering with us!';
}
```

**FILTER**: `retrieve_password_title`
Filters email subject / title sent with reset password instructions.

```php
add_filter( 'retrieve_password_title', 'filter_reset_email_title' );

function filter_reset_email_title($title)
{
    return 'Forgot your password?';
}
```

**FILTER**: `retrieve_password_message`
Filters email message sent with reset password instructions.
**NOTE:** You should better modify the view that comes with the add-on instead of using this filter.

```php
add_filter( 'retrieve_password_message', 'filter_reset_email_message' );

function filter_reset_email_message($message)
{
    return 'Reset password message';
}
```

**FILTER**: `reset_password_errors`
Filters reset password errors.
Useful if you need to remove or add validations.

```php
add_filter( 'registration_errors', 'filter_resetpassword_errors' );

function filter_resetpassword_errors($errors, $input, $user)
{
    // Adding custom validation
    if ( strlen( $input[ 'user_pass' ] ) >= 8 ) {
        $errors->add(
            'password_length',
            'Field <strong>Password</strong> should contain at least 8 characters.'
        );
    }

    // WP_Error
    return $errors;
}
```

**FILTER**: `addon_loginpage_forgotpassword_message`
Filters message shown to user once reset instructions have been send.

```php
add_filter( 'addon_loginpage_forgotpassword_message', 'filter_forgotpassword_message' );

function filter_forgotpassword_message($message)
{
    return 'Reset instructions sent to your inbox.';
}
```

**FILTER**: `addon_loginpage_resetpassword_message`
Filters message shown to user once password has been reset.

```php
add_filter( 'addon_loginpage_resetpassword_message', 'filter_resetpassword_message' );

function filter_resetpassword_message($message)
{
    return 'Password changed!';
}
```


## Coding Guidelines

The coding is a mix between PSR-2 and Wordpress PHP guidelines.

## License

**Page Login ADD-ON** is free software distributed under the terms of the MIT license.
