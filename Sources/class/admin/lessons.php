<?php
class Lessons{
    protected $_table = 'lessons';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function updateMain($id,$row){
        $this->m->_db->setQuery(
                    "UPDATE `lessons` SET `lessons`.`name` = '".$row->name."'"
                    . " , `lessons`.`show_answers` = '".(int)$row->show_answers."'"
                    . " , `lessons`.`description` = '".$row->description."'"
                    . ($row->terms ? " , `lessons`.`terms` = '".$row->terms."'" : '')
                    . " WHERE `lessons`.`id` = ".(int)$id
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            echo '{"status":"success"}';
        }else{
            echo '{"status":"error"}';
        }
    }
    
    public function addNew($row){
        if($this->m->_db->insertObject('lessons',$row)){
            return true;
        }else{
            return false;
        }
    }
    
    public function getData(){
        $this->m->_db->setQuery(
                    "SELECT `lessons`.* "
                    . " FROM `lessons`"
                    . " WHERE `lessons`.`status` = 1"
                );
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    public function getGiven($id){
        $this->m->_db->setQuery(
                    "SELECT `lessons`.* "
                    . " FROM `lessons` "
                    . " WHERE `lessons`.`id` = ".(int)$id 
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($data);
        
        return $data;
    }
    
    public function updatePublishing($id,$result){
        $lesson = $this->getGiven($id);
        if(!$lesson){
            return false;
        }
        
        $result->published = $lesson->published ? 0: 1;
        
        
        $this->m->_db->setQuery(
                "UPDATE `lessons` SET `lessons`.`published` = ".$result->published
                . " WHERE `lessons`.`id` = ".$id
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
                    "UPDATE `lessons` SET `lessons`.`status` = 0"
                    . " WHERE `lessons`.`id` = ".(int)$id
                    . " LIMIT 1"
                );
        return $this->m->_db->query() ? true: false;
    }
}
?>