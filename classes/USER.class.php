<?php

use \Firebase\JWT\JWT;

class USER {
	
	private static $isAdmin = null;
	
	private static $isReferenceur = null;
	
	private static $isConnecteIG = null;
	
	private static $isConnecte = null;

	private static $ipAddress = null;
	
	//singleton
	private function __construct() {}
	
	//d�marrage
	public static function init() {
		session_start();
		// session_set_cookie_param(time() + 604800);
		$currentCookieParams = session_get_cookie_params();  
		$sidvalue = session_id();  
		setcookie(  
			'PHPSESSID', //name  
			$sidvalue, //value  
			60 * 60 * 24 * 7, //expires at end of session  
			$currentCookieParams['path'], //path  
			$currentCookieParams['domain'], //domain  
			true //secure  
		);
        
        // if(self::isBanni() OR self::ipBanHack()){
        //     $tplBan = new Template;
        //     $tplBan->setFile('Ban', '_Ban.html');
        //     $tplBan->bloc('BAN');
            
        //     switch(true){
        //         case self::isBanni() : // Compte banni
        //             list($true, $banDate, $duree, $reason, $author) = self::isBanni();
        //             $tplBan->bloc('BAN.MESSAGE_IS_BANNI', array(
        //                 'DATE' => $duree,
        //                 'MESSAGE' => $reason,
        //             ));

        //             $tplBan->bloc('BAN.DESCRIPTION_IS_BANNI', array(
        //                 'DATE' => $banDate,
        //                 'AUTEUR' => $author,
        //             ));
        //         break;
        //         case self::ipBanHack() : // adresse ip ban hack automatique
        //             $tplBan->bloc('BAN.MESSAGE_IP_BAN_HACK');
        //             list($true, $ip, $array) = self::ipBanHack();
        //             foreach($array as $keys => $values){
        //                 $tplBan->bloc('BAN.DESCRIPTION_IP_BAN_HACK', array(
        //                     'DESC' => $values[0],
        //                     'DATE' => date('d/m/Y à H:i', $values[1]),
        //                     'PAGE' => $values[2]
        //                 )); 
        //             }
        //         break;
        //     }

        //     if(DATA::isSession('notification')){ 
        //         unset($_SESSION['notification']);
        //     }
            
        //     die(UTILS::compressHtml($tplBan->construire('Ban')));
        // }
    }

    public static function ipBanHack(){
        $queryBanHack = MYSQL::query('SELECT * FROM ip_ban_hack WHERE ip=\''.UTILS::getIp().'\' AND tentatives > 2');
        if(MYSQL::numRows($queryBanHack) > 0){
            $queryHack = MYSQL::query('SELECT description, date, page FROM tentatives_hack WHERE ip=\''.UTILS::getIp().'\' ORDER BY id desc LIMIT 5');
            $array = array();
            while($result = mysqli_fetch_row($queryHack)){
                $array[] = [$result[0], $result[1], $result[2]];
            }
            return array(true, UTILS::getIp(), $array);
        }
        
        return false;
    }
    
    public static function isBanni() {
        if(USER::isConnecte()){
            $query = MSSQL::Query('SELECT bloc_code FROM MEMB_INFO WHERE memb___id =\''.USER::GetPseudo().'\' AND bloc_code = 1');
            if(sqlsrv_num_rows($query) > 0){
                $query = MSSQL::Query('SELECT * FROM MuBan WHERE ban_name =\''.USER::GetPseudo().'\' AND ban_expire > GETDATE()');
                if(sqlsrv_num_rows($query) > 0){
                    $result = sqlsrv_fetch_object($query);
                    if($result->ban_permanent == 1){
                        $duree = 'Vous êtes banni à vie !';
                    }else{
                        $duree = 'Vous êtes banni jusqu\'au '.$result->ban_expire->format('d/m/Y à H:i');
                    }
                    
                    $banDate = $result->ban_date->format('d/m/Y à H:i');
                    return array(true, $banDate, $duree, $result->reason, $result->author);
                }else{ // sinon on deban le compte
                    MSSQL::Query('UPDATE MEMB_INFO  SET bloc_code = 0 WHERE memb___id =\''.USER::GetPseudo().'\'');
                    UTILS::notification('success', 'Votre compte est désormais débanni, la prochaine merci d\'en profiter pour lire les rêgles du jeu pour éviter un autre blocage du compte.', false, true);
                    header('location: /Accueil');
                    exit;
                }
            }
            
            return false;
        }
    }

	public static function hasUserConnected(){
		$req = MYSQL::query('SELECT login FROM users WHERE hasOnline = true');
		$arr = [];
		while($result = mysqli_fetch_object($req)){
			array_push($arr, $result->login);
		}
		CACHE::set('hasOnline', serialize($arr));

		return $arr;
	}


    public static function isConnecte() {
		if(self::$isConnecte == null) {
			self::$isConnecte = (DATA::isSession('login') && DATA::isSession('ip') == UTILS::getIp());
		}
		return self::$isConnecte; 
	}
	
