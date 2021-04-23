<?php
    //authentification API
    define('API', array(
        'SECRET_KEY' => '18BA608A63CE09EFA62492D98CCC00476BEA12282B6D0C882FF27A82DA43994B',
        'AUDIENCE_CLAIM' => 'Warehouse',
        'ISSUER_CLAIM' => 'Warehouse',
        'ISSUEDAT_CLAIM' => time(), // issued at
        'NOT_BEFORE_CLAIM' => time() + 10, //not before in seconds
        'EXPIRE_CLAIM' => time() + 60, // expire time in seconds,
        'PORT_FRONT' => ':4200', // supprimer lorsque le front est sur le meme domaine que le back 
        'URL_API' => 'http://warehouse.test',
        'LANG' => 'fr'
    ));

    define('SMTP', array(
        'PROTOCOLE' => '',
        'HOST' => '',
        'PORT' => 465,
        'USERNAME' => '',
        'PASSWORD' => '',
        'SET_FROM' => array(
            'EMAIL' => 'webmaster@magestic.eu',
            'WEBSITE' => 'warehouse',
        )
    ));

    define('SQL', array(
        'DOMAIN' => 'localhost',
        'USER' => 'root',
        'PASSWORD' => '',
        'BDD' => 'magestic'
    ));

    define(
		'MAIL_HEADER', 
		"From: ".SMTP['SET_FROM']['WEBSITE']." <".SMTP['SET_FROM']['EMAIL'].">" . "\r\n
		X-Mailer: PHP ".phpversion()."\n
		X-Priority: 1 \n
		Mime-Version: 1.0\n
		Content-Transfer-Encoding: 8bit\n
		Content-type: text/html; charset= utf-8\n
		Date:" . date("D, d M Y h:s:i") . " +0200\n"
	);

	define('HTML_PARAMS','dir="LTR" lang="'.API['LANG'].'"');
?>
