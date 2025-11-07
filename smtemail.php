<html>
<head>
<title>PHPMailer - SMTP advanced test with authentication</title>
</head>
<body>
1111111111111111
<?php
ini_set('display_errors', '1');
require_once('class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
echo "1222222222222222";
$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  echo "1";
  $mail->Host       = "mail.enguae.com"; // SMTP server
  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->Port       = 587;                    // set the SMTP port for the GMAIL server
  $mail->Username   = "billing@fastuae.com"; // SMTP account username
  $mail->Password   = "B372245!";        // SMTP account password

  $mail->AddReplyTo('billing@fastuae.com', 'Billing Department');

  echo "2";

  $mail->AddAddress('arun@enguae.com', 'Arun Chithran');
  $mail->AddAddress('hashim@enguae.com', 'Hashim One');
  $mail->AddAddress('itmanager@enguae.com', 'Ashraf One');

  $mail->SetFrom('admin@enguae.com', 'First Last');
  // $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML(file_get_contents('contents.html'));
  $mail->AddAttachment('images/phpmailer.gif');      // attachment
  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment

  echo "3";

  $mail->Send();
  echo "Message Sent OK</p>\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>

</body>
</html>
