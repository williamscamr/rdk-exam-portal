<?php
session_start();

if (isset($_SESSION["user_id"])) {
	$score = $_GET["c"];
	$totalQs = $_GET["t"];
	$threshold = $_GET["b"];
	$displayPercent = ($score/$totalQs) * 100;
	$displayThreshold = ($threshold/$totalQs) * 100;
}
else {
	session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
        <title>Reset Password</title>
        <meta charset="UTF-8">
        <body>
	</head>
	
	<h1>Quiz Graded</h1>
	<p>Your quiz has been graded! Here are your results:</p>
	<ul>
		<li>Grade: <?php echo $score; ?> / <?php echo $totalQs;?></li>
		<li>Percentage: <?php echo $displayPercent ?>%</li>
		<?php if ($displayPercent >= $displayThreshold): ?>
			<li>Congratulations! You scored at or above <?php echo $displayThreshold?>. See how close you can get to 100!</li>
		<?php else: ?>
			<li>Good try! You need at least <?php echo $displayThreshold?>% to pass. Keep trying!</li>
		<?php endif; ?>
	</ul>
	<a href="index.php">Back to Main Menu</a>
	
</body>
</html>