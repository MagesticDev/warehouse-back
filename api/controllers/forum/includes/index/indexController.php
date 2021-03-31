<?php

    $indexForumReposiory = new IndexForumRepository;
    $indexForum = array();
    
    if(USER::isAdmin()){
        $indexForum['isAdmin'] = true;
        include('admin/actions.php');
    }

    if($indexForumReposiory->getCategories()){
        foreach($indexForumReposiory->getCategories() as $catKey => $catValue){
           if($catValue['admin'] != 1 || USER::isAdmin()) {
                $indexForum['categories'][$catKey] = $catValue;
                if(is_array($indexForumReposiory->getForum($catValue['id_cat'])) || is_object($indexForumReposiory->getForum($catValue['id_cat']))){
                    foreach($indexForumReposiory->getForum($catValue['id_cat']) as $forumKey => $forumValue){
                        $indexForum['categories'][$catKey]['forums'][$forumKey] = $forumValue;
                        $indexForum['categories'][$catKey]['forums'][$forumKey]['avatar'] =  UTILS::GetAvatar($forumValue['last_author']);
                        $indexForum['categories'][$catKey]['forums'][$forumKey]['url'] =  UTILS::encodeNomPage($forumValue['title']);
                    }
                }
            }
        }

        echo json_encode($indexForum);
    }
?>