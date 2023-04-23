
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?php echo base_url('themes/v1/assets/img/favicon-16x16.png'); ?>" sizes="16x16">
    <title><?= $title ?></title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500" rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo base_url('themes/v1/assets/bower_components/uikit/css/uikit.almost-flat.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('themes/v1/assets/bower_components/toastr/toastr.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('themes/v1/assets/css/login_page.min.css'); ?>" />

</head>
<body class="login_page">

    <div class="login_page_wrapper">
        <div class="md-card" id="login_card">
            <div class="md-card-content large-padding" id="login_form">
                <div class="login_heading">
                    <img src="<?php echo base_url('assets/images/300_X_80_Rho_Com_360_.png'); ?>" id="logoimg" alt="<?php $Settings->site_name; ?>">
                </div>
                <?php echo admin_form_open("auth/login", 'class="login" data-toggle="validator"'); ?>
                    <div class="uk-form-row">
                        <label for="login_username">Username</label>
                        <input class="md-input" type="text" name="identity"  />
                    </div>
                    <div class="uk-form-row">
                        <label for="login_password">Password</label>
                        <input class="md-input" type="password" name="password" />
                    </div>
                    <div class="uk-margin-medium-top">
                        <button type="submit" class="md-btn md-btn-primary md-btn-block md-btn-large">Sign In</button>
                    </div>
                    <div class="uk-margin-top">
                        <a href="#" id="login_help_show" class="uk-float-right">Need help?</a>
                        <span class="icheck-inline">
                            <input type="checkbox" name="remember" id="remember" data-md-icheck />
                            <label for="login_page_stay_signed" class="inline-label"><?= lang('remember_me') ?></label>
                        </span>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="md-card-content large-padding uk-position-relative" id="login_help" style="display: none">
                <button type="button" class="uk-position-top-right uk-close uk-margin-right uk-margin-top back_to_login"></button>
                <h2 class="heading_b uk-text-success">Can't log in?</h2>
                <p>Here’s the info to get you back in to your account as quickly as possible.</p>
                <p>First, try the easiest thing: if you remember your password but it isn’t working, make sure that Caps Lock is turned off, and that your username is spelled correctly, and then try again.</p>
                <p>If your password still isn’t working, it’s time to <a href="#" id="password_reset_show">reset your password</a>.</p>
            </div>
            <div class="md-card-content large-padding" id="login_password_reset" style="display: none">
                <button type="button" class="uk-position-top-right uk-close uk-margin-right uk-margin-top back_to_login"></button>
                <h2 class="heading_a uk-margin-large-bottom">Reset password</h2>
                <form>
                    <div class="uk-form-row">
                        <label for="login_email_reset">Your email address</label>
                        <input class="md-input" type="text" id="login_email_reset" name="login_email_reset" />
                    </div>
                    <div class="uk-margin-medium-top">
                        <a href="index.html" class="md-btn md-btn-primary md-btn-block">Reset password</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('themes/v1/assets/js/common.min.js'); ?>"></script>
    <script src="<?php echo base_url('themes/v1/assets/js/uikit_custom.min.js'); ?>"></script>
    <script src="<?php echo base_url('themes/v1/assets/js/altair_admin_common.min.js'); ?>"></script>
    <script src="<?php echo base_url('themes/v1/assets/js/pages/login.min.js'); ?>"></script>
    <!--  notifications functions -->
    <script src="<?php echo base_url('themes/v1/assets/'); ?>bower_components/toastr/toastr.min.js"></script>
    <script>
        // check for theme
        if (typeof(Storage) !== "undefined") {
            var root = document.getElementsByTagName( 'html' )[0],
                theme = localStorage.getItem("altair_theme");
            if(theme == 'app_theme_dark' || root.classList.contains('app_theme_dark')) {
                root.className += ' app_theme_dark';
            }
        }
        <?php
            if ($error) {
        ?>
            toastr.error('<?= $error; ?>')   
        <?php
                }
            if ($message) {
        ?>
            toastr.success('<?= $message; ?>')        
        <?php
            }
        ?>

    </script>
</body>
</html>