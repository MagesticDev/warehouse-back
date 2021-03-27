<?php

class UTILS {
	/* 
	  * Le résultat de la recherche de L'ip. On le stocke pour éviter de refaire les calculs
	  * @var string
	 */
	private static $ipAddress = null;
	/*
	 * Pour récupérer la vraie adresse IP, męme derričre un proxy
	 * @return 
	 */
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

	public static function initiales($nom){
        $nom_initiale = ''; // déclare le recipient
        $n_mot = explode(" ",$nom);
        foreach($n_mot as $lettre){
            $nom_initiale .= $lettre{0}.'.';
        }
        return strtoupper($nom_initiale);
    }
	
	public static function getTicketStatus($ouvert, $status) {
		if ($ouvert == 1 ) {
			return '<font color="#2776dc">Ouvert</font>';
		} else {
			if ($ouvert == 2) {
				return '<font color="green">Résolu</font>';
			} else {
				return '<font color="red">Fermé</font>';
			}
		}
	}

	public static function GetAvatar($avatar){
		$jpg = '/includes/assets/images/avatars/'.$avatar.'.jpg';
		$gif = '/includes/assets/images/avatars/'.$avatar.'.gif';
		$jpeg = '/includes/assets/images/avatars/'.$avatar.'.jpeg';
		$png = '/includes/assets/images/avatars/'.$avatar.'.png';
		if(file_exists('.'.$jpg)) {
			$check_avatar = $jpg;
		} elseif(file_exists('.'.$gif)) {
			$check_avatar = $gif;
		} elseif(file_exists('.'.$jpeg)) {
			$check_avatar = $jpeg;
		} elseif(file_exists('.'.$png)) {
			$check_avatar = $png;
		} else {
			$check_avatar = '/includes/assets/images/avatars/no-avatar.png';
		}
		
		return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$check_avatar.'?'.time();
	}
	
	public static function getAdmin($pseudo){
		$req = MYSQL::query('SELECT * FROM admin WHERE login=\''.$pseudo.'\'');
		if(mysqli_num_rows($req) > 0){
			return true;
		}
    }
   
    public static function TopRanks($number){
        switch($number){
            case 1 :
                return '<img src="/../includes/assets/images/rank/golds.gif" alt="1ER du classements" />';
            break;
            case 2 :
                return '<img src="/../includes/assets/images/rank/argents.gif" alt="2EME du classements" />';
            break;
            case 3 :
                return '<img src="/../includes/assets/images/rank/bronze.gif" alt="3EME du classements" />';
            break;
            default :
                return $number;
            break;
        }
    }

	public static function regexUrl($string){
		
		//The Regular Expression filter
		$reg_exUrl = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
		
		// Check if there is a url in the text
		if(preg_match_all($reg_exUrl, $string, $url)) {
			
			// Loop through all matches
			foreach($url[0] as $newLinks){
				if(strstr( $newLinks, ":" ) === false){
					$link = 'http://'.$newLinks;
				}else{
					$link = $newLinks;
				}
	
				// Create Search and Replace strings
				$search  = $newLinks;
				$replace = '<a href="'.$link.'" title="'.$newLinks.'" target="_blank">'.$link.'</a>';
				$string = str_replace($search, $replace, $string);
			}
		}
	
		//Return result
		return $string;
	}
	

	public static function notification($type, $message, $true=true, $closeBtn=true){
		if($closeBtn):
			$close = '<button type="button" class="close notif-close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
					</button>';
		else :
			$close = false;
		endif;
				  
		$alert = array(
			'primary' => '<div class="alert alert-primary w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'secondary' => '<div class="alert alert-secondary w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'success' => '<div class="alert alert-success w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'danger' => '<div class="alert alert-danger w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'warning' => '<div class="alert alert-warning w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'info' => '<div class="alert alert-info w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'light' => '<div class="alert alert-light w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
			'dark' => '<div class="alert alert-dark w-50 d-flex align-items-center justify-content-between" role="alert">'.$message.$close.'</div>',
		);
		
		DATA::setSession('notification', '<div class="notification">'.$alert[$type].'</div>');
		if($true){
			header('location: '.$_SERVER['HTTP_REFERER']);
			exit();
		}
	}

