<?php
    if(USER::isConnecte()){
        $topicId = DATA::getGet('edit');
        $responseId = DATA::getGet(DATA::getGet('edit'));
        if((USER::isAdmin() OR USER::getPseudo()) && DATA::isGet('edit')) {
            $reponses = MYSQL::query('SELECT 
                responses.author_text, 
                responses.content,
                responses.topic_id,
                responses.id,
                topics.id_forum,
                topics.topic_id
                FROM responses 
                LEFT JOIN topics ON topics.topic_id = responses.topic_id
                WHERE responses.id=\''.$responseId.'\' AND topics.topic_id = \''.$topicId.'\'
            ');
            
            if(mysqli_num_rows($reponses) > 0){
               $reponses = mysqli_fetch_object($reponses);
                if(USER::isAdmin() OR $reponses->author_text == USER::getPseudo()){
                    if(USER::isAdmin()){
                        $login = '<strong class="text-danger">'.USER::getPseudo().'</strong>';
                    } else {
                        $login = '<strong>'.USER::getPseudo().'</strong>';
                    }

                    if(DATA::isPost('text')){
                        MYSQL::query('UPDATE responses SET content=\''.WYSIWYG::MICode(DATA::getPost('text', false)).'\', author_modif=\''.$login.'\', date_modif=\''.time().'\' WHERE id=\''.$responseId.'\''); 
                        header("HTTP/1.0 200 Ok");
                        exit;
                    } else {
                        $content = array(
                            "content" => $reponses->content
                        );
                        echo json_encode($content);
                    }
                }else{
                //    UTILS::notification('warning', 'Vous ne pouvez pas modifier cette réponse ...', false, true);
                //     header('location: /Forum');
                    header("HTTP/1.0 404 Not Found");
                    exit;
                }
            }else{
                // UTILS::notification('warning', 'Vous ne pouvez pas éditer une réponse inéxistante', false, true);
                // header('location: /Forum');
                header("HTTP/1.0 404 Not Found");
                exit;
            }
        }
    }else{
        header("HTTP/1.0 403 forbidden");
        exit;
    }
?>