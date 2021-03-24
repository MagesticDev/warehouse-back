<?php
if(USER::isConnecte()){
    $accountRepository = new MyAccountRepository;
    require_once('actions/actions.php');
    $array = array();
    foreach($accountRepository->getAccount() as $key => $value){
        if($key != "recovery" AND $key != "password" AND $key != "question" AND $key != "response"){
            $array[$key] = $value;
        }
    }
    $array['avatar'] = UTILS::GetAvatar(USER::getPseudo());
    echo json_encode($array);
}else{ 
    include('../disconnect/indexController.php');
    header("HTTP/1.0 403 Forbidden");
    exit;
}
?>