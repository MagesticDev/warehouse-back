<?php
    $tplnouveau = new Template;
    $tplnouveau->setFile('nouveau', './forum/nouveau.html');

    if(USER::isConnecte()) {
        $tplnouveau->bloc('IF_IS_CONNECTE');

        $q = MSSQL::query('SELECT G_Name FROM GuildMember WHERE Name IN (SELECT Name FROM Character WHERE AccountID=\''.USER::getPseudo().'\')');
        if(sqlsrv_num_rows($q) > 0) {
            $guildeArray = array();
            while($r = sqlsrv_fetch_object($q)) {
                $guildeArray[] = $r->G_Name;
            }
        }
        

        $guildeDroits = MYSQL::query('SELECT guilde FROM forums WHERE id=\''.DATA::getGet('id').'\' AND guilde IS NOT NULL');
        if(mysqli_num_rows($guildeDroits) > 0){
            $resultGuilde = mysqli_fetch_object($guildeDroits);
            if(!in_array($resultGuilde->guilde, $guildeArray, true)){
                UTILS::notification('warning', 'Vous ne pouvez pas créer de topic ici.', false, true);
                header('Location: /Forum');
                exit;
            } 
        }

        if(DATA::getGet('id') == 16 && !USER::isAdmin() OR DATA::getGet('id') == 10 && !USER::isAdmin()) {
            UTILS::notification('warning', 'Vous ne pouvez pas créer de topic ici.', false, true);
            header('Location: /Forum');
            exit;
        }else{
            // Anti flood
            $q = MYSQL::query('SELECT id FROM reponses WHERE auteur2=\''.USER::getPseudo().'\' AND time>'.(time()-10).' LIMIT 1');
            // Vérification du forum
            $verif_forum = MYSQL::selectOneRow('SELECT COUNT(id) AS forum_exist, titre FROM forums WHERE id="'.DATA::getGet('id').'"'); 
                        
            if(MYSQL::numRows($q) > 0){
                UTILS::notification('warning', 'Vous devez attendre 10 secondes avant de pouvoir poster un nouveau message.', false, true);
                $url = explode('/Nouveau', $_SERVER['REQUEST_URI']);
                header('location: '.$url[0]);
                exit;
            }elseif($verif_forum['forum_exist'] == 0){
                UTILS::notification('warning', 'Cette section ne semble pas exister ...', false, true);
                $url = explode('/Nouveau', $_SERVER['REQUEST_URI']);
                header('location: '.$url[0]);
                exit;
            }else{

                if(USER::isAdmin()){
                    $tplnouveau->bloc('IF_IS_CONNECTE.IF_ADMIN');
                }

                $tplnouveau->values(array(
                    'SECTION' => $verif_forum['titre'],
                    'URL' => $_SERVER['REQUEST_URI'],
                    'WYSIWYG'=> WYSIWYG::editeur('texte', DATA::getSession('texte')),
                    'TITRE' => DATA::getSession('titre'),
                    'DESCRIPTION' => DATA::getSession('description')
                ));

                if(DATA::isPost('texte')) {
                    if(DATA::isPost('titre') && strlen(DATA::getPost('titre')) >= 10){
                        DATA::setSession('titre', DATA::getPost('titre'));
                    }

                    if(DATA::isPost('description') && strlen(DATA::getPost('description')) >= 10){
                        DATA::setSession('description', DATA::getPost('description'));
                    }

                    if(DATA::isPost('texte') && strlen(strip_tags(DATA::getPost('texte', false))) >= 10){
                        DATA::setSession('texte', DATA::getPost('texte', false));
                    }

                    if(DATA::isSession('titre') && DATA::isSession('description') && DATA::isSession('texte')){
                        
                        $isVip = MSSQL::query('SELECT * FROM T_VIPList WHERE AccountID=\''.USER::getPseudo().'\' AND Date > GETDATE()');	
                        if(sqlsrv_num_rows($isVip) > 0 && !USER::isAdmin()){ // si l'utilisateur est VIP mais pas admin
                        
                            $login = '<strong class="text-warning">'.USER::getPseudo().'</strong>';
                            $type = 0;
                        
                        }else if(USER::isAdmin()){ // si l'utilisateur est admin
                            
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

                        MYSQL::query('INSERT INTO sujets (
                            id_forum, 
                            titre, 
                            description, 
                            auteur, 
                            auteur2, 
                            dernier_message, 
                            time, 
                            time2, 
                            nbr_lu, 
                            nbr_reps, 
                            ferme, 
                            type, 
                            regle, 
                            dernier_auteur
                        ) VALUES (
                            \''.DATA::getGet('id').'\',
                            \''.DATA::getPost('titre').'\',
                            \''.DATA::getPost('description').'\',
                            \''.$login.'\',
                            \''.USER::getPseudo().'\',
                            \''.$isPostLogin.'\',
                            \''.time().'\',
                            \''.time().'\',
                            "0",
                            "0",
                            \''.((DATA::isPost('isVerouiller')) ? DATA::getPost('isVerouiller') : 0) .'\',
                            \''.$type.'\',
                            \''.((DATA::isPost('isRegle')) ? DATA::getPost('isRegle') : 0).'\',
                            \''.USER::getPseudo().'\'
                        )');
                        
                        $id_nouvelle_rep = MYSQL::selectOneValue('SELECT id_sujet FROM sujets WHERE auteur2=\''.USER::getPseudo().'\' ORDER BY time DESC');   
                        
                        MYSQL::query('INSERT INTO reponses (
                                sujet_id, 
                                contenu, 
                                auteur, 
                                auteur2, 
                                auteur_modif, 
                                time, 
                                date_modif, 
                                ip, 
                                first
                            ) VALUES (
                                \''.$id_nouvelle_rep.'\',
                                \''.DATA::getPost('texte', false).'\',
                                \''.$login.'\',
                                \''.USER::getPseudo().'\',
                                "",
                                \''.time().'\',
                                "0",
                                \''.UTILS::getIp().'\',
                                "1"
                            )
                        ');

                        MYSQL::query('UPDATE forums SET nbr_sujets = (nbr_sujets) + (1), dernier_message=\''.$isPostLogin.'\', dernier_auteur=\''.USER::getPseudo().'\' WHERE id=\''.DATA::getGet('id').'\'');
                
                        unset($_SESSION['titre']);
                        unset($_SESSION['description']);
                        unset($_SESSION['texte']);
                        
                        UTILS::notification('success', 'Votre sujet à été créer avec succès.', false, true);      
                        $url = explode('/Nouveau', $_SERVER['REQUEST_URI']);
                        header('Location: '.$url[0].'/sujet-'.UTILS::encodeNomPage(DATA::getPost('titre')).'-'.$id_nouvelle_rep);
                        exit;
                    }else{
                        UTILS::notification('warning', 'Veuillez remplir tous les champs ou respecter le minimum de 10 caractères par champs.', false, true);
                        header('location: '.$_SERVER['REQUEST_URI']);
                        exit;
                    }
                }
            }
        }
    }else{
        $tplnouveau->bloc('IF_PAS_CONNECTE');
    }

    $PAGE = $tplnouveau->construire('nouveau');
    $TITRE = 'Forum - Communautés de Magestic';
    $DESCRIPTION = 'Venez discuter avec les autres joueurs de Magestic.eu';
?>