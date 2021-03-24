<?php
$tplM = new Template();
$tplM->setFile('modifier', 'forum/modifier.html');
    if(USER::isConnecte()){
        $tplM->bloc('IF_IS_CONNECTE');
        if((USER::isAdmin() OR USER::getPseudo()) && DATA::isGet('id')) {
            
            $reponses = MYSQL::query('SELECT 
                reponses.auteur2 as auteur, 
                reponses.contenu as contenu,
                reponses.sujet_id as sujet_id,
                reponses.id as reponses_id,
                sujets.id_forum as id_forum,
                sujets.id_sujet as id_sujet
                FROM reponses 
                LEFT JOIN sujets ON sujets.id_sujet = reponses.sujet_id
                WHERE reponses.id=\''.DATA::getGet('id').'\'
            ');
            
            if(mysqli_num_rows($reponses) > 0){
                $reponses = mysqli_fetch_object($reponses);
                if(USER::isAdmin() OR $reponses->auteur == USER::getPseudo()){
                    $url = explode('/modifier', $_SERVER['REQUEST_URI']);
                    $tplM->values(array(
                        'URL_RETOUR' => $url[0],
                        'WYSIWYG' => WYSIWYG::editeur('texte', $reponses->contenu),
                        'URL_UPDATE' => $_SERVER['REQUEST_URI']
                    ));

                    if(USER::isAdmin()){
                        $login = '<strong class="text-danger">'.USER::getPseudo().'</strong>';
                    } else {
                        $login = '<strong>'.USER::getPseudo().'</strong>';
                    }

                    if(DATA::isPost('texte')){
                        MYSQL::query('UPDATE reponses SET contenu=\''.WYSIWYG::MICode(DATA::getPost('texte', false)).'\', auteur_modif=\''.$login.'\', date_modif=\''.time().'\' WHERE id=\''.DATA::getGet('id').'\''); 
                        UTILS::notification('success', 'Votre message a été modifié avec succès.', false, true);
                        header('Location: '. $url[0].'#r'.DATA::getGet('id'));
                        exit;
                    }
                }else{
                   UTILS::notification('warning', 'Vous ne pouvez pas modifier cette réponse ...', false, true);
                    header('location: /Forum');
                    exit;
                }
            }else{
                UTILS::notification('warning', 'Vous ne pouvez pas éditer une réponse inéxistante', false, true);
                header('location: /Forum');
                exit;
            }
        }
    }else{
        $tplM->bloc('IF_PAS_CONNECTE');
    }

$PAGE = $tplM->construire('modifier');
$DESCRIPTION = $TITRE = 'Modifier une réponse';
?>