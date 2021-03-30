<?php
    if(USER::isConnecte()) {
        
        if(DATA::getPost('id') == 16 && !USER::isAdmin() OR DATA::getPost('id') == 10 && !USER::isAdmin()) {
            
            echo json_encode(array(
                'error' => '403',
                'type' => 'warning',
                'message' => 'Vous ne pouvez pas créer de topic ici.'
            ));
            
            exit;

        }else{


            // Anti flood
            $q = MYSQL::query('SELECT id FROM responses WHERE author_text=\''.USER::getPseudo().'\' AND time>'.(time()-10).' LIMIT 1');
            // Vérification du forum
            $verif_forum = MYSQL::selectOneRow('SELECT COUNT(id) AS forum_exist, title FROM forums WHERE id="'.DATA::getPost('id').'"'); 
                        
            if(MYSQL::numRows($q) > 0){
                
                echo json_encode(array(
                    'error' => '403',
                    'type' => 'warning',
                    'message' => 'Vous devez attendre 10 secondes avant de pouvoir poster un nouveau message.'
                ));
                
                exit;
            }elseif($verif_forum['forum_exist'] == 0){
                echo "Cette section ne semble pas exister ...",
                // UTILS::notification('warning', 'Cette section ne semble pas exister ...', false, true);
                // $url = explode('/Nouveau', $_SERVER['REQUEST_URI']);
                // header('location: '.$url[0]);
                exit;
            }else{

                if(DATA::isPost('text')) {
                    if(DATA::isPost('title') && strlen(DATA::getPost('title')) >= 10){
                        DATA::setSession('title', DATA::getPost('title'));
                    }

                    if(DATA::isPost('description') && strlen(DATA::getPost('description')) >= 10){
                        DATA::setSession('description', DATA::getPost('description'));
                    }

                    if(DATA::isPost('text') && strlen(strip_tags(DATA::getPost('text', false))) >= 10){
                        DATA::setSession('text', DATA::getPost('text', false));
                    }

                    if(DATA::isSession('title') && DATA::isSession('description') && DATA::isSession('text')){
                        
                        if(USER::isAdmin()){ // si l'utilisateur est admin
                            
                            if(DATA::getPost('type') == 'postit') {
                                $type = 1;
                            } elseif(DATA::getPost('type') == 'annonce') {
                                $type = 2;
                            } elseif(DATA::getPost('type') == 'normal') {
                                $type = 0;
                            }

                            $login = '<strong class="text-danger">'.USER::getPseudo().'</strong>';

                        }else{ //sinon 
                        
                            $login = '<strong>'.USER::getPseudo().'</strong>';
                            $type = 0;

                        }
                        
                        $isPostLogin = 'Par '.$login.'<br />Le '.date('d/m/Y à H:i');

                        MYSQL::query('INSERT INTO topics (
                            id_forum, 
                            title, 
                            description, 
                            author, 
                            author_text, 
                            last_message, 
                            time, 
                            time_new, 
                            nbr_reads, 
                            nbr_responses, 
                            closed, 
                            type, 
                            rule, 
                            last_author
                        ) VALUES (
                            \''.DATA::getPost('id').'\',
                            \''.DATA::getPost('title').'\',
                            \''.DATA::getPost('description').'\',
                            \''.$login.'\',
                            \''.USER::getPseudo().'\',
                            \''.$isPostLogin.'\',
                            \''.time().'\',
                            \''.time().'\',
                            "0",
                            "0",
                            \''.((DATA::isPost('isClosed')) ? DATA::getPost('isClosed') : 0) .'\',
                            \''.$type.'\',
                            \''.((DATA::isPost('isRighted')) ? DATA::getPost('isRighted') : 0).'\',
                            \''.USER::getPseudo().'\'
                        )');
                        
                        $id_nouvelle_rep = MYSQL::selectOneValue('SELECT topic_id FROM topics WHERE author_text=\''.USER::getPseudo().'\' ORDER BY time DESC');   
                        
                        MYSQL::query('INSERT INTO responses (
                                topic_id, 
                                content, 
                                author, 
                                author_text, 
                                author_modif, 
                                time, 
                                date_modif, 
                                ip, 
                                first
                            ) VALUES (
                                \''.$id_nouvelle_rep.'\',
                                \''.DATA::getPost('text', false).'\',
                                \''.$login.'\',
                                \''.USER::getPseudo().'\',
                                "",
                                \''.time().'\',
                                "0",
                                \''.UTILS::getIp().'\',
                                "1"
                            )
                        ');

                        MYSQL::query('UPDATE forums SET topics_nbr = (topics_nbr + 1), last_message=\''.$isPostLogin.'\', last_author=\''.USER::getPseudo().'\' WHERE id=\''.DATA::getPost('id').'\'');
                
                        unset($_SESSION['titre']);
                        unset($_SESSION['description']);
                        unset($_SESSION['texte']);
                        

                        echo json_encode(array(
                            "message" => "Votre sujet à été créer avec succès.",
                            "id_rep" =>  $id_nouvelle_rep
                        ));

                        exit;
                    }else{
                        
                        echo json_encode(array(
                            'error' => '403',
                            'type' => 'warning',
                            'message' => 'Veuillez remplir tous les champs ou respecter le minimum requis de caractères par champs.'
                        ));
                        
                        exit;
                    }
                }
            }
        }
    }else{
        // $tplnouveau->bloc('IF_PAS_CONNECTE');
    }
?>