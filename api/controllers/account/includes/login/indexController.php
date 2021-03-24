<?php

	if(DATA::isPost('login') && DATA::isPost('password')){
		if(USER::Login(DATA::getPost('login'), DATA::getPost('password'))){

			$token = array(
				"iss" => API['ISSUER_CLAIM'],
				"iat" => API['ISSUEDAT_CLAIM'],
				"nbf" => API['NOT_BEFORE_CLAIM'],
				"exp" => API['EXPIRE_CLAIM'],
				"data" => array(
					"id" => DATA::getSession('id'),
					"login" => DATA::getSession('login'),
					"email" => DATA::getSession('email'),
					"ip" => DATA::getSession('ip'),
					"avatar" => UTILS::GetAvatar(DATA::getSession('login'))
				)
			);

			if(USER::isAdmin()){
				$token['data']['hasAdmin'] = true; 
			}
	
			http_response_code(200);

			$jwt = JWT::encode($token, API['SECRET_KEY']);

			DATA::setSession('token', $jwt);

			echo json_encode(
				array(
					"message" => "Successful login.",
					"jwt" => $jwt,
					"id" => DATA::getSession('id'),
					"login" => DATA::getSession('login'),
					"email" => DATA::getSession('email'),
					"expireAt" => API['EXPIRE_CLAIM'],
					"avatar" => UTILS::GetAvatar(DATA::getSession('login'))
				)
			);
        } else {
			http_response_code(401);
        	echo json_encode(array("message" => "Login failed."));
			exit;
		}
    }else{
		http_response_code(401);
        echo json_encode(array("message" => "Error."));
		exit;
	}
?>