<?php
    class usersController extends Model {
        public function init(){

        }
        
        public function del_userAction(){
            $this->disableTemplate();
            $this->disableView();
            $user_id = (int)$_POST['user_id'];
            
            $this->m->_db->setQuery(
                        "UPDATE `users` SET `users`.`status` = 0 "
                        . " WHERE `users`.`id` = ".$user_id
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