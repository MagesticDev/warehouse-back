<?php

class DATA {

	private static $contenuGet = NULL;
	private static $contenuPost = NULL;
	private static $contenuSession = NULL;
	private static $contenuCookie = NULL;
	
	private static function dataPost(){
		return json_decode(file_get_contents("php://input"));
	}
	
	/* singleton */
	private function __construct() {}
	
	// pour lire tout et vérifier les tentatives de hack sans utiliser le reste 
	// de la classe (uniquement pour les anciennes parties du site !!!)
	public static function lireAll() {
		self::lireGet();
		self::lirePost();
		self::lireCookie();
		
	}
	
	private static function lireGet() {
		if(self::$contenuGet === NULL) {
			self::$contenuGet = $_GET;
			if(self::isHackAttemptInData(self::$contenuGet)) {
				UTILS::addHack('Possible attaque dans les données GET', true);
			}
		}
	}
	private static function lirePost() {
		if(self::$contenuPost === NULL) {
			self::$contenuPost = self::dataPost();
			if(!preg_match("#(/?)Forum(./)#i", $_SERVER['REQUEST_URI']) && self::isHackAttemptInData(self::$contenuPost, false)) {
				UTILS::addHack('Possible attaque dans les données POST');
			}
		}
	}
	
	private static function lireSession() {
		if(self::$contenuSession === NULL)
			self::$contenuSession = $_SESSION;
	}
	private static function lireCookie() {
		if(self::$contenuCookie === NULL) {
			self::$contenuCookie = $_COOKIE;
			if(self::isHackAttemptInData(self::$contenuCookie)) {
				UTILS::addHack('Possible attaque dans les cookies', true);
			}
		}
		
	}
	
	public static function isGet($cle) {
		self::lireGet();
		return (isset(self::$contenuGet[$cle]) && !empty(self::$contenuGet[$cle]));	
	}
	public static function isPost($cle) {
		self::lirePost();
		return (isset(self::$contenuPost->$cle) && !empty(self::$contenuPost->$cle));	
    }
	public static function isSession($cle) {
		self::lireSession();
		return (isset(self::$contenuSession[$cle]) && !empty(self::$contenuSession[$cle]));	
	}
	public static function isCookie($cle) {
		self::lireCookie();
		return (isset(self::$contenuCookie[$cle]) && !empty(self::$contenuCookie[$cle]));	
	}
	
	public static function getGet($cle, $traiterHtml = true) {
		self::lireGet();
		if(!isset(self::$contenuGet[$cle])) return '';
		return self::filtrer(self::$contenuGet[$cle], $traiterHtml);
	}
	public static function getPost($cle, $traiterHtml = true) {
		self::lirePost();
		if(!isset(self::$contenuPost->$cle)) return '';
		return self::filtrer(self::$contenuPost->$cle, $traiterHtml);
	}
	public static function getSession($cle) {
		self::lireSession();
		if(!isset(self::$contenuSession[$cle])) return '';
		return self::$contenuSession[$cle];
	}
	public static function getCookie($cle, $traiterHtml = true) {
		self::lireCookie();
		if(!isset(self::$contenuCookie[$cle])) return '';
		return self::filtrer(self::$contenuCookie[$cle], $traiterHtml);
	}
	
	public static function setGet($cle, $valeur) {
		self::$contenuGet[$cle] = $valeur;
	}
	public static function setPost($cle, $valeur) {
		self::$contenuPost[$cle] = $valeur;
	}
	public static function setSession($cle, $valeur) {
		self::$contenuSession[$cle] = $valeur;
		$_SESSION[$cle] = $valeur;
	}
	public static function setCookie($cle, $valeur, $expire = -1) {
		self::$contenuCookie[$cle] = $valeur;
		if($expire == -1) $expire = time() + 31536000;
		setcookie($cle, $valeur, $expire, '/', 'magestic.eu');
	}

	private static function filtrer($txt, $traiterHtml) {
		if($traiterHtml)
			return htmlentities(str_replace("'", "’", $txt));
		else
			return str_replace("'", "’", $txt);
	}
	
	public static function getSerializedPost() {
		return serialize(self::$contenuPost);
	}
	public static function getSerializedGet() {
		return serialize(self::$contenuGet);
	}
	public static function getSerializedCookie() {
		return serialize(self::$contenuCookie);
	}
	
	private static function isHackAttemptInData($data, $checkHtml = true) {
        if($checkHtml){
            foreach($data AS $a => $b) {
                if(($checkHtml && preg_match("#<([^>]+)>#", $b)) || preg_match("#(INSERT |UPDATE |DELETE |SELECT |ALTER |DROP |GRANT |CREATE | OR | AND |INCLUDE|\\0)#i", $b))
                    return true;
            }
        }
		return False;
	}
	
	
}

?>