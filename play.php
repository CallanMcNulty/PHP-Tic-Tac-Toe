<?php
  require("./classes/Game.php");

  $playerId = $_GET["player"];
  $playerPass = $_GET["code"];
  $gameName = $_GET["game"];
  $move = $_GET["move"];

  $db = new DB("localhost", "root", "root", "test");

  $g = $db->loadObject($gameName, "Game", "game", "name");

  $p = $db->loadObject($playerId, "Player");

  echo '{"success":';
  if($p->game_key !== $g->name || $p->password !== $playerPass) {
    echo 'false, "error":';
    echo '"Invalid Request"';
  } else {
    $finished = true;
    $finished = $g->makeMove($p, intval($move));
    if(gettype($finished) == "boolean") {
      echo 'true, ';
      echo '"board":"';
      $g->drawBoard();
      echo '", "finished":';
      if($finished) {
        echo 'true';
      } else {
        echo 'false';
      }
    } else {
      echo 'false, "error":"';
      echo $finished;
      echo '"';
    }
  }
  echo '}';
 ?>
