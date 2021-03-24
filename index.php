<?php
		
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	header('Content-type: application/json; charset=UTF-8');
	define(
		'MAIL_HEADER', 
		"From: Magestic.eu <webmaster@magestic.eu>" . "\r\n
		X-Mailer: PHP ".phpversion()."\n
		X-Priority: 1 \n
		Mime-Version: 1.0\n
		Content-Transfer-Encoding: 8bit\n
		Content-type: text/html; charset= utf-8\n
		Date:" . date("D, d M Y h:s:i") . " +0200\n"
	);

	define('HTML_PARAMS','dir="LTR" lang="fr"');

	require('./consts/constantes.php');

	require('./classes/vendor/autoload.php');

	spl_autoload_register(
		function($x) {
			$sources = array('./classes/'.str_replace('_', '/', $x).'.class.php'); // chargement des classes
			foreach ($sources as $source) {
				if (file_exists($source)) {
					require_once $source;
				}
			}
		}
	);

	
	
    // on bloque l'accès fichier.php
    if(preg_match('#index.php#i', $_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/?') === 0) {
		header("HTTP/1.0 404 Not Found");
        exit;
    }

    //définition du fuseau horaire
    date_default_timezone_set('Europe/Paris');
    
    setlocale(LC_TIME, 'fr_FR.utf8','fra');

    //on initialise le système d'erreurs personnalisé
    set_error_handler(Array("Erreur", "setError"));

    //démarrage des sessions
	USER::init();

	if(DATA::isGet('api')) { // inclusion des pages dans le template
		$api = DATA::getGet('api');
		if(!preg_match('#\.\.#', $api) && !preg_match('#://#', $api) && !preg_match('#_admin#', $api) && file_exists('./api/controllers/'.$api.'.php')) {
			include('./api/controllers/'.$api.'.php');
		} else {
			header("HTTP/1.0 404 Not Found");
		}
	} else {
		header("HTTP/1.0 404 Not Found");
		exit;
	}
	
	

	//On libère la bande passante après le chargement de la page
	$contenuVariable = array_keys(get_defined_vars());
	foreach ($contenuVariable as $var) {
		unset($var);
	}
?>