<?php
    $accountRepository = new MyAccountRepository;
    $accountRepository->updateSignature(DATA::getPost('signatureUpdate', false));
    echo json_encode(array(
        "type" => "updateSignature",
        "status" => "success",
        "enum" => "success"
    ));
    exit;
?>