<?php
  require("./classes/Game.php");
  $g = Game::createGame($_GET["gameName"]);
  $g->addPlayer($_GET["player1"], 0);
  $g->addPlayer($_GET["player2"], 1);
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $g->name; ?></title>
    <link rel="stylesheet" href="/styles.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  </head>
  <body>
    <h1><?php echo $g->name; ?></h1>
    <h3>Players:</h3>
    <div style="width:300px; display:block; margin:auto;">
      <?php foreach($g->players as $player) { ?>
        <h4><?php echo $player->name; ?></h4>
        <ul>
          <li>Player ID: <?php echo $player->id; ?></li>
          <?php $t = $player->positionInTurnOrder===0 ? "X":"O" ?>
          <li>Token: <?php echo $t ?></li>
          <!-- <li>Code: <input type="text" value="<?php //echo $player->password; ?>" readonly></li> -->
          <li>Code: <?php echo $player->password; ?></li>
          <?php $link = "http://".$_SERVER['HTTP_HOST']."/gameboard.html?playerId=".$player->id."&playerName=".$player->name."&token=".$t."&code=".$player->password."&game=".$g->name ?>
          <li>Link: <a href="<?php echo $link; ?>">Play!</a></li>
        </ul>
        <?php } ?>
    </div>
  </body>
</html>
