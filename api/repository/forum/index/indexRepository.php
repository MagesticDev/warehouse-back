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