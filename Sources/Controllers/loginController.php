<?php
class loginController extends Model {
        public function init(){
            $this->disableTemplate();
            $this->disableView();
        }
        public function indexAction(){  
            //$this->m->_auth->login();
            if($this->m->_auth->ajaxlogin()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
}
?>