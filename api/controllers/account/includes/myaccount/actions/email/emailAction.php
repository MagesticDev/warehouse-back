<?php
    $accountRepository = new MyAccountRepository;
    $account = $accountRepository->getAccount();
    $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/UpdateEmail-'.base64_encode($account['email'].'::'.time());
    
    $mail = new SendMail($account['email'], 'Changement de l\'adresse email', USER::getPseudo(), false, $url, 'email');
    $mail->send();

    echo json_encode(array(
        "type" => "updateEmailSendEmail",
        "status" => "success",
        "enum" => "success"
    ));
    exit;
?>