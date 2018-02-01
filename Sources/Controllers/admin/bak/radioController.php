<?php
    class radioController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        
        public function indexAction(){
            $this->m->_db->setQuery(
                        "SELECT `radio`.* "
                        . " FROM `radio`"
                        . " WHERE `radio`.`status` = 1"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
    }
?>