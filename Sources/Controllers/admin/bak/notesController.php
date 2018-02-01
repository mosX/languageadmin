<?php
    class notesController extends Model {
        public function init(){

        }
        public function indexAction(){
            
        }
        
        public function note_itemAction(){
            $this->disableTemplate();
            $this->disableView();

            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->m->note->message = $_POST['message'];
                $this->m->note->parent = $this->m->_user->email;
                $this->m->note->date = date('Y-m-d H:i:s');
                $this->m->note->id = '';

                $this->m->module('note_item');  
            }
        }
        
        public function addAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $_POST = json_decode(file_get_contents('php://input'), true);
                xload('class.notes');
                $notes = new Notes($this->m);

                if($notes->addNew()){
                    $this->m->note = $notes->note;
                    $this->m->note->parent = $this->m->_user->email;
                    
                    $json->status = 'success';
                    ob_start();
                        $this->m->module('note_item');
                        $json->html = ob_get_contents();
                    ob_end_clean();
                }else{
                    $json->status = 'error';                    
                }
                
                echo json_encode($json);
            }
        }
        
        public function delAction(){
            $this->disableTemplate();
            $this->disableView();
            $_POST = json_decode(file_get_contents('php://input'), true);
            
            $user_id = (int)$_POST['user_id'];
            $id = (int)$_POST['id'];            
            
            //проверяем или такой есть под даннЫм пользователем
            $this->m->_db->setQuery(
                        "SELECT COUNT(*) "
                        . " FROM `notes` "
                        . " WHERE 1"
                        . " AND `notes`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $check = $this->m->_db->loadResult();
            
            if(!$check){
                echo '{"status":"error","message":"Такой заметки нету"}';
                return false;
            }
            
            $this->m->_db->setQuery(
                        "UPDATE `notes` SET `notes`.`status` = 0"
                        . " WHERE 1"
                        . " AND `notes`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function pinAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $user_id = (int)$_POST['user_id'];
            $id = (int)$_POST['id'];
            
            $this->m->_db->setQuery(
                        "UPDATE `notes` SET `notes`.`pin` = 1"
                        . " WHERE `notes`.`id` = ".$id
                        . " AND `notes`.`user_id`".$user_id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function editAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true);
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $message = strip_tags(trim($_POST['message']));
                $id = (int)$_POST['id'];
                
                $this->m->_db->setQuery(
                            "UPDATE `notes` "
                            . " SET `notes`.`message` = '".$message."'"
                            . " WHERE `notes`.`id` = ".$id
                            . " LIMIT 1"
                        );
                if($this->m->_db->query()){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';   
                }
            }
        }
    }
?>