<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$default_config = require('config.default.php');
$config = require('config.php');
$config = array_replace_recursive($default_config, $config);

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = '';
$mailSuccessCode = true;
$mailResponse = (!empty($_SESSION['message']) ? $_SESSION['message'] : '');

if (isset($_POST['message']) && empty($_SESSION['message'])) {
    $message .= 'Message on '.date('c') . ":\n\n";
    $message .= $_POST['message'];
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($message, 70, "\r\n");

    $toUser = $config['owner']['e-mail'];

    $senderName = '';
    $replyTo = '';
    if (!empty($_POST['sender-name'])) {
        $senderName = $_POST['sender-name'];
    }

    $sender = $config['message']['sender'];
    if (!empty($_POST['sender-email'])) {
        if(filter_var($_POST['sender-email'], FILTER_VALIDATE_EMAIL)) {
            if ($config['message']['force-sender']) {
                $replyTo = $_POST['sender-email'];
            } else {
                $sender = $_POST['sender-email'];
            }
        } else {
            $mailSuccessCode = false;
            $mailResponse = 'Invalid senders E-Mail Adress';
        }
    }

    $mail = new PHPMailer(false);
    $mail->CharSet = 'UTF-8';
    if (strtolower($config['email']['provider']) === 'smtp') {
        $mail->isSMTP();
    } else {
        $mail->isSendmail();
        $mail->Host = $config['email']['smtp']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['email']['smtp']['user'];
        $mail->Password = $config['email']['smtp']['pass'];

        if (strtolower($config['email']['smtp']['crypt']) == 'smtps') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port = (int) $config['email']['smtp']['port'];
    }

    if (!empty($senderName)) {
        $mail->setFrom($sender, $senderName);
    } else {
        $mail->setFrom($sender);
    }
    if (!empty($replyTo) && !empty($senderName)) {
        $mail->addReplyTo($replyTo, $senderName);
    } elseif (!empty($replyTo)) {
        $mail->addReplyTo($replyTo);
    }

    if ($config['owner']['allow-bad-recipient']) {
        $mail::$validator = function($address) {
            return true;
        };
    }

    $mail->addAddress($toUser);

    $mail->Subject = $config['message']['subject'];
    $mail->Body    = $message;
    $mailSuccessCode = $mail->send();
    if ($mailSuccessCode) {
        $mailResponse = "Message has been sent";
    } else {
        $mailSuccessCode = false;
        $mailResponse = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }

    if ($mailSuccessCode) {
        // We're doing thhis to avoid sending messages twice when you reload
        // Your browser will prompt you, if you want to re-send the form data you've provided
        // and therefore you would trigger sending it again
        // So on success the user is redirected once to the current script without the POST Form Data
        // The success message is stored and printed out on the next page visit, after the redirect
        $_SESSION['mail-sent'] = true;
        $_SESSION['message'] = $mailResponse;
        header('Location: ' .
            (stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://') .
            $_SERVER['HTTP_HOST'] .
            $_SERVER['REQUEST_URI']);
        exit();
    }
}
session_unset();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['page']['title']; ?></title>
    <meta name="description" content="<?php echo $config['page']['description']; ?>">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="content">
        <?php
            if (!empty($mailResponse)) {
                if ($mailSuccessCode) {
                    $base = '<div class="success">%s</div>';
                } else {
                    $base = '<div class="error">%s</div>';
                }

                printf($base, $mailResponse);
            }
        ?>
        <h1><?php echo $config['page']['title']; ?></h1>
        <p><?php echo $config['page']['description']; ?></p>

        <form action="" method="post">
            <?php
            if (!empty($config['form']['message']['label'])) {
    ?><label for="message"><?php echo $config['form']['message']['label']; ?></label><br>
    <?php
            }
            ?>
            <textarea
                name="message"
                id="message"
                required
                autofocus
                placeholder="<?php
                    echo $config['form']['message']['placeholder'];
                ?>"
            ><?php
                if (!$mailSuccessCode && !empty($_POST['message'])) {
                    echo $_POST['message'];
                }
            ?></textarea>
            <br>
            <p><?php
                echo $config['form']['sender']['label'];
            ?></p>
            <div class="flex-container">
                <div class="item">
            <?php
            if (!empty($config['form']['sender']['name']['label'])) {
    ?><label for="sender-name"><?php echo $config['form']['sender']['name']['label']; ?></label><br>
    <?php
            }
            ?>
            <input
                type="text"
                name="sender-name"
                id="sender-name"
                placeholder="<?php
                    echo $config['form']['sender']['name']['placeholder'];
                ?>"
            >
                </div>
                <div class="item">
            <?php
            if (!empty($config['form']['sender']['email']['label'])) {
    ?><label for="sender-name"><?php echo $config['form']['sender']['email']['label']; ?></label><br>
    <?php
            }
            ?>
            <input
                type="email"
                name="sender-email"
                id="sender-email"
                placeholder="<?php
                    echo $config['form']['sender']['email']['palceholder'];
                ?>"
            >
                </div>
                <!--<div class="item full-width">
                    <label for="recipient">Recipient: </label><br>
                    <div class="full-width">
                        <select name="recipient" id="recipient">
                            <option value="hello@e1mo.de">hello</option>
                        </select>
                        @e1mo.de
                    </div>
                </div>-->
            </div>
            <br>
            <input type="submit" value="<?php
                echo $config['form']['submit']['label'];
            ?>">
        </form>
    </div>
</body>
</html>