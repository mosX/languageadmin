<?php
    class feedbackController extends Model {
        public function init(){
            
        }
        
        public function indexAction(){
            $this->m->_db->setQuery(
                        "SELECT `feedback`.* "
                        . " FROM `feedback` "
                        . " WHERE `feedback`.`status` = 1"
                        . " ORDER BY `id` DESC"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        public function deleteAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "UPDATE `feedback` SET `feedback`.`status` = 0"
                        . " WHERE `feedback`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
    }
?>