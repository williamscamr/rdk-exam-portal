<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = $_ENV["MAIL_HOSTNAME"];
$mail->SMTPSecure = $_ENV["MAIL_SMTPSECURE"];
$mail->Username = $_ENV["MAIL_USERNAME"];
$mail->Password = $_ENV["MAIL_PASSWORD"];
$mail->Port = $_ENV["MAIL_PORT"];

$mail->isHtml(true);

return $mail;