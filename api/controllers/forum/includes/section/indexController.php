<?php
    $section['right_write'] = true;
    $sectionForumRepository = new SectionForumRepository(DATA::getGet('section'));
    $sectionForum = $sectionForumRepository->hasForumExist();
    $section = $sectionForum;

    if(!USER::isAdmin() && $sectionForum['id'] == '10'):
        exit;
    endif;
    
    if(!USER::isAdmin() && $sectionForum['rights'] > 0) {
        switch($sectionForum['rights']){
            case 2:
                header("HTTP/1.0 404 Not Found");
                exit;
            break;
            case 1:
                $section['right_write'] = false;
            break;
        }
    }

    if(USER::isConnecte()){
        $section['hasConnected'] = true;
        $isConnecte = true;

        if(USER::isAdmin()){
            $section['isAdmin'] = true;
        }
    }

    $section['url'] =  UTILS::encodeNomPage($sectionForum['title']);
    $paginationClass = new Pagination;

    $pagination = $paginationClass->getPage($sectionForumRepository->pagination(), 10); // on démarre une pagination en récuperant un tableau de la fonction getPage
    $viewPagination = $paginationClass->viewPagination($pagination[0],'page',$pagination[1],$pagination[2],1); //on creer la pagination avec le tableau récupérer précédement
    $section['pagination'] = $viewPagination;
    $subjects = $sectionForumRepository->getSubjects($pagination);

    foreach($subjects as $key => $value){
        $section['subjects'][$key] = $value;
        switch($value['type']){
            case 2:
               $section['subjects'][$key]['announcement'] = true;
            break;
            case 1: 
                $section['subjects'][$key]['post_it'] = true;
            break;
            default: $section['subjects'][$key]['normal'] = true;
        }

        if(isset($isConnecte)){
            $section['subjects'][$key]['isView'] =  $sectionForumRepository->viewSubject(USER::getPseudo(), $value['topic_id']);
        }

        $section['subjects'][$key]['url'] =  UTILS::encodeNomPage($value['title']);
        $section['subjects'][$key]['avatar'] =  UTILS::GetAvatar($value['last_author']);
        
    }
    
    unset($section['rights'], $section['bg']);
    echo json_encode($section);
?>