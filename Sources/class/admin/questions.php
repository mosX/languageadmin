<?php
class Questions{
    protected $_table = 'questions';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function addNormal($answers,$question_id){
        foreach($answers as $item){     //добавляем вопросы
            if($item['act'] == 'insert'){   //добавляем новый
                $answer = new stdClass();
                $answer->text = $item['value'];
                $answer->date = date("Y-m-d H:i:s");
                $this->m->_db->insertObject('answers',$answer,'id');

                $collection = new stdClass();
                $collection->answer_id = $answer->id;
                $collection->question_id = $question_id;

                $this->m->_db->insertObject('answer_collections',$collection,'id');

                if($item['correct'])$correct = $collection->id;
            }
        }

        $this->updateCorrect($question_id,$correct);
    }
    
    public function addImage($answers,$question_id){
        foreach($answers as $item){     //добавляем вопросы
            if($item['act'] == 'insert'){   //добавляем новый
                $answer = new stdClass();
                $answer->image_id = (int)$item['value'];
                $answer->date = date("Y-m-d H:i:s");
                $this->m->_db->insertObject('answers',$answer,'id');

                $collection = new stdClass();
                $collection->answer_id = $answer->id;
                $collection->question_id = $question_id;

                $this->m->_db->insertObject('answer_collections',$collection,'id');

                if($item['correct'])$correct = $collection->id;
            }
        }

        $this->updateCorrect($question_id, $correct);
    }
    
    public function updateMain($id,$value,$score){      //обновляем основный параметры вопроса
        $this->m->_db->setQuery(
                    "UPDATE `questions` "
                    . " SET `questions`.`value` = ".$this->m->_db->Quote($value)
                    . " , `questions`.`score` = '".(int)$score."'"
                    . " WHERE `questions`.`id` = ".(int)$id
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            return true;
        }else{
            return false;
        }
    }
    
    public function updateCorrect($id,$correct){    //обновляем правильный ответ
        if(!$id) return false;
        if(!$correct) return false;
        
        $this->m->_db->setQuery(
                    "UPDATE `questions` SET `questions`.`correct` = ".(int)$correct
                    . " WHERE `questions`.`id` = ".$id
                    . " LIMIT 1"
                );
         return $this->m->_db->query() ?true:false;
    }
    
    public function addNew($data){
        if($this->m->_db->insertObject('questions',$data,'id')){
            return true;
        }else{
            return false;
        }        
    }
    
    public function getList(){
        //получаем все вопросы для селекта
        $this->m->_db->setQuery(
                    "SELECT `questions`.* "
                     . " FROM `questions` "
                    . " WHERE `questions`.`status` = 1"
                    . " ORDER BY `id` DESC"
                );
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    public function getData(){
        $this->m->_db->setQuery(
                    "SELECT `questions`.* "
                    . " , COUNT(`answer_collections`.`id`) as answers"
                    . " , COUNT(`question_collections`.`id`) as lessons"
                    . " FROM `questions` "
                    . " LEFT JOIN `answer_collections` ON `answer_collections`.`question_id` = `questions`.`id`"
                    . " LEFT JOIN `question_collections` ON `question_collections`.`question_id` = `questions`.`id`"
                    . " WHERE `questions`.`status` = 1"
                    . " GROUP by `questions`.`id`"
                    . " ORDER BY `id` DESC"
                );
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    public function getGiven($id){
        $this->m->_db->setQuery(
                    "SELECT `questions`.* "
                    . " FROM `questions` "
                    . " WHERE `questions`.`id` = ".$id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($data);
        
        return $data;
    }
}
?>