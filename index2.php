<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$default_config = require('config.default.php');
$config = require('config.php');
$config = array_replace_recursive($default_config, $config);

if (strtolower($config['email']['provider']) == 'smtp') {
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
}

$message = '';
$mailSuccessCode = true;

if (isset($_POST['message'])) {
    $message .= 'Message on '.date('c') . ":\n\n";
    $message .= htmlentities($_POST['message']);
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $message = wordwrap($message, 70, "\r\n");

    $toUser = $config['owner']['e-mail'];

    switch (strtolower($config['email']['provider'])) {
        case 'smtp':
            try {
                $mail = new PHPMailer(True);
                $mail->isSMTP();
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

                $mail->setFrom($config['message']['sender']);
                $mail->addAddress($toUser);

                $mail->Subject = $config['message']['subject'];
                $mail->Body    = $message;
                $mail->send();
                $mailSuccessCode = true;
                $mailResponse = "Message has been sent";
            } catch (Exception $e) {
                $mailSuccessCode = false;
                $mailResponse = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
            }

            break;

        default:
            $headers['From'] = $config['message']['sender'];
            $headers['Mime-Version'] = '1.0';
            $headers['Content-type'] = 'text/plain; charset=UTF-8';

            $mailSuccessCode = mail($toUser, $config['message']['subject'], $message, $headers);
            if ($mailSuccessCode) {
                $mailResponse = 'Your message was sent successfully.';
            } else {
                $mailResponse = 'There was an error during the sending of the E-Mail.';
            }

            break;
    }
}

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
            if (isset($mailResponse)) {
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