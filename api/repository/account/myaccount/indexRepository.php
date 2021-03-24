<?php
class MyAccountRepository {
    public function getAccount(){
        $req = MYSQL::query('SELECT * FROM users WHERE login = \''.USER::getPseudo().'\'');
        if(mysqli_num_rows($req) > 0){
            return mysqli_fetch_all($req, MYSQLI_ASSOC)[0];
        } else {
            header("HTTP/1.0 403 Forbidden");
        }
        return;
    }

    public function updateSignature($content) {
        MYSQL::query('UPDATE users SET signature = \''.$content.'\' WHERE login = \''.USER::getPseudo().'\'');
    }
}
?>