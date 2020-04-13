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

	$username = $_POST["username"];
	
	$sql = "SELECT current_game FROM users WHERE username='".$username."'";
	$result = $conn->query($sql) or die($conn->error);

	$row = $result->fetch_row();
	echo $row[0];
?>