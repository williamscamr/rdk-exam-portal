<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	
	require __DIR__ . "/vendor/autoload.php";

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);
	
	$sql = sprintf("SELECT * FROM users
					WHERE username = '%s'", 
					$mysqli->real_escape_string($_POST["username"]));
	
	$result = $mysqli->query($sql);
	
	$user = $result->fetch_assoc();	
	
	if ($user && $user["account_activation_hash"] === null) {
		if (password_verify($_POST["password"], $user["password_hash"])) {
			
			session_start();
			
			session_regenerate_id();
			
			$_SESSION["user_id"] = $user["id"];
			
			header("Location: index.php");
			exit;
			
		}
	}
	
	$is_invalid = true;
	
}

?>
<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <title>Login</title>
        <meta charset="UTF-8">
        <body>
	</head>
		<h1>Login</h1>
		
		<?php if ($is_invalid): ?>
			<em>Invalid login</em>
		<?php endif; ?>
		
		<form method = "post">
			<label for="username">username</label>
			<input type="text" name = "username" id = "username"
					value = "<?= htmlspecialchars($_POST["username"] ?? "")?>">
		
			<label for="email">email</label>
			<input type="email" name = "email" id = "email"
					value = "<?= htmlspecialchars($_POST["email"] ?? "") ?>">
			
			<label for="password">password</label>
			<input type="password" name = "password" id = "password">
			
			<button>Log in</button>
		</form>
		
		<a href="forgot-password.php">Forgot password?</a>
		
	</body>
</html>