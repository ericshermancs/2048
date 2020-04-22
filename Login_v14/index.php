<?php
   session_start();
   
   if(isset($_SESSION['username'])){
      header("location:/2048/index.php");
      die();
   }

   $_SERVER['DOCUMENT_ROOT'];

   $path = $_SERVER['DOCUMENT_ROOT'];
   $path .= "/2048/scripts/config.php";
   include_once($path);
   
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form 
      
      $myusername = filter_var($_POST['username'], FILTER_SANITIZE_STRING);  
      $mypassword = filter_var($_POST['password'], FILTER_SANITIZE_STRING);  
      
      //echo $myusername." ".$mypassword." ".$_POST['username']." ".$_POST['password']."<br>";

      
      if($_POST['login']==' Login '){
         $sql = "SELECT * FROM users WHERE username = '".$myusername."' and password = '".$mypassword."'";
      }
      else if($_POST['login'] == ' Register '){
         $sql = "SELECT * FROM users WHERE username = '".$myusername."'";
      }
      // echo $sql;
      $result = mysqli_query($conn,$sql);
      $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      
      
      $count = mysqli_num_rows($result);
      
      // If result matched $myusername and $mypassword, table row must be 1 row
      if($count == 1){
         if($_POST['login']==' Register '){
            $error = "It appears that username is already taken";
         }
         else if($_POST['login']==' Login '){
            $_SESSION['username'] = $myusername;
            header("location: /2048/index.php");
         }
      }
      else {
         if($_POST['login']==' Login ')
            $error = "Your Login Name or Password is invalid";
         else if($_POST['login']==' Register '){
            $sql = "INSERT INTO users(username,password) VALUES('".$myusername."','".$mypassword."');";
            $result = mysqli_query($conn,$sql);
            $_SESSION['username'] = $myusername;
            header("location: /2048/index.php");
         }
      }
   }

?>




<!DOCTYPE html>
<html lang="en">
<head>
	<title>2x4=8</title>
	<meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="/2048/style/main.css">

<!--===============================================================================================-->
	<link href="/2048/style/modal.css" rel="stylesheet" type="text/css">
