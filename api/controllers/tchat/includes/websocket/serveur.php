<?php
$route = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
require_once($route.'/classes/MYSQL.class.php');
require_once($route.'/consts/constantes.php');

// prevent the server from timing out
set_time_limit(0);
// include the web sockets server script (the server is started at the far bottom of this file)
require 'WebSocketHandshake.class.php';
// when a client sends data to the server
function GetAvatar($avatar){
	$jpg = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/assets/images/avatars/'.$avatar.'.jpg';
	$gif = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/assets/images/avatars/'.$avatar.'.gif';
	$jpeg = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/assets/images/avatars/'.$avatar.'.jpeg';
	$png = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/assets/images/avatars/'.$avatar.'.png';
	if(file_exists('.'.$jpg)) {
		$check_avatar = $jpg;
	} elseif(file_exists('.'.$gif)) {
		$check_avatar = $gif;
	} elseif(file_exists('.'.$jpeg)) {
		$check_avatar = $jpeg;
	} elseif(file_exists('.'.$png)) {
		$check_avatar = $png;
	} else {
		$check_avatar = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/includes/assets/images/avatars/no-avatar.png';
	}
	
	return API['URL_API'].$check_avatar.'?'.time();
}

function wsOnMessage($clientID, $message, $messageLength, $binary) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	
	$messageJson = json_decode($message);
	
	
	$Server->log( "$ip ($clientID) $messageJson->pseudo : $messageJson->message." );
	MYSQL::query('INSERT INTO chat (pseudo, message, heure) VALUES (\''.addslashes($messageJson->pseudo).'\', \''.addslashes($messageJson->message).'\', \''.time().'\')');
	
	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}
	
		//Send the message to everyone but the person who said it
		foreach ( $Server->wsClients as $id => $client )
			if ( $id != $clientID ){
				
				$messageReturning = json_encode(array("pseudo" => $messageJson->pseudo, "message" => $messageJson->message, "date" => $messageJson->date, "avatar" => GetAvatar($messageJson->pseudo)));
				$Server->wsSend($id, $messageReturning);
			}
}

// when a client connects
function wsOnOpen($clientID)
{
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log( "$ip ($clientID) has connected." );
	//Send a join notice to everyone but the person who joined
	/*foreach ( $Server->wsClients as $id => $client )
		if ( $id != $clientID )
			$Server->wsSend($id, "Visitor $clientID ($ip) has joined the room.");*/
}
// when a client closes or lost connection
function wsOnClose($clientID, $status) {
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );
	$Server->log( "$ip ($clientID) has disconnected." );
	//Send a user left notice to everyone in the room
	/*foreach ( $Server->wsClients as $id => $client )
		$Server->wsSend($id, "Visitor $clientID ($ip) has left the room.");*/
}
// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer('127.0.0.1', 8080);
?>