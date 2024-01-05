<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Set up your PHPMailer configuration here
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ay321999@gmail.com';
    $mail->Password = 'ulhn qgwh obdc tobx';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 587;

    $mail->setFrom('ay321999@gmail.com');
    $mail->addAddress('adharidevi321999@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Test Subject';
    $mail->Body = 'Test Body';

    $mail->send();
    echo 'Email successfully sent.';
} catch (Exception $e) {
    echo 'Email sending failed. Error: ', $mail->ErrorInfo;
}

?>
