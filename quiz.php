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
	
	$sqlUser = "SELECT * FROM users
			WHERE id = {$_SESSION["user_id"]}";
	$resultUser = $mysqli->query($sqlUser);
	$user = $resultUser->fetch_assoc();
	
	$mostRecentScoreDate = strtotime($user["most_recent_score_date"]);
	$currentTime = time();
	$timeDifferenceHours = ($currentTime - $mostRecentScoreDate) / (60*60);
	if ($timeDifferenceHours >= 24) {
		$quizNo = $mysqli->real_escape_string($_GET["q"]);
		
		$sqlQuizData = "SELECT * FROM quiz{$quizNo}";
		$resultQuizData = $mysqli->query($sqlQuizData);
		$quizData = $resultQuizData->fetch_all(MYSQLI_ASSOC);
		
		//print_r($quizData);
		
		$questionAmt = 5;
		$questionOrder = range(1, $questionAmt);
		shuffle($questionOrder);
		
		//print_r($questionOrder);
		
		$questionsArrStr = [];
		$optionAstr = [];
		$optionBstr = [];
		$optionCstr = [];
		$optionDstr = [];
		$correctStr = [];
		
		for ($x=1; $x <= $questionAmt; $x++) {
			// set random questions
			$randomQuestionIndex = $questionOrder[$x - 1] - 1;
			$questionsArrStr[] = $quizData[$randomQuestionIndex]["Question_Text"];
			
			// set correct answer
			$correctStr[] = $quizData[$randomQuestionIndex]["AnswerD"];
			
			// get random order for each question
			$answerOrder = range(0,3);
			shuffle($answerOrder);
			
			// set answers to options
			$optionAstr[] = $quizData[$randomQuestionIndex]["Answer" . chr(65 + $answerOrder[0])]; // answerA
			$optionBstr[] = $quizData[$randomQuestionIndex]["Answer" . chr(65 + $answerOrder[1])]; // answerB
			$optionCstr[] = $quizData[$randomQuestionIndex]["Answer" . chr(65 + $answerOrder[2])]; // answerC
			$optionDstr[] = $quizData[$randomQuestionIndex]["Answer" . chr(65 + $answerOrder[3])]; // answerD
		}
		$mysqli->close();
	}	else {
		$mysqli->close();
		header("Location: index.php?e=t");
		exit;
	}
	
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
        <title>Quiz <?= $quizNo ?> - RDK</title>
        <meta charset="UTF-8">
	</head>
	<body>
		<h1>Quiz <?= $quizNo ?></h1>
		
		<?php if (isset($user)): ?>
			
			<p>Hello <?= htmlspecialchars($user["first_name"]) ?>. This is Quiz <?= $quizNo ?>.</p>
			<!--<p>Your info is: <?= $str ?>.</p>-->
			<p><a href="index.php">Back to Main Menu</a></p>
			
			<form id="quizForm" method="post" action="process-gradeQuiz.php">
				<!--<div class="question-container"></div>-->
				<input type="hidden" id="quizId" name="quizId" value=<?= $quizNo ?>>
				<input type="hidden" id="correctStr" name = "correctStr[]" value="">
				
				<!-- set up an actual container later. this sucks but i dont care-->
				<div class="question">
					<p><?= $questionsArrStr[0] ?></p>
					<input type="radio" id="q0a" name="q0" value=<?= $optionAstr[0] ?>>
					<label for="q0a"><?= $optionAstr[0] ?></label><br>
					<input type="radio" id="q0b" name="q0" value=<?= $optionBstr[0] ?>>
					<label for="q0b"><?= $optionBstr[0] ?></label><br>
					<input type="radio" id="q0c" name="q0" value=<?= $optionCstr[0] ?>>
					<label for="q0c"><?= $optionCstr[0] ?></label><br>
					<input type="radio" id="q0d" name="q0" value=<?= $optionDstr[0] ?>>
					<label for="q0d"><?= $optionDstr[0] ?></label><br>
				</div>
				
				<div class="question">
					<p><?= $questionsArrStr[1] ?></p>
					<input type="radio" id="q1a" name="q1" value=<?= $optionAstr[1] ?>>
					<label for="q1a"><?= $optionAstr[1] ?></label><br>
					<input type="radio" id="q1b" name="q1" value=<?= $optionBstr[1] ?>>
					<label for="q1b"><?= $optionBstr[1] ?></label><br>
					<input type="radio" id="q1c" name="q1" value=<?= $optionCstr[1] ?>>
					<label for="q1c"><?= $optionCstr[1] ?></label><br>
					<input type="radio" id="q1d" name="q1" value=<?= $optionDstr[1] ?>>
					<label for="q1d"><?= $optionDstr[1] ?></label><br>
				</div>
				
				<div class="question">
					<p><?= $questionsArrStr[2] ?></p>
					<input type="radio" id="q2a" name="q2" value=<?= $optionAstr[2] ?>>
					<label for="q2a"><?= $optionAstr[2] ?></label><br>
					<input type="radio" id="q2b" name="q2" value=<?= $optionBstr[2] ?>>
					<label for="q2b"><?= $optionBstr[2] ?></label><br>
					<input type="radio" id="q2c" name="q2" value=<?= $optionCstr[2] ?>>
					<label for="q2c"><?= $optionCstr[2] ?></label><br>
					<input type="radio" id="q2d" name="q2" value=<?= $optionDstr[2] ?>>
					<label for="q2d"><?= $optionDstr[2] ?></label><br>
				</div>
				
				<div class="question">
					<p><?= $questionsArrStr[3] ?></p>
					<input type="radio" id="q3a" name="q3" value=<?= $optionAstr[3] ?>>
					<label for="q3a"><?= $optionAstr[3] ?></label><br>
					<input type="radio" id="q3b" name="q3" value=<?= $optionBstr[3] ?>>
					<label for="q3b"><?= $optionBstr[3] ?></label><br>
					<input type="radio" id="q3c" name="q3" value=<?= $optionCstr[3] ?>>
					<label for="q3c"><?= $optionCstr[3] ?></label><br>
					<input type="radio" id="q3d" name="q3" value=<?= $optionDstr[3] ?>>
					<label for="q3d"><?= $optionDstr[3] ?></label><br>
				</div>
				
				<div class="question">
					<p><?= $questionsArrStr[4] ?></p>
					<input type="radio" id="q4a" name="q4" value=<?= $optionAstr[4] ?>>
					<label for="q4a"><?= $optionAstr[4] ?></label><br>
					<input type="radio" id="q4b" name="q4" value=<?= $optionBstr[4] ?>>
					<label for="q4b"><?= $optionBstr[4] ?></label><br>
					<input type="radio" id="q4c" name="q4" value=<?= $optionCstr[4] ?>>
					<label for="q4c"><?= $optionCstr[4] ?></label><br>
					<input type="radio" id="q4d" name="q4" value=<?= $optionDstr[4] ?>>
					<label for="q4d"><?= $optionDstr[4] ?></label><br>
				</div>

				<!--<input type="hidden" id="selectedOption" name="selectedOption[]" value="">
				<button type="button" id="prevBtn" onclick="prevQuestion()">Previous</button>
				<button type="button" id="nextBtn" onclick="nextQuestion()">Next</button>-->
				<button type="submit">Submit</button>
			</form>
			
			<script>
				var correctStr = <?= json_encode($correctStr) ?>;
				document.getElementById('correctStr').value = JSON.stringify(correctStr);
			</script>
			<!--<script>
				var currentQuestion = 0;
				var questionsArrStr = <?= json_encode($questionsArrStr) ?>;
				var optionAstr = <?= json_encode($optionAstr) ?>;
				var optionBstr = <?= json_encode($optionBstr) ?>;
				var optionCstr = <?= json_encode($optionCstr) ?>;
				var optionDstr = <?= json_encode($optionDstr) ?>;
				var correctStr = <?= json_encode($correctStr) ?>;
				var selectedOption = [];
				
				for (var i=0; i<questionsArrStr.length; i++) {
					selectedOption.push("");
				}
				
				document.getElementById('correctStr').value = JSON.stringify(correctStr);
				document.getElementById('selectedOption').value = JSON.stringify(selectedOption);
				
				function updateSelectedOption(index, value) {
					selectedOption[index] = value;
				}
				
				function displayQuestion(index) {
					var questionContainer = document.querySelector('.question-container');
					questionContainer.innerHTML = `
						<div class="question">
							<p>${questionsArrStr[index]}</p>
							<input type="radio" id="q${index}a" name="q${index}" value="${optionAstr[index]}" ${optionAstr[index] === selectedOption[index] ? 'checked' : ''} onclick="updateSelectedOption(${index}, '${optionAstr[index]}')">
							<label for="q${index}a">${optionAstr[index]}</label><br>
							<input type="radio" id="q${index}b" name="q${index}" value="${optionBstr[index]}" ${optionBstr[index] === selectedOption[index] ? 'checked' : ''} onclick="updateSelectedOption(${index}, '${optionBstr[index]}')">
							<label for="q${index}b">${optionBstr[index]}</label><br>
							<input type="radio" id="q${index}c" name="q${index}" value="${optionCstr[index]}" ${optionCstr[index] === selectedOption[index] ? 'checked' : ''} onclick="updateSelectedOption(${index}, '${optionCstr[index]}')">
							<label for="q${index}c">${optionCstr[index]}</label><br>
							<input type="radio" id="q${index}d" name="q${index}" value="${optionDstr[index]}" ${optionDstr[index] === selectedOption[index] ? 'checked' : ''} onclick="updateSelectedOption(${index}, '${optionDstr[index]}')">
							<label for="q${index}d">${optionDstr[index]}</label><br>
							<input type="hidden" id="q${index}correct" name="q${index}option" value="${correctStr[index]}">
						</div>
					`;
				}
				
				function nextQuestion() {
					if (currentQuestion < questionsArrStr.length - 1) {
						currentQuestion++;
						displayQuestion(currentQuestion);
					}
				}
				
				function prevQuestion() {
					if (currentQuestion > 0) {
						currentQuestion--;
						displayQuestion(currentQuestion);
					}
				}
				
				// Display the first question when the page loads
				displayQuestion(currentQuestion);
			</script>-->
		
		<?php else: ?>
			<p><a href="index.php">Back to index if this doesn't work</a></p>
		
		<?php endif; ?>
		
	</body>
</html>