<?php

session_start();

if (isset($_SESSION["user_id"])) {
	
	require __DIR__ . "/vendor/autoload.php";

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);
	
	$sql = "SELECT * FROM users
			WHERE id = {$_SESSION["user_id"]}";
			
	$result = $mysqli->query($sql);
	
	$user = $result->fetch_assoc();
	
	$highestSql =  "SELECT Highest_Score FROM Quiz1_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz2_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz3_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz4_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz5_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz6_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz7_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz8_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz9_Score WHERE ID = {$user["id"]}
					UNION ALL
					SELECT Highest_Score FROM Quiz10_Score WHERE ID = {$user["id"]}";
	$highestResult = $mysqli->query($highestSql);
		$values = [];
	if ($result) {
		
		while ($row = $highestResult->fetch_assoc()) {
			if ($row["Highest_Score"] != "") {
				$values[] = ($row["Highest_Score"]) / 5;
			}
			else if ($row["Highest_Score"] == "") {
				$values[] = "N/A";
			}
			//$values[] = ($row["Highest_Score"]) / 5; // change this 5 to the amount of total questions
		}
		$highestResult->free();
	}
	/*else {
		echo "error" . $mysqli->error;
	}*/
}

?>
<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <title>Home</title>
        <meta charset="UTF-8">
	</head>
	<body>
		<h1>Home</h1>
		
		<?php if (isset($user)): ?>
			
			<p>Hello <?= htmlspecialchars($user["first_name"]) ?>. You are logged in.</p>
			<?php if (isset($_GET["e"])): ?>
				<p>You may take only 1 quiz within a 24 hour period. Please try again later.</p>
			<?php endif; ?>
			
			<p>Your current quiz results:</p>
			<?php echo "<ul>";
			foreach ($values as $item) {
				$i = 1;
				$item = $item * 100;
				echo "<li>Quiz $i - $item%</li>";
				$i++;
			}
			echo "</ul>"
			?>
			
			<p><a href="quiz.php?q=1">Quiz 1</a></p>
			<p><a href="quiz.php?q=2">Quiz 2</a></p>
			<p><a href="quiz.php?q=3">Quiz 3</a></p>
			<p><a href="quiz.php?q=4">Quiz 4</a></p>
			<p><a href="quiz.php?q=5">Quiz 5</a></p>
			<p><a href="quiz.php?q=6">Quiz 6</a></p>
			<p><a href="quiz.php?q=7">Quiz 7</a></p>
			<p><a href="quiz.php?q=8">Quiz 8</a></p>
			<p><a href="quiz.php?q=9">Quiz 9</a></p>
			<p><a href="quiz.php?q=10">Quiz 10</a></p>
			<br></br>
			
			<p><a href="logout.php">Log out</a></p>
		
		<?php else: ?>
			<p><a href="login.php">Log in</a> or <a href="signup.html">Sign up</a></p>
		
		<?php endif; ?>
		
	</body>
</html>