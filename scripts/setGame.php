<?php 
	session_start();

	$servername = "localhost";
	$username = "root";
	$password = "password";
	$dbname = "DB2048";


	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$username = $_SESSION["username"];
	$gamestate = $_POST["gamestate"];

	$sql = "UPDATE users SET current_game='".$gamestate."' WHERE username='".$username."'";

	if ($conn->query($sql) === TRUE) {
	    echo true;
	} else {
	    echo false;
	}

	$conn->close();


?>