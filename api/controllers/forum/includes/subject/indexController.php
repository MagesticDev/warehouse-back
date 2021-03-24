<?php
    $isFermer = false;
    $isRegler = false;
    $subjectForumRepository = new SubjectForumRepository(DATA::getGet('subject'));
    $topicForum = $subjectForumRepository->getTopic();
    $topic = $topicForum;

    $topic['write'] = false;
    
    if(!USER::isAdmin() && $topicForum['right'] == 2) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    if(USER::isConnecte()){
        
        $subjectForumRepository->readTopic();
        $topic['hasConnected'] = true;
        $isConnecte = true;
        
        if($topicForum['closed'] != 1 || USER::isAdmin()) {
            $topic['write'] = true;
        }

        $topic['login'] = USER::getPseudo();
        
        if(USER::isAdmin()){
            $topic['isAdmin'] = true;
        }
    }


    $paginationClass = new Pagination;
    $pagination = $paginationClass->getPage($subjectForumRepository->pagination(), 10); // on démarre une pagination en récuperant un tableau de la fonction getPage
    $viewPagination = $paginationClass->viewPagination($pagination[0],'page',$pagination[1],$pagination[2],1); //on creer la pagination avec le tableau récupérer précédement
    $topic['pagination'] = $viewPagination;
        
	switch($topicForum['type']){
        case 2:
            $topic['announcement'] = true;
        break;
        case 1: 
            $topic['post_it'] = true;
        break;
        default: $topic['normal'] = true;
    }

    $topic['url'] = UTILS::encodeNomPage($topicForum['title']);

    $responses = $subjectForumRepository->getResponses($pagination);

    foreach($responses as $key => $value){
        $topic['responses'][$key] = $value;
        
        if($value['date_modif'] != 0) {
            $edit  = '<div class="mt-3 mb-2 ml-1">';
            $edit .= 'Edité par '.$value['author_modif'].' le '.date('d/m/Y \à H\hi', $value['date_modif']);
            $edit .= '</div>';
            $topic['responses'][$key]['editedBy'] = $edit;
        }

        if(CACHE::is('hasOnline', 60)) {
            $hasOnline = unserialize(CACHE::get('hasOnline'));
        } else {
            $hasOnline = USER::hasUserConnected();
        }

        foreach($hasOnline as $keyOnline){
            if($keyOnline === $value['author_text']) {
               $topic['responses'][$key]['online'] = true;
            }    
        }

        if(UTILS::getAdmin($value['author_text'])){
            $topic['responses'][$key]['isAdmin'] = true;
        }

        $topic['responses'][$key]['signature'] = (
        $subjectForumRepository->getSignature($value['author_text']) ? 
            $subjectForumRepository->getSignature($value['author_text']) : 
            $topic['responses'][$key]['signature'] = 'Aucune signature...'
        );
        
        
        $topic['responses'][$key]['time'] = date('d/m/Y \à H\hi', $value['time']);
        $topic['responses'][$key]['date_modif'] = date('d/m/Y \à H\hi', $value['date_modif']);

        $topic['responses'][$key]['nbr_message'] = $subjectForumRepository->nbrMessage($value['author_text']);
        $topic['responses'][$key]['avatar'] = UTILS::GetAvatar($value['author_text']);
        $topic['responses'][$key]['url'] = UTILS::encodeNomPage($value['title']);
       

    }
        
		// // if(USER::isAdmin()){
		// // 	// on vérifie que les champ requis soit bien numérique
		// // 	if(DATA::isPost('deleteAll') OR DATA::isPost('deleteReponse') OR DATA::isPost('deleteReponseOk') OR DATA::isPost('regler') OR DATA::isPost('deregler') OR DATA::isPost('verouiller') OR DATA::isPost('deverrouiller')){
		// // 	   foreach($_POST as $key => $value){
		// // 			if(!is_numeric($value)){
		// // 				UTILS::notification('danger', 'Une erreur est apparu pendant l\'action administrateur, veuiller recommencer.', false, true);
		// // 				header('Location: '.$_SERVER['REQUEST_URI']);
		// // 				exit;
		// // 			}
		// // 	   }
		// // 	}
		// // }

        // // if(DATA::isPost('texte') && ($isCheckForum->ferme != 1 || USER::isAdmin())){
            
        // //     $contenu = WYSIWYG::MICode(DATA::getPost('texte', false));
            
        // //     $isVip = MSSQL::query('SELECT * FROM T_VIPList WHERE AccountID=\''.USER::getPseudo().'\' AND Date > GETDATE()');	
        // //     if(sqlsrv_num_rows($isVip) > 0 && !USER::isAdmin()){ // si l'utilisateur est VIP mais pas admin
        // //         $login = '<strong class="text-warning">'.USER::getPseudo().'</strong>';
        // //     }else if(USER::isAdmin()){ // si l'utilisateur est admin
        // //         $login = '<strong class="text-danger">'.USER::getPseudo().'</strong>';
        // //     }else{ //sinon 
        // //         $login = '<strong>'.USER::getPseudo().'</strong>';
        // //     }
            
        // //     $isPostLogin = 'Par '.$login.'<br />Le '.date('d/m/Y à H:i');													
            

        // //     MYSQL::query('INSERT INTO reponses (
        // //         sujet_id, 
        // //         contenu, 
        // //         auteur, 
        // //         auteur2, 
        // //         time, 
        // //         ip,
        // //         auteur_modif, 
        // //         date_modif,
        // //         first
        // //     ) VALUES (
        // //         \''.$isCheckForum->id_sujet.'\',
        // //         \''.stripslashes($contenu).'\',
        // //         \''.$login.'\',
        // //         \''.USER::getPseudo().'\',
        // //         \''.time().'\',
        // //         \''.UTILS::getIp().'\',
        // //         \'\',
        // //         0,
        // //         0
        // //     )');				
            
        // //     MYSQL::query('UPDATE sujets SET 
        // //         nbr_reps = (nbr_reps) + (1), 
        // //         dernier_message = \''.$isPostLogin.'\',
        // //         time2 = \''.time().'\',
        // //         auteur2 = \''.USER::getPseudo().'\',
        // //         dernier_auteur = \''.USER::getPseudo().'\'
        // //         WHERE id_sujet = \''.$isCheckForum->id_sujet.'\'
        // //     ');				
           				
        // //     MYSQL::query('UPDATE forums SET 
        // //         nbr_reps = (nbr_reps) + (1),
        // //         dernier_message = \''.$isPostLogin.'\',
        // //         dernier_auteur = \''.USER::getPseudo().'\'
        // //         WHERE id=\''.$isCheckForum->id_forum.'\'
        // //     ');				
               
        // //     MYSQL::query('DELETE FROM sujets_lus WHERE id_sujet=\''.$isCheckForum->id_sujet.'\' AND memb___id != \''.USER::getPseudo().'\'');
        // //     UTILS::notification('success', 'Votre message a été envoyé avec succès. Merci pour votre contribution.', false, true);
        // //     header('Location: '.$_SERVER['REQUEST_URI'].'#r');
        // //     exit;
        // // }else if(DATA::isPost('deleteReponse') && USER::isAdmin()){
        // //     UTILS::Alert('danger', 'Supression d\'une réponse', 'Êtes vous sûr de vouloir supprimer cette réponse ?', $_SERVER['REQUEST_URI'], 'deleteReponseOk', DATA::getPost('deleteReponse'));
        // //     header('location: '.$_SERVER['REQUEST_URI'].'#r'.DATA::getPost('deleteReponse'));
        // //     exit;
        // // }else if(DATA::isPost('deleteReponseOk') && USER::isAdmin()){
        // //     if(is_numeric(DATA::getPost('deleteReponseOk'))){
        // //         $htmlReturn = DATA::getPost('deleteReponseOk') - 1;
        // //         MYSQL::query('DELETE FROM reponses WHERE id=\''.DATA::getPost('deleteReponseOk').'\'');
        // //         MYSQL::query('UPDATE sujets SET nbr_reps=nbr_reps-1 WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');
        // //         MYSQL::query('UPDATE forums SET nbr_reps=nbr_reps-1 WHERE id=\''.$isCheckForum->id_forum.'\'');
        // //         UTILS::notification('success', 'Le message a été supprimé.', false, true);
        // //         header('Location: '.$_SERVER['REQUEST_URI'].'#r'.$htmlReturn);
        // //         exit;
        // //     }
        // // }else if(DATA::isPost('deleteAll') && USER::isAdmin()){
        // //     UTILS::Alert('danger', 'Supression d\'un sujet', 'Êtes vous sûr de vouloir supprimer ce sujet et toutes les réponses qu\'il comporte ?', $_SERVER['REQUEST_URI'], 'deleteAllOk', DATA::getPost('deleteAll'));
        // //     header('location: '.$_SERVER['REQUEST_URI'].'#r'.DATA::getPost('deleteAll'));
        // //     exit;
        // // }else if(DATA::isPost('deleteAllOk') && USER::isAdmin()){
        // //     MYSQL::query('UPDATE forums SET nbr_reps=nbr_reps-(SELECT nbr_reps FROM sujets 
        // //         WHERE id_forum=\''.$isCheckForum->id_forum.'\' LIMIT 0,1), 
        // //         nbr_sujets=nbr_sujets-1 WHERE id=\''.$isCheckForum->id_forum.'\'
        // //     ');	
        // //     MYSQL::query('DELETE FROM sujets WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');			
        // //     MYSQL::query('DELETE FROM reponses WHERE sujet_id=\''.$isCheckForum->id_sujet.'\'');
        // //     $url = explode('/sujet', $_SERVER['REQUEST_URI']);
        // //     UTILS::notification('success', 'Sujet supprimé (réponses comprises) ...', false, true);
        // //     header('Location: '.$url[0]);
        // //     exit;
        // // }else if(DATA::isPost('regler') && USER::isAdmin()){
        // //         MYSQL::query('UPDATE sujets SET regle="1" WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');
        // //         UTILS::notification('success', 'Le sujet est désormais [réglé].', false, true);
        // //         header('Location: '.$_SERVER['REQUEST_URI']);
        // //         exit;
        // // }else if(DATA::isPost('deregler') && USER::isAdmin()) {
        // //     MYSQL::query('UPDATE sujets SET regle="0" WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');
        // //     UTILS::notification('warning', 'Le sujet est de nouveau [déréglé].', false, true);
        // //     header('Location: '.$_SERVER['REQUEST_URI']);
        // //     exit;
        // // }else if(DATA::isPost('verrouiller') && USER::isAdmin()){
        // //     MYSQL::query('UPDATE sujets SET ferme="1" WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');
        // //     UTILS::notification('warning', 'Le sujet est désormais [vérouillé].', false, true);
        // //     header('Location: '.$_SERVER['REQUEST_URI']);
        // //     exit;
        // // }else if(DATA::isPost('deverrouiller') && USER::isAdmin()) {
        // //     MYSQL::query('UPDATE sujets SET ferme="0" WHERE id_sujet=\''.$isCheckForum->id_sujet.'\'');
        // //     UTILS::notification('success', 'Le sujet est désormais [dévérouillé].', false, true);
        // //     header('Location: '.$_SERVER['REQUEST_URI']);
        // //     exit;
        // // }
	

    echo json_encode($topic);
?>