<?php
    class SliderRepository {
        public function getSlider(){
            $req = MYSQL::query('SELECT * FROM slider');
            if(mysqli_num_rows($req) > 0){
                return mysqli_fetch_all($req, MYSQLI_ASSOC);
            }
            return;
        }
    }
?>