	// public static function isCharacter($characterName) {
	// 	$query = MYSQL::query('SELECT AccountID FROM Character WHERE Name=\''.$characterName.'\' AND AccountID=\''.self::getPseudo().'\'');
	// 	$result = (sqlsrv_num_rows($query) > 0);
	// 	return $result;
	// }

	public static function Login($pseudo, $password){
		$check = MYSQL::query('SELECT * FROM users WHERE login =\''.$pseudo.'\' AND password=\''.md5($password).'\'');
		if(mysqli_num_rows($check) > 0){
			$RestorePassword = MYSQL::query('SELECT recovery FROM users WHERE login = \''.$pseudo.'\' AND recovery = 1');
            if(mysqli_num_rows($RestorePassword) > 0){
                MYSQL::query('UPDATE users SET recovery = 0 WHERE login = \''.$pseudo.'\'');
			}
			$resultAccount = mysqli_fetch_object($check);
			MYSQL::query('Insert into historique (idType_Historique, date, memb___id, ip, action) VALUES (9, NOW(), "'.$pseudo.'", "' . $_SERVER["REMOTE_ADDR"] . '", "Connexion au site.")');
			DATA::setSession('id', $resultAccount->id);
        	DATA::setSession('email', $resultAccount->email);
			DATA::setSession('password', $password);
			DATA::setSession('login', $pseudo);
			DATA::setSession('ip', UTILS::getIp());
			return true;
		}else{
			MYSQL::query('Insert into historique (idType_Historique, date, memb___id, ip, action) VALUES (8, NOW(), "'.$pseudo.'", "' . $_SERVER["REMOTE_ADDR"] . '", "Connexion échouée au site")');
			return false;
		}
	}
	
	public static function getPseudo() {
		if(!self::isConnecte()) trigger_error('Erreur de session (Tentative de récupèration du pseudo d\'un invité).<br />', E_USER_ERROR);
		return DATA::getSession('login');
	}
	
	public static function isAdmin() {
		if(!self::isConnecte()) return false;

		if(self::$isAdmin === null) {
			$q = MYSQL::query('SELECT * FROM users LEFT JOIN admin on users.login = admin.login WHERE users.login=\''.USER::getPseudo().'\' AND users.hasAdmin = 1');
			if(mysqli_num_rows($q) > 0) {
				self::$isAdmin = true;
			} else {
				self::$isAdmin = false;
			}
		}
		return self::$isAdmin;
	}
	
	// on calcule le nombre de jour date d'inscription	
    public static function DateCompte($date_compte){
		$query_date = MYSQL::QUERY('SELECT registerDate FROM users WHERE login=\''.USER::getPseudo().'\'');
		$row = MYSQL::fetchArray($query_date);
		$premiere_date= $row['registerDate'];
		$deuxieme_date= date("Y-m-d H:i:s");

		$difference = abs(strtotime($deuxieme_date) - strtotime($premiere_date));

		$annee= floor($difference / (365*60*60*24));
		$mois= floor(($difference - $annee* 365*60*60*24) / (30*60*60*24));
		$jours= floor(($difference - $annee* 365*60*60*24 - $mois*30*60*60*24)/ (60*60*24));

		$date_compte = sprintf('%d annee, %d mois, %d jours', $annee, $mois, $jours);

				
		return $date_compte;
			
		/* � utiliser dans index '.USER::DateCompte().' */
    }
	
	
	public static function isReferenceur() {
		if(!self::isConnecte()) return false;
		if(self::$isReferenceur === null) {
			$ref = MSSQL::selectOneValue('SELECT referenceur FROM memb_info WHERE memb___id=\''.self::getPseudo().'\'');
			self::$isReferenceur = ($ref == 1);
		}
		return self::$isReferenceur;
	}
	
	public static function isConnecteIG() {
		if(self::$isConnecteIG === null) {
			$result = sqlsrv_fetch_array(MSSQL::query('SELECT ConnectStat FROM MEMB_STAT WHERE memb___id=\''.self::getPseudo().'\''));
			self::$isConnecteIG = $result['ConnectStat'];
		}
		return self::$isConnecteIG;
	}
	
	public static function getIp() {
		if(self::$ipAddress == null) {
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
				if(strchr($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
					$tab = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
					$proxy = trim($tab[count($tab)-1]);
					$realip = trim($tab[0]);
				} else {
					$realip = trim($_SERVER['HTTP_X_FORWARDED_FOR']);
					$proxy = $_SERVER['REMOTE_ADDR'];
				}
				if(ip2long($realip) === FALSE) $realip = $_SERVER['REMOTE_ADDR'];
			} else {
				$realip = $_SERVER['REMOTE_ADDR'];
				$proxy = '';
			}
			if($realip == $proxy) $proxy = '';
			
			self::$ipAddress =  '(IP : '.$realip.(($proxy != '') ? ', PROXY : '.$proxy : '').')';
		}
		return self::$ipAddress;
	}

}

?>