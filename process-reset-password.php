<?php

$token = $_POST["token"];

$token_hash = hash("sha256", $token);

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);

$sql = "SELECT * FROM users
		WHERE reset_token_hash = ?";
		
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
	die("token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
	die("token has expired");
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

$sql = "UPDATE users
		SET password_hash = ?,
		reset_token_hash = NULL,
		reset_token_expires_at = NULL
		WHERE id = ?";
		
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("ss", $password_hash, $user["id"]);

$stmt->execute();

echo "Password updated. You can now login. REDIRECT to login";