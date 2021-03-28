<?php

class MYSQL {
    
    private static $isEnabled = false;
	
	//singleton
	public static function query($sql) {
		new self();
		$base = mysqli_connect(SQL['DOMAIN'], SQL['USER'], SQL['PASSWORD'], SQL['BDD']);
		
		$base->set_charset("utf8");
		$query = mysqli_query($base, $sql) or die (mysqli_error($base));
		if($query === false) {
			self::error('Dans la requête : <br />'.$sql, mysqli_error());
		}
		
		return $query;
	}
	
	public static function fetchArray($query) { 
		return mysqli_fetch_array($query);
	}
	
	public static function fetchRow($query) {
		return mysqli_fetch_row($query);
	}
	
	public static function fetchAssoc($query) {
		return mysqli_fetch_assoc($query);
	}
	
	public static function freeResult($query) {
		return mysqli_free_result($query);
	}
	
	public static function numRows($query) {
		return mysqli_num_rows($query);
	}
	
	public static function isRows($query) {
		return (self::numRows($query) != 0);
	}
	
	public static function selectOneRow($sql) {
		$query = self::query($sql);
		$row = self::fetchArray($query);
		self::freeResult($query);
		return $row;
	}
	
	public static function selectOneValue($sql, $index = 0) {
		$query = self::query($sql);
		$row = self::fetchRow($query);
		self::freeResult($query);
		return $row[$index];
	}
	
	private static function error($txt, $erreur) {
		trigger_error('Erreur MySQL : '.$txt.'<br />Nom erreur : '.$erreur.'<br />', E_USER_ERROR);
	}

}
?>