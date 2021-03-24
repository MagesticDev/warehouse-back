<?php
class RegisterRepository {
    public function getExistingAccount($pseudo, $email){
        $req = MYSQL::query('SELECT login, email FROM users WHERE login=\''.$pseudo.'\' OR email=\''.$email.'\'');
        if(mysqli_num_rows($req) > 0){
            return mysqli_fetch_all($req, MYSQLI_ASSOC)[0];
        }
        return;
    }

    public function register($pseudo, $email, $question, $response, $password){
        MYSQL::query('INSERT INTO users (
                login,		
                email,	
                country,		
                question,
                response, 
                password
            ) VALUES (
                \''.$pseudo.'\',	
                \''.$email.'\', 
                NOW(),	
                \''.$question.'\', 
                \''.$response.'\', 
                \''.md5($password).'\'
            )
        ');

        MYSQL::query('Insert into historique (idType_Historique, date, memb___id, ip, action) VALUES (21, NOW(), "'.$pseudo.'", "' . $_SERVER["REMOTE_ADDR"] . '", "Nouveau membre")');
	}
}
?>