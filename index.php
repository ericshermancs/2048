<?php
  session_start();
  if(!isset($_SESSION['username'])){
    header("location:login.php");
    die();
  }

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>2x4=8</title>

  <link href="style/main.css" rel="stylesheet" type="text/css">
  <link href="style/modal.css" rel="stylesheet" type="text/css">
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="apple-touch-icon" href="meta/apple-touch-icon.png">
  <link rel="apple-touch-startup-image" href="meta/apple-touch-startup-image-640x1096.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"> <!-- iPhone 5+ -->
  <link rel="apple-touch-startup-image" href="meta/apple-touch-startup-image-640x920.png"  media="(device-width: 320px) and (device-height: 480px) and (-webkit-device-pixel-ratio: 2)"> <!-- iPhone, retina -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">

  <meta name="HandheldFriendly" content="True">
  <meta name="MobileOptimized" content="320">
  <meta name="viewport" content="width=device-width, target-densitydpi=160dpi, initial-scale=1.0, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<script type="text/javascript">
  this.top.location !== this.location && (this.top.location = this.location);
</script>
<body>
  <div class="container">
    <div class="heading">
      <h1 class="title">2x4=8</h1>
      <div class="scores-container">
        <div class="score-container">0</div>
        <div class="best-container">0</div>
        
      </div>
    </div>
    <div>
      Signed in as: <?php echo $_SESSION['username']; ?>
      <a onclick="logout()">Sign out</a>
    </div>
    <button id="myBtn">How To Play</button>
    <div class="above-game">
      <a class="restart-button">New Game</a>
    </div>

    <div class="game-container">
      <div class="game-message">
        <p></p>
        <div class="lower">
	        <a class="keep-playing-button">Keep going</a>
          <a class="retry-button">Try again</a>
        </div>
      </div>

      <div class="grid-container">
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
        <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
          <div class="grid-row">
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
          <div class="grid-cell"></div>
        </div>
      </div>

      <div class="tile-container">

      </div>

    </div>
    <div class="below-game">
		<div class="highscores-container">
			<h2 class="title">High Scores</h2>
			<div id="highscores-list">
				
			</div>
		</div>
	</div>

  </div>

  <!--
  above ends original game
  -->
  <!-- Trigger/Open The Modal -->
  

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
        <li>In this game there are 2 types of blocks:
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


  <script src="js/bind_polyfill.js"></script>
  <script src="js/classlist_polyfill.js"></script>
  <script src="js/animframe_polyfill.js"></script>
  <script src="js/keyboard_input_manager.js"></script>
  <script src="js/html_actuator.js"></script>
  <script src="js/grid.js"></script>
  <script src="js/tile.js"></script>
  <script src="js/local_storage_manager.js"></script>
  <script src="js/game_manager.js"></script>
  <script src="js/modal.js"></script>
  <script type="text/javascript">
    function logout(){
      // remove traces of past user, go logout please
      localStorage.clear();
      window.location.replace("logout.php");
    }
  </script>
  <!-- Wait till the browser is ready to render the game (avoids glitches)-->
  <script type="text/javascript">
    window.requestAnimationFrame(function () {
      new GameManager(5, KeyboardInputManager, HTMLActuator, LocalStorageManager);
    });
  </script>

  <!--<script src="js/application.js"></script> -->
</body>
</html>
