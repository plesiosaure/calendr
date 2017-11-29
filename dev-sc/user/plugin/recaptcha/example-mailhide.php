<html><body>
<?
require_once ("recaptchalib.php");

// get a key at http://www.google.com/recaptcha/mailhide/apikey
$mailhide_pubkey  = '6Lds_ssSAAAAAM1BdY-fH_jLLMpdn43v0KFF4NmI';
$mailhide_privkey = '6Lds_ssSAAAAAOTHfo0tkGu4CZiTpPDGT9vDSwNl';

?>

The Mailhide version of example@example.com is
<? echo recaptcha_mailhide_html ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?>. <br>

The url for the email is:
<? echo recaptcha_mailhide_url ($mailhide_pubkey, $mailhide_privkey, "example@example.com"); ?> <br>

</body></html>
