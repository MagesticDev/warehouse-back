<?php
    $mainRepository = new MainRepository;
    if($mainRepository->getMain()){
        $arr = array();
        foreach($mainRepository->getMain() as $key => $value){
            switch($value['type']){
                case "title" : 
                    $arr['title'] = $value['value'];
                    $arr['titleInitial'] = UTILS::initiales($value['value']);
                break;
                case "facebook" :
                    $arr['facebook'] = $value['value'];
                    $arr['facebook_desc'] = $value['description'];
                break;
                case "twitter" :
                    $arr['twitter'] = $value['value'];
                    $arr['twitter_desc'] = $value['description'];
                break;
                case "youtube" :
                    $arr['youtube'] = $value['value'];
                    $arr['youtube_desc'] = $value['description'];
                break;
                case "dateStartWebsite" :
                    $arr['dateStartWebsite'] = $value['value'];
                break;
                case "copyright" :
                    $arr['copyright'] = $value['value'];
                break;
                case "url" :
                    $arr['url'] = $value['value'];
                break;
                case "https" :
                    $arr['https'] = $value['value'];
                break;
                case "tchatActive" :
                    $arr['tchatActive'] = $value['value'];
                break;
                case "menu" :
                    $arr['menu'] = $value['value'];
                break;
            }
        }
        
        echo json_encode($arr);
    }
?>