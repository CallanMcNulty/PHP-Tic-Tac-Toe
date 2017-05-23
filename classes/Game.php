<?php
  require("./classes/DB.php");
  require("./classes/Player.php");
  class Game {
    public $name;
    public $turn;
    public $gameState;
    public $players_key;

    public $players;
    public static $exclude_from_db = array("players");
    public static $constructor_properties = array("name", "turn", "gameState");

    function __construct($gameName, $currentTurn, $state) {
      $this->name = $gameName;
      $this->turn = $currentTurn;
      $this->gameState = $state;
      $this->players = array();
    }

    static function createGame($gameName) {
      $db = new DB("localhost", "root", "root", "test");
      $game = new Game($gameName, 0, "#########");
      $db->saveObject($game, "game", "name");
      return $game;
    }

    function addPlayer($playerName, $position) {
      $db = new DB("localhost", "root", "root", "test");
      $player = new Player($playerName, $this->random_str(12), $position);
      Player::makeTable();
      $id = $player->setId();
      $player->game_key = $this->name;
      $player->game = $this;
      $db->saveObject($player);
      array_push($this->players, $player);
      $this->players_key = $this->players_key.$id.".";
      $db->saveObject($this, "game", "name");
      return $player;
    }

    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
      $str = '';
      $max = mb_strlen($keyspace, '8bit') - 1;
      for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
      }
      return $str;
    }

    function makeMove($player, $boardIndex) {
      if($this->turn % 2 !== $player->positionInTurnOrder) {
        return "Not your turn";
      }
      if($this->gameState[$boardIndex] !== "#") {
        return "Not a valid move";
      }
      $token = $player->positionInTurnOrder===0 ? "X" : "O";
      $this->gameState[$boardIndex] = $token;
      $this->turn ++;
      $db = new DB("localhost", "root", "root", "test");
      $db->saveObject($this, "game", "name");
      if($this->checkForVictory($token)) {
        return true;
      }
      return false;
    }

    function checkForVictory($token) {
      $state = $this->gameState;

      //check horiz
      for ($i=0; $i <= 6; $i+=3) {
        if($state[$i]===$token && $state[$i+1]===$token && $state[$i+2]===$token) {
          return true;
        }
      }
      //check vert
      for ($i=0; $i <= 2; $i++) {
        if($state[$i]===$token && $state[$i+3]===$token && $state[$i+6]===$token) {
          return true;
        }
      }
      //check diagonals
      if($state[0]===$token && $state[4]===$token && $state[8]===$token) {
        return true;
      }
      if($state[2]===$token && $state[4]===$token && $state[6]===$token) {
        return true;
      }
      return false;
    }

    function drawBoard() {
      for ($i=0; $i <= 6; $i+=3) {
        echo substr($this->gameState, $i, 3);
      }
    }

    function endGame() {
      $db = new DB("localhost", "root", "root", "test");
      $db->destroy("game", $this->name, "name");
      $db->destroy("player", $this->name, "game_key");
    }

  }
 ?>
