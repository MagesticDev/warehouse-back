<?php
    $accountRepository = new MyAccountRepository;
    $account = $accountRepository->getAccount();
    $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/UpdatePassword-'.base64_encode(DATA::getPost('password').'::'.time());
    $content = UTILS::tplMail($account['email'], USER::getPseudo(), false, $url, 'password');
    UTILS::MAIL($account['email'], 'Changement de password', $content, MAIL_HEADER); 
    echo json_encode(array(
        "type" => "updatePasswordSendEmail",
        "status" => "success",
        "enum" => "success"
    ));
    exit;
?>