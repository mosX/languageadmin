<?php
    class mailController extends Model {
        public function init(){
            //$this->m->_template = 'test';
            $this->m->addCSS('mail');
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
        }
        public function indexAction(){
            //получаем письма
            $this->m->_db->setQuery(
                        "SELECT `mailbox`.* "
                        . " FROM `mailbox` "
                        . " WHERE `mailbox`.`folder` = 'inbox'"
                    
                    );
            $this->m->data = $this->m->_db->loadObjectList();            
        }
        
        public function save_patternAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if($_POST['id']){   //обвноялем
                $this->m->_db->setQuery(
                            "UPDATE `mail_patterns` "
                            . " SET `mail_patterns`.`name` = '".$_POST['name']."'"
                            . " , `mail_patterns`.`text` = '".$_POST['text']."'"
                            . " , `mail_patterns`.`subject` = '".$_POST['subject']."'"
                            . " WHERE `mail_patterns`.`id` = ".$_POST['id'] 
                            . " LIMIT 1"
                        );
                if($this->m->_db->query()){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }else{            
                $row->parent_id = $this->m->_user->id;
                $row->name = strip_tags(trim($_POST['name']));
                $row->text = $_POST['text'];
                $row->subject = $_POST['subject'];
                $row->date = date("Y-m-d H:i:s'");
                $row->status = 1;

                if($this->m->_db->insertObject('mail_patterns',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }
        }
        
        public function edit_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $this->m->_db->setQuery(
                        "SELECT `mail_patterns`.* "
                        . " FROM `mail_patterns` WHERE `mail_patterns`.`id` = ".(int)$_POST['id']
                        . " AND `mail_patterns`.`parent_id` = ".$this->m->_user->id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function patterns_listAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $this->m->_db->setQuery(
                        "SELECT `mail_patterns`.`id` "
                        . " ,`mail_patterns`.`name`"
                        . " FROM `mail_patterns`"
                        . " WHERE `mail_patterns`.`parent_id` = ".$this->m->_user->id
                        . " ORDER BY `id` DESC"
                    );
            $this->m->data = $this->m->_db->loadObjectList('id');
            
            echo json_encode($this->m->data);
        }
        
        public function get_mailAction(){   //for replay
            $this->disableTemplate();
            $this->disableView();
            
            $this->m->_db->setQuery(
                        "SELECT `mailbox`.* FROM `mailbox` "
                        . " WHERE `mailbox`.`id` = ".$this->m->_path[2]
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($mail);
            
            
            //GET PATTERN LIST
            $this->m->_db->setQuery(
                        "SELECT `mail_patterns`.`id` "
                        . " ,`mail_patterns`.`name`"
                        . " FROM `mail_patterns`"
                        . " WHERE `mail_patterns`.`parent_id` = ".$this->m->_user->id
                        . " ORDER BY `id` DESC"
                    );
            $list = $this->m->_db->loadObjectList('id');
            
            $json->mail = $mail;
            $json->patterns = $list;
            
            echo json_encode($json);
        }
        
        public function loadpatternAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $this->m->_db->setQuery(
                        "SELECT `mail_patterns`.* "
                        . " FROM `mail_patterns` "
                        . " WHERE `mail_patterns`.`id` = ".(int)$this->m->_path[2]
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($pattern);
            echo json_encode($pattern);
        }
    }
?>