<?php
  class Player {
    public $id;
    public $name;
    public $password;
    public $positionInTurnOrder;
    public $game_key;

    public $game;
    public static $exclude_from_db = array("game");
    public static $constructor_properties = array("name", "password", "positionInTurnOrder");

    function __construct($Name, $Password, $PositionInTurnOrder) {
      $this->name = $Name;
      $this->password = $Password;
      $this->positionInTurnOrder = $PositionInTurnOrder;
    }

    function makeTable($tableName="player") {
      $conn = new mysqli("localhost", "root", "root", "test");
      $conn->query("CREATE TABLE ".$tableName." (id INT, name VARCHAR(255), password VARCHAR(255), positionInTurnOrder INT, game_key VARCHAR(255))");
      $conn->close();
    }

    function setId() {
      $conn = new mysqli("localhost", "root", "root", "test");
      $result = $conn->query("SELECT id FROM player");
      $max = -1;
      if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $max = max($max, $row["id"]);
        }
      }
      $conn->close();
      $this->id = $max + 1;
      return $this->id;
    }

  }
 ?>
