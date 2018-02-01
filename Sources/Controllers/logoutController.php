<?php
class logoutController extends Model {
         public function init(){
            $this->disableTemplate();
            $this->disableView();
        }
        public function indexAction(){
            $this->m->_auth->logout();
        }	
}
?>