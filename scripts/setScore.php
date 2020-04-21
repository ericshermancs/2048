<?php 
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
	$highest_score = $_POST["highest_score"];
	
	$sql = "UPDATE users SET highest_score=".$highest_score." WHERE username='".$username."'";

	if ($conn->query($sql) === TRUE) {
	    echo true;
	} else {
	    echo false;
	}

	$conn->close();
?>