	public static function Alert($type, $titre, $message, $urlPost, $name, $value){
		$alert 	= '<div class="notification">';
		$alert .= '<div class="alert alert-'.$type.'" role="alert">';
		$alert .= '<h4 class="alert-heading text-left">'.$titre.'</h4>';
		$alert .= '<p>'.$message.'</p><hr>';
		$alert .= '<p class="mb-0">';
		$alert .= '<form method="POST" action="'.$urlPost.'">';
		$alert .= '<a href="'.$_SERVER['HTTP_REFERER'].'" class="btn btn-success">Annuler</a>';
		$alert .= '<input type="hidden" name="'.$name.'" value="'.$value.'"/>';
		$alert .= '<button type="submit" class="btn btn-danger ml-1">Confirmer</button>';
		$alert .= '</form>';
		$alert .= '</p></div>';
		$alert .= '</div>';
		DATA::setSession('notification', $alert);
	}

	public static function getVip(){
		if(CACHE::is('ListeVip-'.date('Y-m-d'), 3600)) {			
			$vipArray = unserialize(CACHE::get('ListeVip-'.date('Y-m-d')));
		} else {
			$vip = MSSQL::query('SELECT * FROM T_VIPList WHERE Date > GETDATE()');
			if(sqlsrv_num_rows($vip) > 0){
				$vipArray = array();
				while($result = sqlsrv_fetch_object($vip)){
					$vipArray[] = [$result->AccountID, $result->Date, $result->Type];
				}
				CACHE::set('ListeVip-'.date('Y-m-d'), serialize($vipArray));
			}
		}
	}

	public static function initOutputFilter() {
	   ob_start('ob_gzhandler');
	   register_shutdown_function('ob_end_flush');
	}
	
	public static function random($car) {
		$string = "";
		$chaine = "AZERTYUIOPQSDFGHJKLMWXCVBN0123456789";
		srand((double)microtime()*1000000);
		
		for($i=0; $i<$car; $i++) {
			$string .= $chaine[rand()%strlen($chaine)];
		}
		
		return $string;
	}


	
	public static function myUrlEncode($string) {
		$entities = array(        '%21',        '%2A',          '%27',        '%28',          '%29',          '%3B',           '%3A',          '%40',          '%26',         '%3D',        '%2B',         '%24',         '%2C',         '%2F',        '%3F',        '%25',        '%23',       '%5B',       '%5D');
		$replacements = array(    '!',          '*',            "'",          "(",            ")",            ";",             ":",            "@",            "&",           "=",          "+",            "$",           ",",           "/",          "?",          "%",         "#",          "[",         "]");
		return str_replace($entities, $replacements, urlencode($string));
	}
	
	/*
	 * Pour encoder le nom d'une page pour avoir une url propre
	 */
	public static function encodeNomPage($nom) {
		$newNom =	 strtolower(strtr($nom, 'ŔÁÂĂÄĹŕáâăäĺŇÓÔŐÖŘňóôőöřČÉĘËčéęëÇçĚÍÎĎěíîďŮÚŰÜůúűü˙Ńń',
											'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn'
					));
		$newNom = preg_replace('`([^_a-z0-9])`i', '_', $newNom);

		return $newNom;
	}
	
	function suppr_accents($str, $encoding='utf-8'){
		// transformer les caractères accentués en entités HTML
		$str = htmlentities($str, ENT_NOQUOTES, $encoding);
	 
		// remplacer les entités HTML pour avoir juste le premier caractères non accentués
		// Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
		$str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
	 
		// Remplacer les ligatures tel que : Œ, Æ ...
		// Exemple "Å“" => "oe"
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
		// Supprimer tout le reste
		$str = preg_replace('#&[^;]+;#', '', $str);
	 
		return $str;
	}
	
