<?php
class ADMIN {
	private static $droits = null;

	public static function init() {
        if(!USER::isAdmin()){
            UTILS::addHack('Tentative d\'accès sur l\'administration sans les droits requis.'); 
            UTILS::notification('danger', 'Vous n\'avez pas les droits nécéssaires pour accéder à cette page. Votre adresse IP sera bannie après 3 tentatives', false, true);
            header('location: /Accueil');
            exit;
        }
	}
	
	public static function getDroit($type) {
		if(self::$droits === null) {
            $droits = MSSQL::query('SELECT CtlCode FROM Character WHERE AccountID=\''.USER::getPseudo().'\' AND CtlCode >= '.$type.'');
            if(sqlsrv_num_rows($droits) > 0){
                return true;
            }else{
                return false;
            }
		}
	}
	
	public static function verifDroit($type) {
		if(!self::getDroit($type)){
            UTILS::notification('warning', 'Vous n\'avez pas les droits nécéssaires pour accéder à cette page.', false, true);
            header('location: /Administration');
            exit;
        } 
    }
    
    public static function Grade($pseudo) {
        $grade = MSSQL::query('SELECT CtlCode FROM Character WHERE AccountID=\''.$pseudo.'\'');
        $result = sqlsrv_fetch_object($grade);
        return $result->CtlCode;
    }
}

?>