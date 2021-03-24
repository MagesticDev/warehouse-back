<?php
	class Repository {
        public function getRepository($folder, $get){
            if(file_exists(dirname(dirname(__FILE__)).'/api/repository/'.$folder.'/'.$get.'/indexRepository.php')){
                include(dirname(dirname(__FILE__)).'/api/repository/'.$folder.'/'.$get.'/indexRepository.php');
            }
        }
    }
?>