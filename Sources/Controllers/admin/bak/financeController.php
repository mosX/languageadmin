<?php
    class financeController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
  
        public function paymentsAction(){
            $this->m->_db->setQuery(
                        "SELECT `deposits`.* "
                        . " FROM `deposits`"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
    }
?>