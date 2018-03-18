<?php
class Questions{
    protected $_table = 'questions';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function editNormal($answers,$question_id){
        foreach($answers as $item){     //добавляем вопросы
            switch($item['act']){
                case 'update':
                        //нужно получить ансвет айди по колекшн айди
                        $this->m->_db->setQuery(
                                    "SELECT `answer_collections`.`answer_id` as id"
                                    . " FROM `answer_collections`"
                                    . " WHERE `answer_collections`.`id` = ".(int)$item['id']
                                    . " LIMIT 1"
                                );
                        $answer_id = $this->m->_db->loadResult();

                        if($answer_id){
                            $this->m->_db->setQuery(
                                    "UPDATE `answers` SET `answers`.`text` = '".$item['value']."'"
                                    . " WHERE `answers`.`id` = ".(int)$answer_id
                                    . " LIMIT 1"
                                );
                            $this->m->_db->query();
                        }
                        if($item['correct'])$correct = $item['id'];                                        
                    break;
                case 'insert':
                        $answer = new stdClass();
                        $answer->text = $item['value'];
                        $answer->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('answers',$answer,'id');

                        $collection = new stdClass();
                        $collection->answer_id = $answer->id;
                        $collection->question_id = $question_id;
                        $this->m->_db->insertObject('answer_collections',$collection,'id');

                        if($item['correct'])$correct = $collection->id;
                    break;
                case 'delete':
                        if(!$item['id']) break;
                        
                        $this->deleteAnswer($item['id']);
                        
                        

                    break;
            }
        }
        
        $this->updateCorrect($question_id,$correct);
    }
    
    public function deleteAnswer($collection_id,$answer_id=null){
        if(!$answer_id){    //если нет ансвер айди то получаем его        
            if(!$collection_id) return false;

            $this->m->_db->setQuery(    //получаем айдишник ответа  
                        "SELECT `answer_collections`.* FROM `answer_collections`"
                        . " WHERE `answer_collections`.`id` = ".$collection_id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($collection);
            
            $answer_id = $collection->answer_id;
            
            $this->m->_db->setQuery(    //удаляем коллекцию
                        "DELETE FROM `answer_collections` "
                        . " WHERE `answer_collections`.`id` = ".(int)$collection_id
                        . " LIMIT 1"
                    );
            $this->m->_db->query();
        }
        
        $this->m->_db->setQuery(    //удаляем коллекцию
                    "DELETE FROM `answers` "
                    . " WHERE `answers`.`id` = ".(int)$answer_id
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            return true;
        }else{
            return false;
        }
    }
    
    public function editImage($answers,$question_id){
        foreach($answers as $item){     //добавляем вопросы
            switch($item['act']){
                case 'update':
                        //нужно получить ансвет айди по колекшн айди
                        $this->m->_db->setQuery(
                                    "SELECT `answer_collections`.`answer_id` as id"
                                    . " FROM `answer_collections`"
                                    . " WHERE `answer_collections`.`id` = ".(int)$item['id']
                                    . " LIMIT 1"
                                );
                        $answer_id = $this->m->_db->loadResult();
                        if($answer_id){
                            $this->m->_db->setQuery(
                                    "UPDATE `answers` SET `answers`.`image_id` = '".$item['value']."'"
                                    . " WHERE `answers`.`id` = ".(int)$answer_id
                                    . " LIMIT 1"
                                );
                            $this->m->_db->query();
                        }
                        
                        if($item['correct'])$correct = $item['id'];                                        
                        
                    break;
                case 'insert':
                        $answer = new stdClass();
                        $answer->image_id = (int)$item['value'];
                        $answer->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('answers',$answer,'id');

                        $collection = new stdClass();
                        $collection->answer_id = $answer->id;
                        $collection->question_id = $question_id;

                        $this->m->_db->insertObject('answer_collections',$collection,'id');

                        if($item['correct'])$correct = $collection->id;
                    break;
                case 'delete':
                        $this->m->_db->setQuery(
                                    "DELETE FROM `answer_collections` WHERE `answer_collections`.`id` = ".(int)$item['id']
                                    . " LIMIT 1"
                                );
                        $this->m->_db->query();

                        if($item['correct'])$correct = '';
                    break;
            }
        }
        
        $this->updateCorrect($question_id,$correct);
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
    
    public function updateMain($id,$value,$score,$audio_id){      //обновляем основный параметры вопроса
        $this->m->_db->setQuery(
                    "UPDATE `questions` "
                    . " SET `questions`.`value` = ".$this->m->_db->Quote($value)
                    . " , `questions`.`score` = '".(int)$score."'"
                    . " , `questions`.`audio_id` = ".(int)$audio_id
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
                    "SELECT COUNT(`questions`.`id`)"
                    . " FROM `questions`"
                    . " WHERE `questions`.`status` = 1"
                );
        $this->m->total = $this->m->_db->loadResult(); 
        
        $xNav = new xNav("/lessons/questions/", $this->m->total, "GET");
        $xNav->limit = 50;
        $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `questions`.* "
                    . " , COUNT(`answer_collections`.`id`) as answers"
                    . " , (SELECT COUNT(`question_collections`.`id`) FROM `question_collections` WHERE `question_collections`.`question_id` = `questions`.`id`) as lessons"
                    //. " , `audios`.`description`"
                    //. " , `audios`.`filename`"
                    . " FROM `questions` "
                    . " LEFT JOIN `answer_collections` ON `answer_collections`.`question_id` = `questions`.`id`"
                    //. " LEFT JOIN `audios` ON `audios`.`id` = `questions`.`audio_id` "
                    . " WHERE `questions`.`status` = 1"
                    . " GROUP by `questions`.`id`"
                    . " ORDER BY `questions`.`id` DESC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        $data = $this->m->_db->loadObjectList();
        //p($data);
        return $data;
    }
    
    public function getGiven($id){
        $this->m->_db->setQuery(
                    "SELECT `questions`.* "
                    . " , `audios`.`filename`"
                    . " , `audios`.`description` as audio_description"
                    . " FROM `questions` "
                    . " LEFT JOIN `audios` ON `audios`.`id` = `questions`.`audio_id`"
                    . " WHERE `questions`.`id` = ".$id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($data);
        
        return $data;
    }
}
?>