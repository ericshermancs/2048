<?php
   session_start();
   
   if(isset($_SESSION['username'])){
      header("location:index.php");
      die();
   }

   include("scripts/config.php");
   
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
            header("location: index.php");
         }
      }
      else {
         if($_POST['login']==' Login ')
            $error = "Your Login Name or Password is invalid";
         else if($_POST['login']==' Register '){
            $sql = "INSERT INTO users(username,password) VALUES('".$myusername."','".$mypassword."');";
            $result = mysqli_query($conn,$sql);
            $_SESSION['username'] = $myusername;
            header("location: index.php");
         }
      }
   }

?>

<body>
<iframe src="./Login_v14/index.html"></iframe>
</body>

<!--
<html>
   
   <head>
      <title>Login Page</title>
      
      <style type = "text/css">
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }
         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }
         .box {
            border:#666666 solid 1px;
         }
      </style>
      
   </head>
   
   <body bgcolor = "#FFFFFF">
	
      <div align = "center">
         <div style = "width:300px; border: solid 1px #333333; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>
				
            <div style = "margin:30px">
               
               <form action = "" method = "post">
                  <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
                  <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
                  <input type = "submit" name="login" value = " Login "/>
                  <input type = "submit" name="login" value = " Register ">
                  <br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
					
            </div>
				
         </div>
			
      </div>

   </body>
</html>

-->
