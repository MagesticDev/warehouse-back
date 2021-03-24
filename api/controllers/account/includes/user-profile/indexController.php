<?php
    if(DATA::getSession('token') === DATA::getGet(DATA::getGet('user-profile'))){
        DATA::setSession('frontToken', DATA::getGet(DATA::getGet('user-profile')));
        $userProfileRepository = new UserProfile;
        $userProfileRepository->updateSession();
        $arr = array(
            "message" => "Successful login.",
            "jwt" => DATA::getSession('token'),
            "id" => DATA::getSession('id'),
            "login" => DATA::getSession('login'),
            "email" => DATA::getSession('email'),
            "expireAt" => API['EXPIRE_CLAIM'],
            "avatar" => UTILS::GetAvatar(DATA::getSession('login'))
        );

       
        echo json_encode($arr);
    } else {
        include('../disconnect/indexController.php');
    }
?>