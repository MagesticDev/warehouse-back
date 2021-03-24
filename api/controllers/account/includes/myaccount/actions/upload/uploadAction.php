<?php
    /* INSTALLER L\'extension PHP -> GD pour le redimenssionnement */
    // upload du nouvelle avatar
    $handle = new upload($_FILES['avatar'], 'fr_FR');
    if($handle->uploaded) {
        $handle->file_max_size = '1000000';
        /*
        $handle->image_resize  = true;
        $handle->image_ratio_y = true;
        $handle->image_x = 200;*/
        $handle->image_max_width = 400;
        $handle->image_max_height = 400;

        $handle->file_new_name_body   = USER::getPseudo();
        $handle->file_overwrite = true;
        $handle->file_auto_rename = false;
        $handle->allowed = array('image/*');
        $handle->file_new_name_ext = 'png';
        $handle->process('includes/assets/images/avatars');

        // si l'upload est good
        if ($handle->processed) {
            $handle-> clean();
            echo json_encode(array(
                'url' => UTILS::GetAvatar(USER::getPseudo()), 
                'status' => 'success')
            );
            exit;
        } else {
            echo json_encode(array(
                'url' => UTILS::GetAvatar(USER::getPseudo()), 
                'status' => 'error',
                'dump' => $handle->error)
            );
            exit;
        }
    }
?>