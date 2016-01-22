<?php

require_once 'swiftmailer/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

// Create the Transport
echo 'line6';
$transport = Swift_SmtpTransport::newInstance('mail.privateemail.com', 465, "ssl")
	->setUsername('admin@curlcsc.com')
	->setPassword('')
;

/*
You could alternatively use a different transport such as Sendmail or Mail:

// Sendmail
$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

// Mail
$transport = Swift_MailTransport::newInstance();
*/

// Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);
echo '23';

// Create a message
$message = Swift_Message::newInstance('Wonderful Subject');
$message ->setFrom(array('noreply@curlcsc.com' => 'John Doe'))
	->setTo(array('admin@curlcsc.com' => 'A name'))
	->setBody('Here is the message itself');
echo '32';
// Send the message
$result = $mailer->send($message);