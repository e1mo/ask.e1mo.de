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
    <style>
        form {
            width: 500px;
        }

        textarea {
            width: 100%;
            height: 10em;
        }

        input {
            width: 100%;
            box-sizing: border-box;
        }

        input[type=submit] {
            width: 500px;
        }

        select {
            width: 50%;
        }

        .flex-container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        .flex-container > .item {
            width: 50%;
            padding: .2em;
            box-sizing: border-box;
        }

        .flex-container > .full-width {
            width: 100%;
        }

        @media only screen and (max-width: 600px) {
            body {
                padding-left: 5%;
            }

            form {
                width: 90%;
            }

            textarea {
                width: 100% !important;
                height: 10em;
            }

            .flex-container {
                width: 100%;
            }

            .flex-container > .item {
                width: 100%;
                padding: .2em 0;
            }

            input[type=submit] {
                width: 100%;
            }
        }
    </style>
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
                <label for="recipient">Recipient: </label>
                <select name="recipient" id="recipient">
                    <option value="hello@e1mo.de">hello</option>
                </select>
                @e1mo.de
            </div>
        </div>
        <br>
        <input type="submit" value="<?php
            echo $config['form']['submit']['label'];
        ?>">
    </form>
</body>
</html>