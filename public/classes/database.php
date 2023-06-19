<?php

class Database {

	private $risorsa;
	private $debug;
	private $global;

	function __construct($db_host="", $db_user="", $db_password="", $db_database="") {

		// se i campi sono inseriti
		if (!$db_host || !$db_user || !$db_password || !$db_database) {
			if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_DATABASE')) {
				exit();
			}
			$db_host = DB_HOST;
			$db_user = DB_USER;
			$db_password = DB_PASSWORD;
			$db_database = DB_DATABASE;
		}

		// connetto al' host
		$this -> risorsa = mysqli_connect($db_host, $db_user, $db_password) or $this -> getErr("Errore di connessione all'host!");

		// seleziono il db
		$handle = mysqli_select_db($this -> risorsa, $db_database) or $this -> getErr("Errore di selezione del database!");

		$this -> Debug(false, false);

	}

	function Debug($debug="true", $global="false") {
		$this -> debug = $debug;
		$this -> global = $global;

	}
		
	function Log($query) {

		return true;

		$session = new Session();
		$userid = $session -> get_userid();
		$userid = 12;

		/*$query = str_replace("UPDATE","Ha aggiornato nella",$query);
		$query = str_replace("INSERT","Ha inserito nella",$query);
		$query = str_replace("jh2s_rkcommerce_","tabella ",$query);
		$query = str_replace("SET","il/i campo/i",$query);
		$query = str_replace("`","",$query);*/


		$log = "INSERT INTO jh2s_logging (userid, created, query) VALUES (".$userid.", '".date("Y-m-d H:i:s",time())."', '". $this -> Escape($query)."')";
		$result = mysqli_query($this -> risorsa, $log) or $this -> getErr($log);

	}

	function Free() {

		if (mysqli_free_result($this -> risorsa)) {

			return true;

		} else {

			return false;

		}
	}

	function lastID() {

		return @mysqli_insert_id($this -> risorsa);

	}

	function Count($result) {

		return @mysqli_num_rows($result);

	}

	function Found($result) {

		if ($this -> Count($result) != 0) {

			return true;

		} else {

			return false;

		}
	}

	private function getErr($errore = "") {

		if($errore) {
			echo $errore;
		}

		echo mysqli_error($this -> risorsa);

	}

	function Query($query) {

		if($this -> debug) {
			echo $query . "<br />";
		}

		// eseguo la query
		$result = mysqli_query($this -> risorsa, $query) or $this -> getErr($query);

		if ($result) {

			if(strstr($query,"INSERT") || strstr($query,"UPDATE")) {
				//$this -> Log($query);
			}

			return $result;
		}

		if(!$this -> global) {
			$this -> Debug(false, false);
		}
	}

	function Result($result) {

		return @mysqli_fetch_array($result, MYSQLI_ASSOC);

	}

	function Single($result, $field) {

		$item = @mysqli_fetch_array($result, MYSQLI_ASSOC);
		return $item[$field];

	}

	function Results($result) {

		$array = array();
		while($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$array[] = $row;
		}

		return count($array) ? $array : false;

	}

	function Object($result) {

		$righe = @mysqli_fetch_object($result);

		return $righe;

	}

	function MultiInsert($table, $arrays)
	{

		$insert = array();
		foreach($arrays as $array) {

			$keys = array_keys($array);
			$values = array_values($array);
			$escapes = array();
			foreach($values as $k=>$v) {
				$escapes[$k] = $this -> Escape($v);
			}
			$insert[] = "('" . implode("', '",$escapes) . "')";
		}

		$sql = "INSERT INTO " . $table . " (`" . implode("`, `", $keys) . "`) VALUES " . implode(",",$insert);

		return($this->Query($sql));

	}

	function Insert($table, $array)
	{
		$keys = array_keys($array);
		$values = array_values($array);
		$escapes = array();
		foreach($values as $k=>$v) {
			$escapes[$k] = $this -> Escape($v);
		}

		$sql = "INSERT IGNORE INTO " . $table . " (`" . implode("`, `", $keys) . "`) VALUES ('" . implode("', '",$escapes) . "')";

		//echo $sql . "<br />";

		return($this->Query($sql));

	}

	function Update($table, $array, $field, $what)
	{

		$set = array();
		$c=0;
		foreach($array as $k=>$v) {
			$set[$c] = '`' . $k.'` = "'.$this->Escape($v).'"';
			$c++;
		}
		$sql = "UPDATE " . $table . " SET " . implode(", ", $set) . " WHERE ".$field." = '".$what."'";

		return($this->Query($sql));

	}

	function Escape($s) {

		$s = mysqli_real_escape_string($this->risorsa, $s);

		return $s;

	}

	function Close()
	{
		mysqli_close($this->risorsa);
	}

}
