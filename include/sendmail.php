<?php
session_start();
/**
* Filename.......: example.1.php
* Project........: HTML Mime Mail class
* Last Modified..: 15 July 2002
*/

//  error_reporting(E_ALL);
//	print_r($_POST);

$pixel = isset($_POST["pixel"]) ? $_POST["pixel"] : 0;
$checkMySec = md5("pixeldusche".date("ymdhi"));
$sec = isset($_POST["mystring"]) ? $_POST["mystring"] : 0;

$data = json_decode($_POST['data'],true);

$betreff = $data[0];
$betreff = $betreff["value"];

$x = count($data);
$mail_txt = '';

for($i=1; $i<($x-1); $i++) {
	$mail_txt .= '<p><b>'.utf8_decode($data[$i]["name"]).'</b>: '.utf8_decode(nl2br($data[$i]["value"])).'</p>';

}

$root = $_SERVER['DOCUMENT_ROOT'];


if( $sec && ($sec == $checkMySec) ) {

//	$Empfaenger	= array("post@pixel-dusche.de");
	$Empfaenger	= array("info@your-plate.de");

	$kundemail 	= "info@your-plate.de";
	$name 		= 'Yourplate Gmbh';

        include_once('htmlMimeMail.php');

        $mail = new htmlMimeMail();
        $mail->setHtml($mail_txt, strip_tags($mail_txt));
        #$mail->setText($mbody);
		#$mail->addHtmlImage($background, 'background.gif', 'image/gif');
		// $mail->setReturnPath($Empfaenger);
		// $mail->setReturnPath($mailVonKunde);
        // $mail->setReply($mailVonKunde);

		$mail->setFrom('"' .$name .'" <' .$kundemail .'>');
//		if ($bcc) $mail->setBcc( $bcc.' <'.$bcc.'>' );
//		$mail->setReply($em);
		$mail->setSubject($betreff);
		$mail->setHeader('X-Mailer', 'HTML Mime mail class (http://www.phpguru.org)');

		/**
        * Send it using SMTP. If you're using Windows you should *always* use
		* the smtp method of sending, as the mail() function is buggy.
        */
		# $result = $mail->send(array($Empfaenger), 'smtp');

		$result = $mail->send($Empfaenger);

		// These errors are only set if you're using SMTP to send the message
		if (!$result) {
			echo "NOT SENT :(";
			print_r($mail->errors);
		} else {
			// echo $row->antwort;
			echo 'Mail sent';
		}
}
else echo 'Captcha';

?>