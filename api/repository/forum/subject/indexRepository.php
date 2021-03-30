<?php
class SubjectForumRepository {

    private $id = null;

    public function __construct($id) {
        $this->id = $id;
    }

    public function getTopic(){
        $req = MYSQL::query('SELECT 
            topics.title as title_topics, 
            topics.description, 
            topics.topic_id,
            topics.type,
            topics.closed,
            topics.rule,
            forums.title,
            forums.id,
            forums.rights
            FROM topics 
            RIGHT JOIN forums ON topics.id_forum = forums.id WHERE topics.topic_id=\''.$this->id.'\''
        );
        if(mysqli_num_rows($req) > 0){
            MYSQL::query('UPDATE topics SET nbr_reads= (nbr_reads) + (1) WHERE topic_id=\''.$this->id.'\'');
            return mysqli_fetch_all($req, MYSQLI_ASSOC)[0];
        } else {
            
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }
 
    public function readTopic(){
        $topicRead = MYSQL::selectOneValue('SELECT count(*) FROM topics_read WHERE memb___id=\''.USER::getPseudo().'\' AND topic_id=\''.$this->id.'\'');		
        if($topicRead == 0) {
            MYSQL::query('INSERT INTO topics_read VALUES (\''.$this->id.'\', \''.USER::getPseudo().'\', \''.USER::getIp().'\')');	
        }
    }

    public function pagination(){
        return 'SELECT id FROM responses WHERE topic_id=\''. $this->id.'\'';
    }

    public function getResponses($pagination){
        $req = MYSQL::query('SELECT
            T.topic_id, 
            T.id_forum, 
            T.title, 
            T.description, 
            T.author, 
            T.author_text,
            T.last_author, 
            T.time, 
            T.rule, 
            T.closed, 
            T.type, 
            R.id, 
            R.topic_id, 
            R.content, 
            R.author, 
            R.author_text, 
            R.author_modif, 
            R.time, 
            R.date_modif, 
            R.ip, 
            R.first
            FROM topics AS T
            RIGHT JOIN responses AS R ON T.topic_id = R.topic_id
            WHERE T.topic_id=\''.$this->id.'\'
            ORDER BY R.time ASC
            LIMIT '.$pagination[3].','.$pagination[4].''
        );

        return mysqli_fetch_all($req, MYSQLI_ASSOC);
    }

    public function getSignature($author){
        $req = MYSQL::query('SELECT signature FROM users WHERE login=\''.$author.'\'');
        $result = mysqli_fetch_object($req);
        return $result->signature;
    }

    public function nbrMessage($author){
        return MYSQL::selectOneValue('SELECT COUNT(author_text) FROM responses WHERE author_text=\''.$author.'\'');
    }
}
?>