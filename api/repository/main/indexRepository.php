<?php
    class MainRepository {
        public function getMain(){
            $req = MYSQL::query('SELECT * FROM main');
            if(mysqli_num_rows($req) > 0){
                return mysqli_fetch_all($req, MYSQLI_ASSOC);
            }
            return;
        }
    }
?>