<?php
    
    $tchatRepository = new TchatRepository;
    $getMessages = $tchatRepository->getMessages();
    foreach($getMessages as $key => $value){
        $messages[$key] = $value;
        $messages[$key]['date'] =  date('d/m/Y à H:i', $value['heure']);
        $messages[$key]['avatar'] = UTILS::GetAvatar($value['pseudo']);
        unset($messages[$key]['heure'], $messages[$key]['ID']);
    }
    echo json_encode($messages);
?> 