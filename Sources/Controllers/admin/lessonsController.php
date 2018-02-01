<?php
    class lessonsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                $row->description = strip_tags(trim($_POST['description']));
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `lessons` SET `lessons`.`name` = '".$row->name."'"
                                . " , `lessons`.`description` = '".$row->description."'"
                                . " WHERE `lessons`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('lessons',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `lessons`.* "
                            . " FROM `lessons`"
                            . " WHERE `lessons`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
         public function question_collectionsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $row->question_id = (int)$_POST['question'];
                $row->lesson_id = (int)$this->m->_path[2];
                
                //проверяем или не было добавлено ранее
                $this->m->_db->setQuery(
                            "SELECT `question_collections`.`id` "
                            . " FROM `question_collections` "
                            . " WHERE `question_collections`.`question_id` = ".$row->question_id
                            . " AND `question_collections`.`lesson_id` = ".$row->lesson_id
                            . " LIMIT 1"                        
                        );
                $check = $this->m->_db->loadResult();
                
                if($check){
                    echo '{"status":"error"}';
                    return false;
                }
                    
                if($this->m->_db->insertObject('question_collections',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }else{
                //получаем все вопросы для селекта
                $this->m->_db->setQuery(
                            "SELECT `questions`.* "
                             . " FROM `questions` "
                            . " WHERE `questions`.`status` = 1"
                        );
                $this->m->list = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `question_collections`.* "
                             . " FROM `question_collections`"
                             . " WHERE `question_collections`.`lesson_id` = ".(int)$this->m->_path[2]                             
                        );
                $this->m->data = $this->m->_db->loadObjectList();                
            }
        }
        
        public function answer_collectionsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $row->answer_id = (int)$_POST['answer'];
                $row->question_id = (int)$this->m->_path[2];
                
                //проверяем или не было добавлено ранее
                $this->m->_db->setQuery(
                            "SELECT `answer_collections`.`id` "
                            . " FROM `answer_collections` "
                            . " WHERE `answer_collections`.`answer_id` = ".$row->answer_id
                            . " AND `answer_collections`.`question_id` = ".$row->question_id
                            . " LIMIT 1"                        
                        );
                $check = $this->m->_db->loadResult();
                
                if($check){
                    echo '{"status":"error"}';
                    return false;
                }
                    
                if($this->m->_db->insertObject('answer_collections',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
                
            }else{
                //получаем все вопросы для селекта
                $this->m->_db->setQuery(
                            "SELECT `answers`.* "
                             . " FROM `answers` "
                            . " WHERE `answers`.`status` = 1"
                        );
                $this->m->list = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `answer_collections`.* "
                             . " FROM `answer_collections`"
                             . " WHERE `answer_collections`.`question_id` = ".(int)$this->m->_path[2]
                             //. " AND `answer_collections`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
                
            }
        }
        
        public function answersAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $id = (int)$_POST['id'];
                $row->text = strip_tags(trim($_POST['text']));
                $row->date  = date("Y-m-d H:i:s");
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `answers` SET `answers`.`name` = '".$row->text."'"
                                . " WHERE `answers`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('answers',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `answers`.* "
                            . " FROM `answers` "
                            . " WHERE `answers`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function questionsAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                $row->value = strip_tags(trim($_POST['value']));
                $row->description = strip_tags(trim($_POST['description']));
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `questions` SET `lessons`.`name` = '".$row->name."'"
                                . " , `questions`.`value` = '".$row->value."'"
                                . " , `questions`.`description` = '".$row->description."'"
                                . " WHERE `questions`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('questions',$row)){
                        echo '{"status":"success"}';
                    }else{
                        p($this->m->_db->_sql);
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `questions`.* "
                            . " FROM `questions` "
                            . " WHERE `questions`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
                
            }
        }
        
       /* public function getdataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `lessons`.* "
                        . " FROM `lessons` "
                        . " WHERE `lessons`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }*/
        
        
    }
?>