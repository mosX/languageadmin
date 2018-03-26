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
                    "SELECT COUNT(`question_collections`.`id`)"
                    . " FROM `question_collections`"
                    . " WHERE `question_collections`.`lesson_id` = ".(int)$lesson_id
                );
        $this->m->total = $this->m->_db->loadResult(); 
        
        $xNav = new xNav("/lessons/question_collections/".$this->m->_path[2], $this->m->total, "GET");
        $xNav->limit = 50;
        $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `question_collections`.* "
                    . " , `questions`.`value`"
                    . " , `questions`.`correct`"
                    . " , `questions`.`score`"
                    . " , `questions`.`type`"
                    . " , `answers`.`text` as answer"
                    . " , `images`.`filename`"
                    . " , `audios`.`filename` as audio"
                    . " , `audios`.`description`"
                    . " , (SELECT COUNT(`answer_collections`.`id`) FROM `answer_collections` WHERE `answer_collections`.`question_id` = `questions`.`id`) as answers"
                    . " FROM `question_collections`"
                    . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                    . " LEFT JOIN `answer_collections` ON `answer_collections`.`id` = `questions`.`correct`"
                    . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                    . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                    . " LEFT JOIN `audios` ON `audios`.`id` = `questions`.`audio_id`"
                    . " WHERE `question_collections`.`lesson_id` = ".(int)$lesson_id
                    . " ORDER BY `questions`.`id` DESC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
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
    
    public function removeGiven($id,$question_id=null){
        //if(!(int)$id) return false;
        if($id){
            //получаем айди вопроса
            $this->m->_db->setQuery(
                        "SELECT `question_collections`.* "
                        . " FROM `question_collections` "
                        . " WHERE `question_collections`.`id` = ".(int)$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($collection);
            
            $question_id = $collection->question_id;
        }
        
        //получаем коллекции ответов и ответы по данному вопросу
        $this->m->_db->setQuery(
                    "SELECT `answer_collections`.* "
                    . " FROM `answer_collections`"                    
                    . " WHERE `answer_collections`.`question_id` = ".$question_id
                );
        $answers = $this->m->_db->loadObjectList();
        
        foreach($answers as $item){
            $this->m->_db->setQuery(    
                "DELETE FROM `answer_collections` "
                . " WHERE `answer_collections`.`id` = ".$item->id
                . " LIMIT 1"
            );
            $this->m->_db->query();
            
            $this->m->_db->setQuery(    
                "DELETE FROM `answers` "
                . " WHERE `answers`.`id` = ".$item->answer_id
                . " LIMIT 1"
            );
            $this->m->_db->query();
        }
        
        $this->m->_db->setQuery(    
            "DELETE FROM `questions` "
            . " WHERE `questions`.`id` = ".$question_id
            . " LIMIT 1"
        );
        $this->m->_db->query();
                
        if($id){
            $this->m->_db->setQuery(
                "DELETE FROM `question_collections` "
                . " WHERE `question_collections`.`id` = ".$id
                . " LIMIT 1"
            );
        }else if($question_id){
            $this->m->_db->setQuery(
                "DELETE FROM `question_collections` "
                . " WHERE `question_collections`.`question_id` = ".$id
                //. " LIMIT 1"
            );
        }
        return $this->m->_db->query() ? true: false;
    }
    public function getGivenLesson($lesson_id,$ids = null){
        $this->m->_db->setQuery(
                    "SELECT `question_collections`.* "
                    . " , `questions`.`value`"
                    . " , `questions`.`correct`"
                    . " , `questions`.`type`"
                    . " , `audios`.`filename` as audio"
                    . " FROM `question_collections`"
                    . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                    . " LEFt JOIN `audios` ON `audios`.`id` = `questions`.`audio_id`"
                    . " WHERE `question_collections`.`lesson_id` = ".(int)$lesson_id
                    . ($ids ? " AND `questions`.`id` IN (".implode(',',$ids).")" :"")
                    . " AND `questions`.`status` = 1"
                );

        $data = $this->m->_db->loadObjectList('question_id');        
        
        return $data;
    }

}
?>