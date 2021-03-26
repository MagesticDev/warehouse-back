<?php

    function randomName() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $name = array(); //remember to declare $name as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $name[] = $alphabet[$n];
        }
        return implode($name); //turn the array into a string
    }

    $handle = new upload($_FILES['upload'], 'fr_FR');
    if($handle->uploaded) {
        $handle->file_max_size = '1000000';
        /*
        $handle->image_resize  = true;
        $handle->image_ratio_y = true;
        $handle->image_x = 200;*/
        //$handle->image_max_width = 400;
        //$handle->image_max_height = 400;
        $newName = randomName().md5($_FILES['upload']['name']);
        $handle->file_new_name_body  = $newName;
        $handle->file_overwrite = true;
        $handle->file_auto_rename = false;
        $handle->allowed = array('image/*');
        $handle->file_new_name_ext = 'png';
        $handle->process('includes/assets/images/forum');

        // si l'upload est good
        if ($handle->processed) {
            $handle-> clean();
            echo json_encode(array(
                "uploaded" => 1,
                "fileName" => $_FILES['upload']['name'],
                "url" => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/includes/assets/images/forum/'.$newName.'.png'
            ));
            exit;
        } else {
            echo json_encode(array(
                "uploaded" => 0,
                "fileName" => $_FILES['upload']['name'],
                "url" => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/includes/assets/images/forum/'.$newName.'.png',
                "error"=> array(
                    "message" => $handle->error
                )
            ));
            exit;
        }
    }
?>