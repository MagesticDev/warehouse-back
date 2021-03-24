<?php
    class UserProfile {
        public function updateSession(){
            MYSQL::query('UPDATE users SET hasOnline = true WHERE login = \''.USER::getPseudo().'\'');
        }
    }
?>