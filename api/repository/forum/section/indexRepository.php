<?php
class SectionForumRepository {

    protected $id;

    public function __construct($id) {
        $this->id = $id;
    }
    
    public function hasForumExist(){
        $req = MYSQL::query('SELECT * FROM forums WHERE id=\''. $this->id.'\'');
        if(mysqli_num_rows($req) < 1){
            header("HTTP/1.0 404 Not Found");
            exit;
        } else {
            return mysqli_fetch_all($req, MYSQLI_ASSOC)[0];
        }
    }

    public function pagination(){
        return 'SELECT topic_id, id_forum FROM topics WHERE id_forum=\''. $this->$id.'\'';
    }

    public function getSubjects($pagination){
        $req =  MYSQL::query("SELECT * FROM topics 
            WHERE id_forum=\"".$this->id."\" 
            ORDER BY type DESC, time_new DESC LIMIT ".$pagination[3].",".$pagination[4].""
        );
        if(mysqli_num_rows($req) > 0){
            return mysqli_fetch_all($req, MYSQLI_ASSOC);
        }
        return null;
    }

    public function viewSubject($pseudo, $subject_id){
        $isView = MYSQL::query('SELECT * FROM topics_read WHERE memb___id=\''.$pseudo.'\' AND topic_id=\''.$subject_id.'\'');
        if(mysqli_num_rows($isView) == 1){
            return true;
        }
        return false;
    }
}
?>