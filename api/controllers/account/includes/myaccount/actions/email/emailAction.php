<?php
    $accountRepository = new MyAccountRepository;
    $account = $accountRepository->getAccount();
    $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/UpdateEmail-'.base64_encode($account['email'].'::'.time());
    $content = UTILS::tplMail($account['email'], USER::getPseudo(), false, $url, 'email');
    UTILS::MAIL($account['email'], 'Changement de l\'adresse email', $content, MAIL_HEADER); 
    echo json_encode(array(
        "type" => "updateEmailSendEmail",
        "status" => "success",
        "enum" => "success"
    ));
    exit;
?>