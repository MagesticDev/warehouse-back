<?php
		
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
	header('Content-type: application/json; charset=UTF-8');

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

	$error = false;

	if(DATA::isGet('api')) { // inclusion des pages dans le template
		$api = DATA::getGet('api');
		if(!preg_match('#\.\.#', $api) && !preg_match('#://#', $api) && !preg_match('#_admin#', $api) && file_exists('./api/controllers/'.$api.'.php')) {
			include('./api/controllers/'.$api.'.php');
		} else {
			$error = true;
		}
	} else {
		$error = true;
	}

	if($error) {
		echo json_encode(array(
			'error' => '404',
			'message' => '404 Not Found'
		));
		header("HTTP/1.0 404 Not Found");
		exit;
	}

	$result_errors = Erreur::getInstance();
	$errors = $result_errors->getErrors(); 
	if($errors && USER::isAdmin()) {
		DATA::setSession('PHP_ERRORS', json_encode($errors));
	}
	
	//On libère la bande passante après le chargement de la page
	$contenuVariable = array_keys(get_defined_vars());
	foreach ($contenuVariable as $var) {
		unset($var);
	}
?>