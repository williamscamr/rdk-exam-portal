<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));

$token_hash = hash("sha256", $token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);

$sql = "UPDATE users
		SET reset_token_hash = ?,
			reset_token_expires_at = ?
		WHERE email = ?";
		
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("sss", $token_hash, $expiry, $email);

$stmt->execute();

if ($mysqli->affected_rows) {
	
	$mail = require __DIR__ . "/mailer.php";
	
	$mail->setFrom("rdkexamportal@gmail.com");
	$mail->addAddress($email);
	$mail->Subject = "Password Reset";
	$mail->Body = <<< END
	
	Click <a href="localhost/bb_website_test/reset-password.php?token=$token">here</a>
	to reset your password.
	
	END;
	
	try {
		
		$mail->send();
		
	} catch (Exception $e) {
		
		echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
		
	}
	
	
	
}

echo "An email has been sent, please check your inbox.";