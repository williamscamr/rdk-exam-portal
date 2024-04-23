<?php
if (empty($_POST["firstname"])) {
	die("Name is required");
}

if (empty($_POST["lastname"])) {
	die("Name is required");
}

if (empty($_POST["username"])) {
	die("Username is required");
}

if (empty($_POST["belt"])) {
	die("Selection is required");
}

if (empty($_POST["studio"])) {
	die("Selection is required");
}

if (! filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
	die("Valid email is required");
}

if (strlen($_POST["password"]) <8) {
	die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password"])) {
	die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/i", $_POST["password"])) {
	die("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["password_confirmation"]) {
	die("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$activation_token = bin2hex(random_bytes(16));

$activation_token_hash = hash("sha256", $activation_token);

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);

$sql = "INSERT INTO users (user_type, username, email, password_hash, first_name,
		last_name, belt, studio, account_activation_hash) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
	die("SQL error: " . $mysqli->error);
}

/*$stmt->bind_param("ssss",
				$_POST["name"],
				$_POST["email"],
				$password_hash, 
				$activation_token_hash);*/

$stmt->bind_param("sssssssss",
				$_POST["user_type"],
				$_POST["username"],
				$_POST["email"],
				$password_hash,
				$_POST["firstname"],
				$_POST["lastname"],
				$_POST["belt"],
				$_POST["studio"],
				$activation_token_hash);				

if ($stmt->execute()) {
	
	$mail = require __DIR__ . "/mailer.php";
	
	$mail->setFrom("rdkexamportal@gmail.com");
	$mail->addAddress($_POST["email"]);
	$mail->Subject = "Account Activation";
	$mail->Body = <<< END
	
	Click <a href="localhost/bb_website_test/activate-account.php?token=$activation_token">
	here</a>
	to activate your account.
	
	END;
	
	try {
		
		$mail->send();
		
	} catch (Exception $e) {
		
		echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
		exit;
	}
	
	header("Location: signup-success.html");
	exit;
}
else {
	if ($mysqli->errno === 1062) {
		die("email already taken");
	}
	else {
		die($mysqli->error. " " . $mysqli->errno);
		//die("unknown error");
	}
}