</head>
<body>
		<div class="heading">
			<h1 class="title">2x4=8</h1>
		</div>

		<div class="limiter">
			<div class="container-login100">
				<div class="wrap-login100 p-l-85 p-r-85 p-t-55 p-b-55">
					<button id="myBtn">How To Play</button>
					<form class="login100-form validate-form flex-sb flex-w" action="index.php" method="POST">
						<span class="login100-form-title p-b-32">
							2x4=8
						</span>
						<span class="txt1 p-b-11">
							Username
						</span>
						<div class="wrap-input100 validate-input m-b-36" data-validate = "Username is required">
							<input class="input100" type="text" name="username" >
							<span class="focus-input100"></span>
						</div>
						
						<span class="txt1 p-b-11">
							Password
						</span>
						<div class="wrap-input100 validate-input m-b-12" data-validate = "Password is required">
							<span class="btn-show-pass">
								<i class="fa fa-eye"></i>
							</span>
							<input class="input100" type="password" name="password" >
							<span class="focus-input100"></span>
						</div>
						<!--
						<div class="flex-sb-m w-full p-b-48">
							<div class="contact100-form-checkbox">
								<input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me">
								<label class="label-checkbox100" for="ckb1">
									Remember me
								</label>
							</div>

							<div>
								<a href="#" class="txt3">
									Forgot Password?
								</a>
							</div>
						</div>
						-->
						<div class="container-login100-form-btn">
							<button class="login100-form-btn" name="login" value=" Login " style="margin:10px;">
								Login
							</button>
							<button class="login100-form-btn" name="login" value=" Register " style="margin:10px;">
								Register
							</button>
						</div>

					</form>
					<div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
				</div>
			</div>
		</div>
		<div class="highscores-container">
			<h2 class="title">High Scores</h2>
			<div id="highscores-list">
				
			</div>
		</div>
		  <!-- The Modal -->
		<div id="myModal" class="modal">

			<!-- Modal content -->
			<div class="modal-content">
			  <span class="close">&times;</span>
			  <h1>How To Play:</h1>
			  <ul>
			    <li>Use the arrow keys (Up, Down, Left, Right)
			    to move all the blocks in a certain direction</li>
			    <li>A new block will spawn in a random available spot each turn</li>
			    <li>In this game there are 2 types of blocks:</li>
			        <ul>
			            <li>Regular number blocks</li>
			            <li>Special operation blocks (&times;, &div;, and 💣)</li> 
			        </ul>
			    <li>Number blocks with the same number can be merged to form blocks with larger numbers</li>
			    <li>Special operation blocks have the following behaviors:</li>
			    <ul>
			        <li>&times; blocks will merge with each other to form a new larger multiplication block (for example, &times;2 merged with &times;4 will result in a new &times;8 block) and the same holds true for &div; blocks</li>
			        <li>&times; blocks and &div; blocks can reduce each other (for example, &times;4 merged with &div;2 will result in &times;2, and &div;4 merged with &times;2 will result in &div;2 )</li>
			        <li>&times; blocks and &div; blocks with equal numbers will cancel each other out and delete both blocks</li>
			        <li>&div; blocks cannot merge with regular number blocks of the same numerical value or lower (&div;4 cannot merge with 2 or 4)</li>
			        <li>💣 blocks can merge with any block of any type or value and both blocks will be deleted </li>
			    </ul>
			    <li>Blocks can only be merged once per move, use this to your advantage to manipulate unwanted blocks around the grid</li>
			    <li>Your score is equal to the sum of all regular number blocks on the grid</li>
			  </ul>
			  <h2>Tips:</h2>
			  <ul>
			    <li>Merging a &div; block with a regular number will mean that your score will decrease by at least half the original number block's value. Merging with higher numbers will result in a larger hit to your score, so try to merge with the smallest regular block you can, or use the 💣 block to eliminate them</li>
			      <li>Conversely, merging a &times; block with a regular number block will mean your score will increase by at least double the original number block's value. Merger with higher numbers will result in a larger boost to your score. Try to avoid using 💣 to eliminate them</li>
			  </ul>
			</div>

		</div>
		<div id="dropDownSelect1"></div>

		<!--===============================================================================================-->
		<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/animsition/js/animsition.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/bootstrap/js/popper.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/select2/select2.min.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/daterangepicker/moment.min.js"></script>
		<script src="vendor/daterangepicker/daterangepicker.js"></script>
		<!--===============================================================================================-->
		<script src="vendor/countdowntime/countdowntime.js"></script>
		<!--===============================================================================================-->
		<script src="js/main.js"></script>
		<script src="/2048/js/modal.js"></script>
		<script type="text/javascript">
			var http = new XMLHttpRequest();
			var url = '/2048/scripts/getHighScoreList.php';
			http.open('GET', url, true);

			//Send the proper header information along with the request
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

			http.onreadystatechange = function() {//Call a function when the state changes.
			  if(http.readyState == 4 && http.status == 200) {
			      let data = JSON.parse(http.responseText);
			      let menu = document.getElementById('highscores-list');
			      while (menu.firstChild) {
			          menu.removeChild(menu.firstChild);
			      }
			      var table = document.createElement('table');


			      var tr = document.createElement('tr');   

			      var td1 = document.createElement('td');
			      var td2 = document.createElement('td');
			      var td3 = document.createElement('td');

			      var text1 = document.createTextNode("Rank");
			      var text2 = document.createTextNode("Username");
			      var text3 = document.createTextNode("Score");

			      td1.appendChild(text1);
			      td2.appendChild(text2);
			      td3.appendChild(text3);
			      tr.appendChild(td1);
			      tr.appendChild(td2);
			      tr.appendChild(td3);

			      table.appendChild(tr);


			      for (var i = 0; i < data.length; i++){
			          var tr = document.createElement('tr');   

			          var td1 = document.createElement('td');
			          var td2 = document.createElement('td');
			          var td3 = document.createElement('td');

			          var text1 = document.createTextNode(i+1);
			          var text2 = document.createTextNode(data[i]['username']);
			          var text3 = document.createTextNode(data[i]['highest_score']);

			          td1.appendChild(text1);
			          td2.appendChild(text2);
			          td3.appendChild(text3);
			          tr.appendChild(td1);
			          tr.appendChild(td2);
			          tr.appendChild(td3);

			          table.appendChild(tr);
			      }
			      menu.appendChild(table);
			  }
			}
			http.send();
		</script>


</body>
</html>
