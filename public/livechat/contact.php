<?php
/* ======================================================================*\
  || #################################################################### ||
  || # Rhino Socket 2.0                                                 # ||
  || # ---------------------------------------------------------------- # ||
  || # Copyright 2014 Rhino All Rights Reserved.                        # ||
  || # This file may not be redistributed in whole or significant part. # ||
  || #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
  || #                  http://www.livesupportrhino.com                 # ||
  || #################################################################### ||
  \*====================================================================== */

// Check if the file is accessed only via index.php if not stop the script from running
if (!defined('LS_PREVENT_ACCESS'))
    die('You cannot access this file directly.');

// Destroy session when user has been sent to contact form.
unset($_SESSION['convid']);
unset($_SESSION['jrc_userid']);
unset($_SESSION['jrc_email']);
unset($_SESSION['chat_wait']);

// Post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_email'])) {
    $defaults = $_POST;

    // Errors in Array
    $errors = array();

    if (empty($defaults['name']) || strlen(trim($defaults['name'])) <= 2) {
        $errors['name'] = $tl['error']['e'];
    }

    if (LS_EMAIL_BLOCK) {
        $blockede = explode(',', LS_EMAIL_BLOCK);
        if (in_array($defaults['email'], $blockede) || in_array(strrchr($defaults['email'], "@"), $blockede)) {
            $errors['email'] = $tl['error']['e10'];
        }
    }

    if ($defaults['email'] == '' || !filter_var($defaults['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = $tl['error']['e1'];
    }

    if (LS_CLIENT_PHONE && !preg_match('^((\+)?(\d{2})[-])?(([\(])?((\d){3,5})([\)])?[-])|(\d{3,5})(\d{5,8}){1}?$^', $defaults['phone'])) {
        $errors['phone'] = $tl['error']['e14'];
    }

    if (empty($defaults['message']) || strlen(trim($defaults['message'])) <= 2) {
        $errors['message'] = $tl['error']['e2'];
    }

    if (LS_CAPTCHA) {

        $human_captcha = explode(':#:', $_SESSION['jrc_captcha']);

        if ($defaults[$human_captcha[0]] == '' || $defaults[$human_captcha[0]] != $human_captcha[1]) {
            $errors['human'] = $tl['error']['e12'];
        }
    }

    if (count($errors) > 0) {

        /* Outputtng the error messages */
        if ($_SERVER['HTTP_X_REQUESTED_WITH']) {

            header('Cache-Control: no-cache');
            die('{"status":0, "errors":' . json_encode($errors) . '}');
        } else {

            $errors = $errors;
        }
    } else {

        $userDetails = ls_get_data($_GET['user'], 'users');

        /*// Get the department for the contact form if set
        if (is_numeric($defaults["department"]) && $defaults["department"] != 0) {

            $op_email = LS_EMAIL;

            foreach ($LV_DEPARTMENTS as $d) {
                if (in_array($defaults["department"], $d)) {
                    if ($d['email'])
                        $op_email = $d['email'];
                }
            }
        }*/
        
        if (isset($userDetails['email']) && !empty($userDetails['email'])) {
            $op_email = $userDetails['email'];
        } else {
            $op_email = LS_EMAIL;
        }

        $listform = $tl["general"]["g27"] . ': ' . $defaults['name'] . '<br />';
        $listform .= $tl["general"]["g47"] . ': ' . $defaults['email'] . '<br />';
        $listform .= $tl["general"]["g50"] . ': ' . $defaults['phone'] . '<br />';
        $listform .= $tl["general"]["g28"] . ': ' . $defaults['message'];

        $mail = new PHPMailer(); // defaults to using php "mail()" or optional SMTP

        if (LS_SMTP_MAIL) {

            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->Host = LS_SMTPHOST;
            $mail->SMTPAuth = (LS_SMTP_AUTH ? true : false); // enable SMTP authentication
            $mail->SMTPSecure = LS_SMTP_PREFIX; // sets the prefix to the server
            $mail->SMTPKeepAlive = (LS_SMTP_ALIVE ? true : false); // SMTP connection will not close after each email sent
            $mail->Port = LS_SMTPPORT; // set the SMTP port for the GMAIL server
            $mail->Username = base64_decode(LS_SMTPUSERNAME); // SMTP account username
            $mail->Password = base64_decode(LS_SMTPPASSWORD); // SMTP account password
            $mail->AddReplyTo($defaults['email'], $defaults['name']);
            $mail->SetFrom($op_email, LS_TITLE);
            $mail->AddAddress($op_email, LS_TITLE);
            // CC? Yes it does, send it to following address
            if (defined(LS_EMAILCC)) {
                $emailarray = explode(',', LS_EMAILCC);

                if (is_array($emailarray))
                    foreach ($emailarray as $ea) {
                        $mail->AddCC(trim($ea));
                    }
            }
        } else {

            $mail->AddReplyTo($defaults['email'], $defaults['name']);
            $mail->SetFrom($op_email, LS_TITLE);
            $mail->AddAddress($op_email, LS_TITLE);
            // CC? Yes it does, send it to following address
            if (defined(LS_EMAILCC)) {
                $emailarray = explode(',', LS_EMAILCC);

                if (is_array($emailarray))
                    foreach ($emailarray as $ea) {
                        $mail->AddCC(trim($ea));
                    }
            }
        }

        $mail->Subject = LS_TITLE;
        $mail->AltBody = $tl['general']['g45'];
        $mail->MsgHTML($listform);

        if ($mail->Send()) {

            unset($_SESSION['jrc_captcha']);
            unset($_SESSION['chatbox_redirected']);

            // Ajax Request
            if ($_SERVER['HTTP_X_REQUESTED_WITH']) {

                header('Cache-Control: no-cache');
                die(json_encode(array('status' => 1, 'html' => $tl["general"]["g65"])));
            } else {

                ls_redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $tl["general"]["g1"]; ?> - <?php echo LS_TITLE; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Live Chat Rhino" />
        <link rel="stylesheet" href="css/stylesheet.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/functions.js"></script>
        <script type="text/javascript" src="js/contact.js"></script>

        <?php if (LS_FONTG_TPL != "NonGoogle") { ?>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=<?php echo LS_FONTG_TPL; ?>:regular,italic,bold,bolditalic" type="text/css" />
        <?php } ?>

        <style type="text/css">
            .navbar-brand { font-family:<?php if (LS_FONTG_TPL != "NonGoogle") echo '"' . str_replace("+", " ", LS_FONTG_TPL) . '", ';
        echo LS_FONT_TPL; ?>; }
        </style>
        <style id="cFontStyles" type="text/css">
            body, code, input[type="text"], textarea { font-family:<?php echo LS_FONT_TPL; ?>; }
        </style>
        <?php if (LS_FHCOLOR_TPL != '#494949') { ?>
            <style type="text/css">
                .navbar-brand { color: <?php echo LS_FHCOLOR_TPL; ?>; }
            </style>
        <?php } if (LS_FCOLOR_TPL != '#494949') { ?>
            <style type="text/css">
                body { color: <?php echo LS_FCOLOR_TPL; ?>; }
            </style>
        <?php } if (LS_FACOLOR_TPL != '#2f942b') { ?>
            <style type="text/css">
                a { color: <?php echo LS_FACOLOR_TPL; ?>; }
            </style>
        <?php } if (LS_ICCOLOR_TPL != '#f9f9f9') { ?>
            <style type="text/css">
                .jrc_chat_form {
                    background-color: <?php echo LS_ICCOLOR_TPL; ?> !important;
                }
            </style>
        <?php } ?>

        <?php if (!LS_LANGDIRECTION) { ?>
            <!-- RTL Support -->
            <style>body { direction: rtl; }</style>
            <!-- End RTL Support -->
        <?php } ?>

        <!--[if lt IE 9]>
        <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <script src="js/respond_ie.js"></script>
         <![endif]-->

        <!-- Le fav and touch icons -->
        <link rel="shortcut icon" href="img/ico/favicon.ico">

    </head>
    <body<?php if (LS_BGCOLOR_TPL) echo ' style="background-color:' . LS_BGCOLOR_TPL . ';"'; ?>>

        <?php if ($_GET['slide'] == 1) {
            include_once('template/slide_up/contact.php');
        } else {
            include_once('template/pop_up/contact.php');
        } ?>

        <script type="text/javascript">
<?php if (LS_CAPTCHA) { ?>
                $(document).ready(function ()
                {
                    $(".ls-ajaxform").append('<input type="hidden" name="<?php echo $random_name; ?>" value="<?php echo $random_value; ?>" />');
                });
<?php } ?>

            ls.main_url = "<?php echo BASE_URL; ?>";
            ls.socket_url = "<?php echo SOCKET_PROTOCOL; ?>";
            ls.lsrequest_uri = "<?php echo LS_PARSE_REQUEST; ?>";
            ls.ls_submit = "<?php echo $tl['general']['g7']; ?>";
            ls.ls_submitwait = "<?php echo $tl['general']['g8']; ?>";
        </script>
    </body>
</html>