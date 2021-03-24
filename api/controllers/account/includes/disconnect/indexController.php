<?php
    MYSQL::query('DELETE FROM login WHERE pseudo = \''.USER::getPseudo().'\'');
    MYSQL::query('UPDATE users SET hasOnline = false WHERE login = \''.USER::getPseudo().'\'');
    MYSQL::query('INSERT INTO historique (idType_historique, date, memb___id, ip, action) VALUES (10, NOW(), \''.USER::getPseudo().'\', \''.$_SERVER["REMOTE_ADDR"].'\', \'Déconnexion du site.\')');
    $_SESSION = array();
    http_response_code(401);
    echo json_encode(array("message" => "Disconnected."));
	exit;
?>