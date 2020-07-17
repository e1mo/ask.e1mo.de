<?php
#ini_set('display_errors', 1);
#ini_set('display_startup_errors', 1);
#error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Send e1mo an anonymous message">
	<meta name="author" content="e1mo">
	<title>Ask e1mo something</title>
</head>
<body>
<?php
$allowedRcpts = [
	"nerviges",
	"anstrengendes",
	"ueberfluessiges",
	"kaputtmachendes",
	"stoerendes",
	"nicht-kommunizierendes",
	"unrealistische-anforderungen-habendes",
	"ganz-passables",
	"liebenswertes",
	"akzeptables",
	"depressives",
	"valides"];
shuffle($allowedRcpts);
session_start();
$token = hash('sha512', openssl_random_pseudo_bytes(64));

function mlheader($value)
{
    $value = str_replace("\n\n", "\n", $value);
    return trim(str_replace("\n", "\n ;", $value));
}

$isValidAddr = true;
$isValidUser = true;

if (!empty($_POST['message']) && (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']))
{
    $message = 'Message on ' . date('c') . ":\n\n";
    $message .= $_POST['message'];

    $toUser = 'nerviges';
    if (isset($_POST['rcpt']))
    {
        $isValidUser = in_array($_POST['rcpt'], $allowedRcpts);
        $isValidAddr = filter_var($_POST['rcpt'] . '@e1mo.de', FILTER_VALIDATE_EMAIL);
        if (($isValidUser || random_int(1, 3) < 3) && $isValidAddr)
        {
            $toUser = $_POST['rcpt'];
        }
        else
        {
            if (!$isValidUser)
            {
                echo 'Nice try, trying to send it to ' . $_POST['rcpt'] . '@e1mo.de but naye... I\'m sending it to the normal address' . "\n<br>";
            }
            if (!$isValidAddr)
            {
                echo 'Come on... try at least to provide an valid email address...' . "\n<br>";
            }
        }
    }
    if (!$isValidUser || !$isValidAddr)
    {
        $message .= "\n\nThey tried to send it to the invalid address of \"" . $_POST['rcpt'] . "@e1mo.de\"\n\$isValidUser\t=>\t" . var_export($isValidUser, true) . "\n\$isValidAddr\t=>\t" . var_export($isValidAddr, true);
    }

    $toAddr = $toUser . '@e1mo.de';

    $headers = [];
    $headers['From'] = 'ask@e1mo.de';
    $headers['Mime-Version'] = '1.0';
    $headers['Content-type'] = 'text/plain; charset=UTF-8';

    if (!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
    {
        $headers['From'] = $_POST['email'];
        $headers['Reply-To'] = $_POST['email'];
        #mail($_POST['email'], 'Your message to e1mo', $message, ['From' => 'ask@e1mo.de']);
        
    }

    $mailret = mail($toAddr, 'ask.e1mo.de', $message, $headers);
    if ($mailret)
    {
        printf('Mail has been sent');
    }
    else
    {
        printf("Mail sending failed");
    }
}

$_SESSION['token'] = $token;

?>

<h1>Send anonymous message to e1mo</h1>

<form method="post" action="">
	<textarea rows="30" cols="50" name="message" placeholder="I always wanted to tell you that..." required></textarea><br>
	<input type="hidden" value="<?php echo $token; ?>" name="token" required autofocus>
	<input type="submit" value="send"><br>
	<input type="email" placeholder="person@example.com" id="email" name="email"><br>
	<label for="email">E-Mail if you want an reply. <b>THIS IS OPTIONAL</b></label><br>
	<label for="rcpt">Recipient address: </label>
	<select name="rcpt" id="rcpt">
		<?php
foreach ($allowedRcpts as $item)
{
    if (random_int(1, 4) > 1)
    {
        echo '<option value="' . $item . '">' . $item . '</option>';
    }
}
?>
	</select>
	<label for="rcpt">@e1mo.de</label>
</form>

<a style="display: none;" rel="me" href="https://cuties.social/@e1mo">Mastodon</a>
</body>
</html>

