<?php
	$repository = new Repository;
	$repository->getRepository('account', DATA::getGet('account'));
	include('includes/'.DATA::getGet('account').'/indexController.php');

	// switch(DATA::getGet('account')){
	// 	case 'login' :
	// 		include('components/connexion.php');
	// 	break;
	// 	case 'disconnect' :
	// 		include('components/deconnexion.php');
	// 	break;
	// 	case 'register' : 
	// 		include('components/inscription.php');
	// 	break;
	// 	case 'user-profile' : 
	// 		if(DATA::getSession('token') === DATA::getGet(DATA::getGet('user-profile'))){
	// 			DATA::setSession('frontToken', DATA::getGet(DATA::getGet('user-profile')));
	// 			MYSQL::query('UPDATE users SET hasOnline = true WHERE login = \''.USER::getPseudo().'\'');
	// 			echo json_encode(
	// 				array(
	// 					"message" => "Successful login.",
	// 					"jwt" => DATA::getSession('token'),
	// 					"id" => DATA::getSession('id'),
	// 					"login" => DATA::getSession('login'),
	// 					"email" => DATA::getSession('email'),
	// 					"expireAt" => API['EXPIRE_CLAIM'],
	// 					"avatar" => UTILS::GetAvatar(DATA::getSession('login'))
	// 				)
	// 			);
	// 		} else {
	// 			include('components/deconnexion.php');
	// 		}
	// 	break;
	// 	case 'recovery' : 
	// 		include('components/recovery.php');
	// 	break;
	// 	case 'myaccount' : 
	// 		include('components/compte.php');
	// 	break;
	// 	default : 
	// 		http_response_code(404);
    //     	echo json_encode(array("message" => "Error 404."));
	// }
?>