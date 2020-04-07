<?php 
	$servername = "localhost";
	$username = "root";
	$password = "password";
	$dbname = "2048";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$sql = "SELECT username, highest_score FROM users ORDER BY highest_score DESC limit 10";
	$result = $conn->query($sql);

	$dbdata = array();

    while ( $row = $result->fetch_assoc())  {
		$dbdata[]=$row;
	}

	//Print array in JSON format
	echo json_encode($dbdata);
?>