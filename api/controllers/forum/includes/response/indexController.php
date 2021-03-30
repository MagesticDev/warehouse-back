<?php

if(DATA::isPost('text') && DATA::isPost('idSubject') && DATA::isPost('id_forum') &&  (!DATA::isPost('closed')  || USER::isAdmin())){
    
    $content = WYSIWYG::MICode(DATA::getPost('text', false));
    
    if(USER::isAdmin()){ // si l'utilisateur est admin
        $login = '<strong class="text-danger">'.USER::getPseudo().'</strong>';
    }else{ //sinon 
        $login = '<strong>'.USER::getPseudo().'</strong>';
    }
    
    $isPostLogin = 'Par '.$login.'<br />Le '.date('d/m/Y à H:i');													
    

    MYSQL::query('INSERT INTO responses (
        topic_id, 
        content, 
        author, 
        author_text, 
        time, 
        ip,
        author_modif, 
        date_modif,
        first
    ) VALUES (
        \''.DATA::getPost('idSubject').'\',
        \''.stripslashes($content).'\',
        \''.$login.'\',
        \''.USER::getPseudo().'\',
        \''.time().'\',
        \''.UTILS::getIp().'\',
        \'\',
        0,
        0
    )');				
    
    MYSQL::query('UPDATE topics SET 
        nbr_responses = (nbr_responses) + (1), 
        last_message = \''.$isPostLogin.'\',
        time_new = \''.time().'\',
        author_text = \''.USER::getPseudo().'\',
        last_author = \''.USER::getPseudo().'\'
        WHERE topic_id = \''.DATA::getPost('idSubject').'\'
    ');				
                
    MYSQL::query('UPDATE forums SET 
        nbr_responses = (nbr_responses) + (1),
        last_message = \''.$isPostLogin.'\',
        last_author = \''.USER::getPseudo().'\'
        WHERE id=\''.DATA::getPost('id_forum').'\'
    ');				
        
   
    UTILS::notification('success', 'Votre message a été envoyé avec succès. Merci pour votre contribution.', false, true);
    
    exit;
}

?>