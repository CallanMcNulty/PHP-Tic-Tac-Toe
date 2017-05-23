<?php
  require("./classes/Game.php");
  $db = new DB("localhost", "root", "root", "test");
  $db->clearAll();
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Create Tic-Tac-Toe</title>
    <link rel="stylesheet" href="/styles.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  </head>
  <body>
    <h1>Tic-Tac-Toe</h1>
    <div id="form-div">
      <h3>New Game</h3>
      <form id="form-element" action="newGame.php" style="text-align:center;">
        <input type="text" name="gameName" placeholder="Game Name" class="new-game-input" required pattern="[a-zA-Z0-9]+">
        <br><input type="text" name="player1" placeholder="Player 1 Name" class="new-game-input" required pattern="[a-zA-Z0-9]+">
        <br><input type="text" name="player2" placeholder="Player 2 Name" class="new-game-input" required pattern="[a-zA-Z0-9]+">
        <br><button type="submit">Go</button>
      </form>
    </div>
    <div id="loading">
      Loading...
      <br>
      <svg width="100" height="100">
        <path d="M0,50 a1,1 0 0,0 100,0" fill="lightseagreen" />
        <circle cx="50" cy="50" r="40" fill="white"/>
      </svg>
    </div>
  </body>
  <script type="text/javascript">
    $("#form-element").submit(function() {
      $("#form-div").hide();
      $("#loading").show();
    });
  </script>
</html>
