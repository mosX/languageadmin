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
    }
?>