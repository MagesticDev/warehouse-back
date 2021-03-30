<?php
class IndexForumRepository {
    public function getCategories(){
        $req = MYSQL::query('SELECT 
                C.id as id_cat,
                C.title,
                C.description,
                C.position,
                C.admin
                FROM categories as C
                ORDER by C.position ASC
            ');
        if(mysqli_num_rows($req) > 0){
            return mysqli_fetch_all($req, MYSQLI_ASSOC);
        }
        return;
    }

    public function newOrder($idCat, $newRank){
        MYSQL::query('UPDATE categories SET position = \''.$newRank.'\' WHERE id = \''.$idCat.'\'');
    }

    public function newCategorie($new, $isAdmin, $description, $position){
        MYSQL::query('INSERT INTO categories SET title = \''.DATA::filterPut($new).'\', description = \''.DATA::filterPut($description).'\', admin = \''.(is_numeric($isAdmin) ? 1 : 0).'\', position = \''.$position.'\'');
    }

    public function newForum($titleForum, $descriptionForum, $addPositionForum, $isAdmin){
        MYSQL::query('INSERT INTO forums 
            (
                title, 
                description, 
                rights, 
                position,
                id_cat,
                topics_nbr,
                nbr_responses,
                last_message,
                last_author,
                bg
            ) 
            VALUES 
            (
                \''.DATA::filterPut($titleForum).'\', 
                \''.DATA::filterPut($descriptionForum).'\', 
                \''.(is_numeric($isAdmin) ? 1 : 0).'\', 
                \''.is_numeric($addPositionForum).'\', 
                (SELECT MAX(id) FROM categories cust),
                0,
                0,
                "Par <strong><span class=\"admin\">\''.USER::getPseudo().'\'</span></strong><br />Le '.date('d/m/Y à H:i').'",
                "'.USER::getPseudo().'",
                "images/forum_bg.png"
            )
        ');
    }

    public function getForum($id){
        $req = MYSQL::query('SELECT
            F.id,
            F.id_cat,
            F.title,
            F.description,
            F.rights, 
            F.topics_nbr,
            F.nbr_responses, 
            F.last_message,
            F.last_author,
            F.bg AS bg 
            FROM forums AS F
            WHERE F.id_cat = \''.$id.'\'
            ORDER by F.position ASC
        ');
        if(mysqli_num_rows($req) > 0){
            return mysqli_fetch_all($req, MYSQLI_ASSOC);
        }
        return;
    }
}
?>