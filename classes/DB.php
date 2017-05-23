<?php
  class DB {
    public $host;
    public $user;
    public $password;
    public $dbName;
    private static $type_associations = array(3=>"int", 5=>"float", 1=>"boolean", 253=>"string");

    function __construct($Host, $User, $Password, $DbName) {
      $this->host = $Host;
      $this->user = $User;
      $this->password = $Password;
      $this->dbName = $DbName;
    }

    function openConnection() {
      return new mysqli($this->host, $this->user, $this->password, $this->dbName);
    }

    function clearAll() {
      $conn = new mysqli($this->host, $this->user, $this->password);
      $conn->query("DROP DATABASE ".$this->dbName);
      $conn->query("CREATE DATABASE ".$this->dbName);
      $conn->close();
    }

    function getNonExcludedProperties($class) {
      $props = array_keys(get_class_vars($class));
      $reflClass = new ReflectionClass($class);
      if(!in_array("exclude_from_db", $props)) {
        $exclude = array();
      } else {
        $exclude = $reflClass->getStaticPropertyValue("exclude_from_db");
      }
      $nonExcluded = array();
      foreach($props as $prop) {
        if(!in_array($prop, $exclude) && $prop!="exclude_from_db" && $prop!="constructor_properties") {
          array_push($nonExcluded, $prop);
        }
      }
      return $nonExcluded;
    }

    function destroy($table, $value, $uniqueId="id") {
      $conn = $this->openConnection();
      $q = "DELETE FROM ".$table." WHERE ".$uniqueId."='".$value."'";
      $conn->query($q);
      $conn->close();
    }

    function saveObject($obj, $tableName="", $uniqueId="id") {
      // echo "Begin Save<br>";
      $conn = $this->openConnection();
      $class = get_class($obj);

      $toSave = $this->getNonExcludedProperties($class);

      //Query DB
      if($tableName=="") {
        $tableName = strtolower($class);
      }
      $initialQuery = "SELECT * FROM ".$tableName." WHERE ".$uniqueId."='".$obj->$uniqueId."'";
      // echo $initialQuery."<br>";
      $result = $conn->query($initialQuery);

      //If no table matches, create table
      if(!$result) {
        // echo "No Table<br>";
        $q = "CREATE TABLE ".$tableName." (";
        foreach ($toSave as $prop) {
          $type = gettype($obj->$prop);
          if($type=="string") {
            $sqlType = "VARCHAR(255)";
          } elseif($type=="integer") {
            $sqlType = "INT";
          } elseif($type=="double" || $type=="float") {
            $sqlType = "FLOAT";
          } elseif($type=="boolean") {
            $sqlType = "BOOLEAN";
          }
          $q = $q.$prop." ".$sqlType.", ";
        }
        $q = chop($q, ", ").")";
        // echo $q."<br>";
        $conn->query($q);
        $result = $conn->query("SELECT * FROM ".$tableName." WHERE ".$uniqueId."='".$obj->$uniqueId."'");
      }

      //If no row matches, add row
      if($result->num_rows == 0) {
        // echo "No Row<br>";
        $q = "INSERT INTO ".$tableName." (";
        foreach($toSave as $prop) {
          if($obj->$prop!==NULL) {
            $q = $q.$prop.", ";
          }
        }
        $q = chop($q, ", ").") VALUES (";
        foreach($toSave as $prop) {
          if($obj->$prop!==NULL) {
            $q = $q."'".$obj->$prop."', ";
          }
        }
        $q = chop($q, ", ").")";
        // echo $q."<br>";
        $conn->query($q);

      //If a row does match, update it
      } else {
        // echo "Update Row<br>";
        $q = "UPDATE ".$tableName." SET ";
        foreach($toSave as $prop) {
          if($obj->$prop!==NULL) {
            $q = $q.$prop."='".$obj->$prop."', ";
          }
        }
        $q = chop($q, ", ")." WHERE ".$uniqueId."='".$obj->$uniqueId."'";
        // echo $q."<br>";
        $conn->query($q);
      }
      $conn->close();
      return true;
    }


    function loadObject($idVal, $className, $tableName="", $idProperty="id") {
      // echo "Begin Load<br>";
      //Make Query
      $conn = $this->openConnection();
      if($tableName=="") { $tableName = strtolower($className); }
      $q = "SELECT * FROM ".$tableName." WHERE ".$idProperty."='".$idVal."'";
      // echo $q."<br>";
      $result = $conn->query($q);

      //If successful...
      if($result) {
        if($result->num_rows > 0) {
          $row = $result->fetch_assoc();

          //Get type data from database response
          $types = array();
          for ($i=0; $i < $result->field_count; $i++) {
            $field = $result->fetch_field_direct($i);
            $types[$field->name] = $field->type;
          }

          //Check what parameters the contructor requires
          $props = array_keys(get_class_vars($className));
          $reflClass = new ReflectionClass($className);
          if(!in_array("constructor_properties", $props)) {
            $constrProps = array();
          } else {
            $constrProps = $reflClass->getStaticPropertyValue("constructor_properties");
          }

          //Get the values of constructor parameters
          $toLoad = $this->getNonExcludedProperties($className);
          $args = array();
          foreach ($toLoad as $prop) {
            if(in_array($prop, $constrProps)) {
              $val = $row[$prop];
              settype($val, DB::$type_associations[$types[$prop]]);
              array_push($args, $val);
            }
          }

          //Run constructor
          $instance = $reflClass->newInstanceArgs($args);

          //Set property values (in case the contructor doesn't do it)
          foreach ($toLoad as $prop) {
            $val = $row[$prop];
            settype($val, DB::$type_associations[$types[$prop]]);
            $instance->$prop = $val;
          }

          //return result
          return $instance;

          //Otherwise: error
        } else { return false; } //No row found
      } else { return false; } //No table found
      $conn->close();
    }

  }
 ?>
