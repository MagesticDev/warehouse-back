<?php
    class TchatRepository {
        public function getMessages(){
            $req = MYSQL::query('(SELECT DISTINCT ID, message, heure, pseudo FROM chat ORDER BY heure DESC LIMIT 25) ORDER BY ID ASC');
            if(mysqli_num_rows($req) > 0){
                return mysqli_fetch_all($req, MYSQLI_ASSOC);
            }
            return null;
        }
    }
?>