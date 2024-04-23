<?php

//$config = parse_ini_file(__DIR__ . "/config.ini", true);

require __DIR__ . "/vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mysqli = new mysqli(hostname: $_ENV["DATABASE_HOSTNAME"],
					 username: $_ENV["DATABASE_USERNAME"],
					 password: $_ENV["DATABASE_PASSWORD"],
					 database: $_ENV["DATABASE_NAME"]);


/*$host = "localhost";
$dbname = "your_database_name";
$username = "root";
$password = "password";

$mysqli = new mysqli(	hostname: $host, 
						username: $username, 
						password: $password, 
						database: $dbname);*/

if ($mysqli->connect_errno) {
	die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;
