<?php
class errorController extends Model {
        public function init(){
            
        }
        public function indexAction(){
            
            if(!$this->m->_user->id) redirect('/');
        }
}
?>