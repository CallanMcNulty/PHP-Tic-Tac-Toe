<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Tic-Tac-Toe</title>
    <link rel="stylesheet" href="/styles.css">
  </head>
  <body>
    <div style="font-weight:bold; font-size:40px; color:gray; text-align:center; padding:50px;">Goodbye.</div>
  </body>
</html>
<?php
  require("./classes/Game.php");

  $playerId = $_GET["player"];
  $playerPass = $_GET["code"];
  $gameName = $_GET["game"];

  $db = new DB("localhost", "root", "root", "test");
  $g = $db->loadObject($gameName, "Game", "game", "name");
  $g->endGame();
?>
