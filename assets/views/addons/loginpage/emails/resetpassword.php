<html>
    <head>
        <title>Reset password instructions</title>
    </head>
    <body>

        <h1>Reset password</h1>

        <p>
            Greetings <?php echo $user->display_name ?>,
        </p>

        <p>
            We have received  a <strong>reset password</strong> request.
        </p>

        <p>
            Click this <a href="<?php echo $reset_link ?>">reset link</a> to proceed.
        </p>

        <p>
            <i>
                If you haven't requested this email, please delete this message and contact us.
            </i>
        </p>

    </body>
</html>