<?php
if(DATA::getPut()){
    $put = DATA::getPut();
    $i = 0;
    foreach($put as $key => $value){
        if($key === "addCategorie"){
            foreach($value as $key => $value){
                if(isset($value['addNameCategorie']) && isset($value['forums']) && isset($value['addDescriptionCategorie'])){
                    $indexForumReposiory->newCategorie($value['addNameCategorie'], $value['addRightCategorie'], $value['addDescriptionCategorie'], $value['addPositionCategorie']);
                    foreach($value['forums'] as $keyForum => $valueForum){
                        $indexForumReposiory->newForum($valueForum['titleForum'], $valueForum['descriptionForum'], $keyForum, $value['addRightCategorie']);
                    }
                } 
            }
        } else {                                                                                            
            $indexForumReposiory->newOrder($value["id_cat"], $i);
        }
        $i++;
    } 
}           
?>