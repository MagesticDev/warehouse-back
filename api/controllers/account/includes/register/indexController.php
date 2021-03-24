<?php

$message = array();

if(!USER::isConnecte()){ 

	if(DATA::isPost('email') 
	&& DATA::isPost('confirmEmail') 
	&& DATA::isPost('password') 
	&& DATA::isPost('confirmPassword') 
	&& DATA::isPost('country') 
	&& DATA::isPost('question') 
	&& DATA::isPost('response') 
	&& DATA::isPost('pseudo')
	&& DATA::isPost('captcha')
	&& DATA::isPost('check')){

		$registerRepository = new RegisterRepository;
		$account = $registerRepository->getExistingAccount(DATA::getPost('pseudo'), DATA::getPost('email'));
		
		if(DATA::getPost('email') != DATA::getPost('confirmEmail')){
			array_push($message, array(
					'status' => 'error',
					'type' => 'identicalEmail',
					'message' => 'Vos deux adresses email ne sont pas identiques...'
				)
			);
		}

		if(!preg_match("/^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-_.]?[[:alnum:]])*\.([a-z]{2,4})$/i", DATA::getPost('email'))){
			array_push($message, array(
					'status' => 'error',
					'type' => 'email',
					'message' => 'L\'adresse email est incorrect...'
				)
			);
		}

		if((!preg_match('/^[a-z0-9]+$/i', DATA::getPost('pseudo', false)))){
			array_push($message, array(
					'status' => 'error',
					'type' => 'pseudo',
					'message' => 'Le pseudo est incorrect...'
				)
			);
		}

		if($account){
			
			if($account['login'] === DATA::getPost('pseudo')){
				array_push($message, array(
						'status' => 'error',
						'type' => 'pseudo',
						'message' => 'Ce pseudo est déjà utilisée...'
					)
				);
			}

			if($account['email'] === DATA::getPost('email')){
				array_push($message, array(
						'status' => 'error',
						'type' => 'email',
						'message' => 'Cette adresse email est déjà utilisée...'
					)
				);
			}
		}

		if(DATA::getPost('password') != DATA::getPost('confirmPassword')){
			array_push($message, array(
					'status' => 'error',
					'type' => 'identicalPassword',
					'message' => 'Vos deux mots de passe ne sont pas identiques...'
				)
			);
		}

		if(md5(DATA::getPost('captcha')) != $_SESSION['image_random_value']){
			array_push($message, array(
					'status' => 'error',
					'type' => 'incorrectCaptcha',
					'message' => 'Merci de recopier correctement l\'image de verification.'
				)
			);
		}

		if(empty($message)){
			$registerRepository->register(DATA::getPost('pseudo'), DATA::getPost('email'), DATA::getPost('question'), DATA::getPost('response'), DATA::getPost('password'));
			array_push($message, array(
					'status' => 'success',
					'type' => 'registerSuccess',
					'message' => 'Votre inscription s\'est déroulée avec succès, vous pouvez dès à présent vous connecter.'
				)
			);
		}
		echo json_encode($message);
	} else {
		array_push($message, array(
				'status' => 'error',
				'type' => 'incorrectForm',
				'message' => 'Merci de remplir tout les champs correctement.'
			)
		);
		echo json_encode($message);
	}
}else{
	array_push($message, array(
			'status' => 'error',
			'type' => 'hasLogged',
			'message' => null
		)
	);
	echo json_encode($message);
}
?>