	/*
	 * Pour optimiser le code html avant envoi
	 */
	public static function compressHtml($buffer) {
		$poz_current = 0;
		$poz_end = strlen($buffer)-1;
		$result = '';
		
		function compressbuffer_html($buffer) {
			
			$buffer = preg_replace('<!\-\- [\/\ a-zA-Z]* \-\->', '', $buffer);
			$buffer = preg_replace("#([\s]+)#", ' ', $buffer);
			$buffer = preg_replace('#<!--.*?-->#s', '', $buffer);
			$buffer = str_replace(Array('<!--', '-->'), Array("\n<!--\n", "\n-->\n"), $buffer);
			return $buffer;
		}
 
		while($poz_current < $poz_end) {
			$t_poz_start = strpos($buffer, '<textarea', $poz_current);
			if($t_poz_start === false) {
				$buffer_part_2strip = substr($buffer, $poz_current);
				$temp = compressbuffer_html($buffer_part_2strip);
				$result .= $temp;
				$poz_current = $poz_end;
			} else {
				$buffer_part_2strip = substr($buffer, $poz_current, $t_poz_start-$poz_current);
				$temp = compressbuffer_html($buffer_part_2strip);
				$result .= $temp;
				$t_poz_end = strpos($buffer, '</textarea>', $t_poz_start);
				$temp = substr($buffer, $t_poz_start, $t_poz_end-$t_poz_start);
				$result .= $temp;
				$poz_current = $t_poz_end;
			}
		}
		return $result;
	}
	
	
	
	public static function addHack($description, $doBan = false) {
		$post = str_replace("'", "''", DATA::getSerializedPost());
		$get = str_replace("'", "''", DATA::getSerializedGet());
		$cookies = str_replace("'", "''", DATA::getSerializedCookie());
		$desc = str_replace("'", "''", $description);
		
		$page = $_SERVER['REQUEST_URI'];
        
		MYSQL::query(preg_replace("#\\0#", "\\\\0", 'INSERT INTO tentatives_hack
			(ip, 					
			date, 			
			page, 			
			post, 			
			getGet, 			
			cookies, 			
			description,
			pseudo) 
			VALUES
			(\''.self::getIp().'\', 
			\''.time().'\',
			\''.$page.'\', 
			\''.$post.'\', 
			\''.$get.'\', 	
			\''.$cookies.'\', 
			\''.$desc.'\',
			\''.((USER::isConnecte()) ? USER::getPseudo() : '').'\')'));
		
		if($doBan) {
			$query = MYSQL::query('SELECT ip FROM ip_ban_hack WHERE ip=\''.self::getIp().'\'');
			if(MYSQL::fetchArray($query) > 0) {
                MYSQL::query('UPDATE ip_ban_hack SET tentatives=tentatives+1, derniere=\''.time().'\' WHERE ip=\''.self::getIp().'\'');
			} else {
                MYSQL::query('INSERT INTO ip_ban_hack (ip, tentatives, derniere) VALUES (\''.self::getIp().'\', 1, \''.time().'\');');
			}
			
			
		}
	}	

	public static function Encode($text){
		$text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
		$text = htmlspecialchars_decode($text);
		return $text;
	}

	public static function FormatNumber($n) {
        $n = (0+str_replace(",","",$n));
        if(!is_numeric($n)) return false;
        if($n>1000000000000) return round(($n/1000000000000),1).' T';
        else if($n>1000000000) return round(($n/1000000000),1).' B';
        else if($n>1000000) return round(($n/1000000),1).' M';
        else if($n>1000) return round(($n/1000),1).' K';
        return number_format($n);
    }

	public static function GetGolds($parametre){
		if($parametre < 10){
			if($parametre == ''){
				$parametre = 0;
			}
			return '[<strong class="text-danger">'.$parametre.'</strong>]';
		}elseif($parametre >= 10 && $parametre <= 90){
			return '[<strong class="text-warning">'.$parametre.'</strong>]';
		}else{
			return '[<strong class="text-success">'.$parametre.'</strong>]';
		}
	}
}

?>