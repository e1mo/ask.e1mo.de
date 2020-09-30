<?php

$default_config = require('config.default.php');
$config = require('config.php');
$config = array_merge($default_config, $config);

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
        ></textarea>
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
            <div class="item full-width">
                <label for="recipient">Recipient: </label><br>
                <div class="full-width">
                    <select name="recipient" id="recipient">
                        <option value="hello@e1mo.de">hello</option>
                    </select>
                    @e1mo.de
                </div>
            </div>
        </div>
        <br>
        <input type="submit" value="<?php
            echo $config['form']['submit']['label'];
        ?>">
    </form>
</body>
</html>