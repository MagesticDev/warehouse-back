<?php

    $indexForumReposiory = new IndexForumRepository;
    $indexForum = array();
    
    if(USER::isAdmin()){
        $indexForum['isAdmin'] = true;
        if(DATA::getPut()){
            $put = DATA::getPut();
            $i = 0;
            foreach($put as $key => $value){
                if($key === "addCategorie"){
                    foreach($value as $key => $value){
                        if(isset($value['addNameCategorie'])){
                            $indexForumReposiory->newCategorie($value['addNameCategorie'], $value['addRightCategorie'], $value['addDescriptionCategorie'], $value['addPositionCategorie']);
                        } 
                    }
                } else {                                                                                            
                    $indexForumReposiory->newOrder($value["id_cat"], $i);
                }
                
                $i++;
            } 
        }           
    }


    if($indexForumReposiory->getCategories()){
        foreach($indexForumReposiory->getCategories() as $catKey => $catValue){
            if($catValue['admin'] != 1 OR USER::isAdmin()) {
                $indexForum['categories'][$catKey]= $catValue;
                foreach($indexForumReposiory->getForum($catValue['id_cat']) as $forumKey => $forumValue){
                    $indexForum['categories'][$catKey]['forums'][$forumKey] = $forumValue;
                    $indexForum['categories'][$catKey]['forums'][$forumKey]['avatar'] =  UTILS::GetAvatar($forumValue['last_author']);
                    $indexForum['categories'][$catKey]['forums'][$forumKey]['url'] =  UTILS::encodeNomPage($forumValue['title']);
                }
            }
        }

        echo json_encode($indexForum);
    }
?>