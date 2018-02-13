<?php
    class tasktableController extends Model {
        public function init(){
            /*$this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');*/
            $this->m->addCSS('calendar');
            $this->m->addJS('calendar');
            $this->m->addJS('clockpicker/clockpicker')->addJS('jscolor');
            $this->m->addCSS('clockpicker/clockpicker')->addCSS('clockpicker/standalone');
            
        }
        
        public function indexAction(){
            
        }
    }
?>