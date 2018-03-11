<?php
class QuestionCollections{
    protected $_table = 'question_collections';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function getGiven($id,$question_id=null,$lesson_id=null){
        //if(!$id) return false;
        $this->m->_db->setQuery(
                    "SELECT `question_collections`.* "
                    . " FROM `question_collections` "
                    //. " WHERE `question_collections`.`id` = ".(int)$id 
                    . " WHERE 1"
                    . ($id ? " AND `question_collections`.`id` = ".(int)$id :"")
                    . ($question_id ? " `question_collections`.`question_id` = ".$question_id :"")
                    . ($lesson_id ? " AND `question_collections`.`lesson_id` = ".$lesson_id :"")
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($question);

        return $question;
    }
    
    public function getData($lesson_id){
        $this->m->_db->setQuery(
                    "SELECT `question_collections`.* "
                    . " , `questions`.`value`"
                    . " , `questions`.`correct`"
                    . " , `questions`.`score`"
                    . " , `questions`.`type`"
                    . " , `answers`.`text` as answer"
                    . " , `images`.`filename`"
                    . " FROM `question_collections`"
                    . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                    . " LEFT JOIN `answer_collections` ON `answer_collections`.`id` = `questions`.`correct`"

                    . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                    . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                    . " WHERE `question_collections`.`lesson_id` = ".(int)$lesson_id
                    . " ORDER BY `id` DESC"
                );
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    public function addNew($row){
        if($this->m->_db->insertObject('question_collections',$row,'id')){
            return true;
        }else{
            return false;
        }
    }
    
    public function updatePublishing($id,$result){
        $question = $this->getGiven($id);
        if(!$question){
            return false;
        }
        
        $result->published = $question->published ? 0: 1;
        
        $this->m->_db->setQuery(
                "UPDATE `question_collections` SET `question_collections`.`published` = ".$result->published
                . " WHERE `question_collections`.`id` = ".$id
                . " LIMIT 1"
            );
        if($this->m->_db->query()){
            return true;
        }else{
            return false;
        }
    }
    
    public function removeGiven($id){
        if(!(int)$id) return false;
        
        $this->m->_db->setQuery(
                "DELETE FROM `question_collections` "
                . " WHERE `question_collections`.`id` = ".$id
                . " LIMIT 1"
            );
        return $this->m->_db->query() ? true: false;
    }
    public function getGivenLesson($lesson_id){
        $this->m->_db->setQuery(
                    "SELECT `question_collections`.* "
                    . " , `questions`.`value`"
                    . " , `questions`.`correct`"
                    . " , `questions`.`type`"
                    . " FROM `question_collections`"
                    . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                    . " WHERE `question_collections`.`lesson_id` = ".(int)$lesson_id
                );

        $data = $this->m->_db->loadObjectList('question_id');        
        
        return $data;
    }

}
?>