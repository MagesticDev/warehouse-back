<?php
if(USER::isConnecte()){
        

        if(isset($_FILES['avatar'])){
            include('upload/uploadAction.php');
        }

        if(DATA::isPost('signatureUpdate', false)){
            include('signature/signatureAction.php');
        }

        if(DATA::isPost('emailUpdate')){
            include('email/emailAction.php');
        }

        if(DATA::isPost('passwordUpdate')){
            include('password/passwordAction.php');
        }

        $expires = false;

        // Changement du password après avoir recu un mail de confirmation
        if(DATA::isGet('UpdatePassword')){
            $value = explode('::', base64_decode(DATA::getGet('password')));
            $expiration = date('Hi', time()) - date('Hi', $value[1]);
            if($expiration < 60){
                // MSSQL::query('UPDATE MEMB_INFO SET memb__pwd = \''. $value[0].'\' WHERE memb___id=\''.USER::getPseudo().'\'');
                MYSQL::query('Insert into historique (idType_Historique, isDate, memb___id, ip, isAction) VALUES (22, NOW(), "'.USER::getPseudo().'", "' . $_SERVER["REMOTE_ADDR"] . '", "Changement de password")');
                // UTILS::notification('success', 'Votre mot de passe à été mis à jour.', false, true);
                // header('location: /Compte');
                exit;
            }else{
                $expires = true;
            }
        }elseif(DATA::isGet('updateEmail')){ // Changement de l'adresse après avoir recu un mail de confirmation
            $value = explode('::', base64_decode(DATA::getGet('updateEmail')));
            $expiration = date('Hi', time()) - date('Hi', $value[1]);
            if($expiration < 60){
                // MSSQL::query('UPDATE MEMB_INFO SET mail_addr = \''. $value[0].'\' WHERE memb___id=\''.USER::getPseudo().'\'');
                // MYSQL::query('Insert into historique (idType_Historique, isDate, memb___id, ip, isAction) VALUES (22, NOW(), "'.USER::getPseudo().'", "' . $_SERVER["REMOTE_ADDR"] . '", "Changement de l\'adresse email")');
                // UTILS::notification('success', 'Votre adresse email à été mis à jour.', false, true);
                // header('location: /Compte');
                exit;
            }else{
                $expires = true;
            }
        }elseif(DATA::isGet('accountDelete')){ // suppression du compte après avoir recu un mail de confirmation
            $value = explode('::', base64_decode(DATA::getGet('accountDelete')));
            $expiration = date('Hi', time()) - date('Hi', $value[1]);
            if($expiration < 10){ // suppression du compte irréversible
                // MSSQL::query("DELETE FROM MEMB_INFO WHERE memb___id='".$value[0]."'"); 
                // // delete characters 
                // MSSQL::query("DELETE FROM Characters WHERE AccountID='".$value[0]."'"); 
                // // delete list in AccountCharacter table 
                // MSSQL::query("DELETE FROM AccountCharacter WHERE Id='".$value[0]."'"); 
                // // delete from MEMB_STAT 
                // MSSQL::query("DELETE FROM MEMB_STAT WHERE memb___id='".$value[0]."'"); 
                // MSSQL::query('DELETE FROM T_GMSystem WHERE Name = \''.$value[0].'\'');
                // MSSQL::query('DELETE FROM warehouse WHERE AccountID = \''.$value[0].'\''); 

                // MYSQL::query('Insert into historique (idType_Historique, isDate, memb___id, ip, isAction) VALUES (22, NOW(), "'.USER::getPseudo().'", "' . $_SERVER["REMOTE_ADDR"] . '", "Suppression du compte")');
                // UTILS::notification('success', 'Votre compte à été supprimé avec succès.', false, true);
                // header('location: /Deconnexion');
                exit;
            }else{
                $expires = true;
            }
        } 

        if($expires){
            // UTILS::notification('warning', 'Désolé mais le délais est dépassé.', false, true);
            // header('location: /Compte');
            exit;
        }
    
} else {
    header("HTTP/1.0 403 Forbidden");
    exit;
}
?>