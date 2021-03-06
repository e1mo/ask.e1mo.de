<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();

$default_config = require('config.default.php');
$config = require('config.php');
$config = array_replace_recursive($default_config, $config);

define('debug', $config['page']['debug']);

if (debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'functions.php';

$message = '';
$mailSuccessCode = true;
$mailResponse = (!empty($_SESSION['message']) ? $_SESSION['message'] : '');

if ($config['owner']['recipient-choices']['enabled']) {
    $recipientChoices = processRecipientChoices($config['owner']['recipient-choices']['choices']);
} else {
    $recipientChoices = [];
}

if (isset($_POST['message']) && empty($_SESSION['message'])) {
    $message .= 'Message on '.date('c') . ":\n\n";
    $message .= $_POST['message'];
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($message, 70, "\r\n");

    $toUser = $config['owner']['e-mail'];

    if (!empty($recipientChoices['addresses'])) {
        if (!empty($_POST['recipient-address'])) {
            $_toUser = $_POST['recipient-address'];
        } elseif (!empty($_POST['recipient-user']) && isset($_POST['recipient-domain'])) {
            $_toUser = $_POST['recipient-user'];
            if (!empty($_POST['recipient-domain'])) {
                $_toUser .= '@' . $_POST['recipient-domain'];
            }
        }
        if (in_array($_toUser, $recipientChoices['addresses'])) {
            $toUser = $_toUser;
        } else {
            echo 'Invalid to Address';
        }
    }

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
            $mailResponse = 'Invalid senders E-Mail Address';
        }
    }

    $mail = new PHPMailer(debug);
    $mail->CharSet = 'UTF-8';
    if (strtolower($config['email']['provider']) === 'smtp') {
        $mail->isSMTP();
        if (debug) {
            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
        }
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
    } else {
        $mail->isSendmail();
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

    foreach ($config['modules']['headers'] as $module) {
        if (debug) {
            printf("Loading headers from module %s\n", $module);
        }
        $addHeaders = require( __DIR__ . '/modules/' . $module . '/headers.php');
        foreach ($addHeaders as $hname => $hval) {
            $hsucess = $mail->addCustomHeader($hname, $hval);
            if (debug) {
                printf("Added header \"%s\" with value \"%s\" (%s)\n", $hname, $hval, ($hsucess ? 'ok' : 'failed'));
            }
        }
    }

    $mailSuccessCode = $mail->send();
    if ($mailSuccessCode) {
        $mailResponse = "Message has been sent";
    } else {
        $mailSuccessCode = false;
        $mailResponse = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
    }

    if ($mailSuccessCode && !debug) {
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
                <?php
                if (!empty($recipientChoices)) {
                echo '
                <div class="item full-width">
                    <label for="recipient">' . $config['form']['recipient']['label'] .'</label><br>
                    <div class="full-width">';
                    if (empty($recipientChoices['domains']) || empty($recipientChoices['users'])) {
                        echo "<select class=\"full-width\" name=\"recipient-address\" id=\"recipient-address\" required>\n";
                        foreach ($recipientChoices['addresses'] as $address) {
                            printf("<option value=\"%1\$s\">%1\$s</option>\n", $address);
                        }
                        echo "</select>";
                    } else {
                        echo "<select name=\"recipient-user\" id=\"recipient-user\" required>\n";
                        foreach ($recipientChoices['users'] as $user) {
                            printf("<option value=\"%1\$s\">%1\$s</option>\n", $user);
                        }
                        echo "</select>
                        <span class=\"at-connector\">@</span>";
                        if (count($recipientChoices['domains']) > 1) {
                            echo "<select name=\"recipient-domain\" id=\"recipient-domain\" required>\n";
                            foreach ($recipientChoices['domains'] as $domain) {
                                    printf("<option value=\"%1\$s\">%1\$s</option>\n", $domain);
                            }
                            echo '</select>';
                        } else {
                            printf('
                                <span>%1$s</span><input type="hidden" value="%1$s" name="recipient-domain">',
                                $recipientChoices['domains'][0]
                            );
                        }
                    }
                    echo '</div>
                </div>
                ';}
                ?>
            </div>
            <br>
            <input type="submit" value="<?php
                echo $config['form']['submit']['label'];
            ?>">
        </form>
        <div>
            Powered by the Open-Source <a href="https://github.com/e1mo/ask.e1mo.de" title="Sour code on GitHub">ask.e1mo.de</a> by <a href="https://github.com/e1mo">e1mo</a>, released under the BSD 3-Clause license.
        </div>
    </div>
</body>
</html>
