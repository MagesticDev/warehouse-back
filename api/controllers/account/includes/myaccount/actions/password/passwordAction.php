<?php
    $accountRepository = new MyAccountRepository;
    $account = $accountRepository->getAccount();
    $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/UpdatePassword-'.base64_encode(DATA::getPost('password').'::'.time());
    
    $mail = new SendMail(
        $account['email'], 
        'Changement de password', 
        USER::getPseudo(),
        false, // titre template
        $url, // url de retour (page courange)
        'password' // template
    );
    
    $mail->send();
    
    echo json_encode(array(
        "type" => "updatePasswordSendEmail",
        "status" => "success",
        "enum" => "success"
    ));
    exit;
?>