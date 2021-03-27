<?php
    if(!USER::isConnecte()){
        if(DATA::isGet('restorePassword')){
            $value = explode('::', base64_decode(DATA::getGet('restorePassword')));
            $expiration = date('Hi', time()) - date('Hi', $value[1]);
          
            $reqAccount = MYSQL::query('SELECT * FROM users WHERE email = \''.$value[2].'\' AND password = \''.$value[0].'\' AND recovery = 1');
            if(mysqli_num_rows($reqAccount) > 0){
                
                if($expiration < 60){
                    if(DATA::isPost('password') && DATA::isPost('passwordCheck') && DATA::isPost('captcha')){
                        $type = 'warning';
                        if(DATA::getPost('password') === DATA::getPost('passwordCheck')){
                            if(md5(DATA::getPost('captcha')) == $_SESSION['image_random_value']){
                                MYSQL::query('UPDATE users SET password = \''. md5(DATA::getPost('password')).'\', recovery = 0 WHERE email = \''.$value[2].'\' AND password = \''.$value[0].'\'');
                                $message = array(
                                    "type" => "recoveryOk",
                                    "status" => "success",
                                    "message" => "Le password a été réinitialisé avec succès."
                                );
                            }else{
                               $message = array(
                                    "type" => "captcha",
                                    "status" => "warning",
                                    "message" => "Merci de recopier correctement l'image de verification."
                                );
                            }
                        }else{
                            $message = array(
                                "type" => "password",
                                "status" => "warning",
                                "message" => "Vos deux mots de passe ne sont pas identiques..."
                            );
                        }

                    }

                }else{
                    $message = array(
                        "type" => "timeOut",
                        "status" => "warning",
                        "message" => "Désolé mais le délais est dépassé."
                    );
                }

            }else{
               $message = array(
                    "type" => "recovery",
                    "status" => "warning",
                    "message" => "Désolé l'url n'est pas valide."
                );
            }

        }else{
            if(DATA::isPost('email') && DATA::isPost('captcha')){
                $reqAccount = MYSQL::query('SELECT login, email, password FROM users WHERE email = \''.DATA::getPost('email').'\'');
                $type = 'warning';
                if(mysqli_num_rows($reqAccount) > 0){
                    if(md5(DATA::getPost('captcha')) == $_SESSION['image_random_value']){
                        
                        $resultAccount = mysqli_fetch_object($reqAccount);
                        MYSQL::query('UPDATE users SET recovery = 1 WHERE email = \''.DATA::getPost('email').'\'');

                        $message = array(
                            "type" => "recovery",
                            "status" => "success",
                            "message" => "Un email de réinitialisation du password a été envoyé sur l'adresse [".DATA::getPost('email')."]."
                        );
                        
                        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].(API['PORT_FRONT'] ? API['PORT_FRONT'] : '').'/Recovery/'.base64_encode($resultAccount->password.'::'.time().'::'.$resultAccount->email);
                        $mail = new SendMail($resultAccount->email, 'Mot de passe perdu', $resultAccount->login, false, $url, 'passwordRecovery');
                        $mail->send();
                        
                    }else{
                        $message = array(
                            "type" => "recovery",
                            "status" => "error",
                            "message" => "Merci de recopier correctement l'image de verification."
                        );
                    }
                }else{
                    $message = array(
                        "type" => "recovery",
                        "status" => "error",
                        "message" => "Désolé, aucun compte n\'est associé à l'adresse email que vous avez saisi"
                    );
                }
            }
        }
    }else{
        $message = array(
            "type" => "recovery",
            "status" => "error",
            "message" => "Vous ne pouvez pas réinitialiser votre password de cette manière tout en étant connecté."
        );
    }

    echo json_encode($message);
    exit;
?>