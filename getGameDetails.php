<?php
  require("./classes/Game.php");

  $playerId = $_GET["player"];
  $playerPass = $_GET["code"];
  $gameName = $_GET["game"];

  $db = new DB("localhost", "root", "root", "test");

  $g = $db->loadObject($gameName, "Game", "game", "name");

  $p = $db->loadObject($playerId, "Player");

  echo '{"success":';
  if($p->game_key !== $g->name || $p->password !== $playerPass) {
    echo 'false, "error":';
    echo '"Invalid Request"';
  } else {
    echo 'true, "turn":'.$g->turn.',';
    echo '"state":"'.$g->gameState.'",';
    $XWins = $g->checkForVictory('X');
    $OWins = $g->checkForVictory('O');
    $t = '#';
    if($XWins) {
      $t = 'X';
    } elseif($OWins) {
      $t = 'O';
    }
    echo '"victor":'.'"'.$t.'"';
  }
  echo "}";
 ?>
