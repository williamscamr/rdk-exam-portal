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

?>

<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <title>Reset Password</title>
        <meta charset="UTF-8">
        <body>
	</head>
	
	<h1>Reset Password</h1>
	
	<form method = "post" action = "process-reset-password.php">
	
		<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
		
		<label for="password">New password</label>
		<input type="password" id="password" name="password">
		
		<label for "password_confirmation">Repeat password</label>
		<input type="password" id="password_confirmation"
				name = "password_confirmation">
				
		<button>Send</button>
	</form>
	
</body>
</html>