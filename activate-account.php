<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);

$sql = "SELECT * FROM users
		WHERE account_activation_hash = ?";
		
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
	die("token not found");
}

/*if (strtotime($user["reset_token_expires_at"]) <= time()) {
	die("token has expired");
}*/

$sql = "UPDATE users
		SET account_activation_hash = NULL
		WHERE id = ?";
		
$stmt = $mysqli->prepare($sql);

$stmt->bind_param("s", $user["id"]);

$stmt->execute();

?>

<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <title>Account Activated</title>
        <meta charset="UTF-8">
        <body>
	</head>
	
	<h1>Account Activated</h1>
	
	<p>Account activated successfully. You can now <a href="login.php">log in</a>.</p>
	
</body>
</html>