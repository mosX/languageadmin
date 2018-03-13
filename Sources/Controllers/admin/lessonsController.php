<?php
    class lessonsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            xload('class.admin.lessons');
            $lessons = new Lessons($this->m);
                
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                $row->description = strip_tags(trim($_POST['description']));
                $row->show_answers = $_POST['show_answers'] ? 1 : 0;
                
                $row->terms = serialize($_POST['terms']);
                
                if($id){        //EDIT
                    if($lessons->updateMain($id,$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($lessons->addNew($row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->data = $lessons->getData();
            }
        }
        
        public function imagesAction(){
            $this->m->_db->setQuery(
                        "SELECT `images`.* "
                        . " , COUNT(`answers`.`id`) as answers"
                        . " FROM `images` "
                        . " LEFT JOIN `answers` ON `answers`.`image_id` = `images`.`id`"
                        . " WHERE `images`.`status` = 1"
                        . " GROUP BY `images`.`id`"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        public function publish_questionAction(){
            $this->disableTemplate();
            $this->disableView();

            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);

            $id = (int)$_GET['id'];                                
            $result= new stdClass();

            if($questionCollections->updatePublishing($id, $result)){
                echo '{"status":"success","result":"'.$result->published.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
            
        public function publishAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                xload('class.admin.lessons');
                $lessons = new Lessons($this->m);
                $id = (int)$_GET['id'];
                
                $result = new stdClass();   //сюда запишем результат апдейта
                if($lessons->updatePublishing($id,$result)){
                    echo '{"status":"success","result":"'.$result->published.'"}';
                }else{
                    echo '{"status":"error"}';
                }
            }
        }
        
        /*public function delete_question_collectionAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);
            
            $id = (int)$_GET['id'];
            if($questionCollections->removeGiven($id)){
                echo '{"status":"success"}';
            }else{                
                echo '{"status":"error"}';
            }
        }*/
        
        public function delete_lessonAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.lessons');
            $lessons = new Lessons($this->m);
            
            $id = (int)$_GET['id'];
            
            if($lessons->removeGiven($id)){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
               
        public function testing_checkAction(){
            $this->m->_db->setQuery(
                        "SELECT `testing_results`.* "
                        . " , `lessons`.`name`"
                        . " FROM `testing_results`"
                        . " LEFT JOIN `lessons` ON `lessons`.`id` = `testing_results`.`lesson_id`"
                        . " WHERE `testing_results`.`id` = ".(int)$this->m->_path[2]
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($result);
            
            $result->results = unserialize($result->results);
            
            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);
            
            $data = $questionCollections->getGivenLesson($result->lesson_id);
            
            foreach($data as $item)$ids[] = $item->question_id;
            
            //получаем ответы
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        . " , `images`.`filename`"
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                        . " WHERE `answer_collections`.`question_id` IN (".  implode(',', $ids).")"
                    );
            $answers = $this->m->_db->loadObjectList();
            
            foreach($answers as $item){
                if(is_array($result->results[$item->question_id])){
                    if($item->id == $result->results[$item->question_id]['answer']){
                        $data[$item->question_id]->time = $result->results[$item->question_id]['time'];
                        $item->selected = 'true';
                    }
                }else{
                    if($item->id == $result->results[$item->question_id]){
                         $item->selected = 'true';
                    }
                }
                
                if($data[$item->question_id]->correct == $item->id){
                    $item->correct = 'true';
                }
                
                $data[$item->question_id]->answers[] = $item;
            }
            $this->m->data = $data;
            
            //$this->m->testing = $data;
        }
        
        public function resultsAction(){
            $this->m->_db->setQuery(
                        "SELECT `testing_results`.* "
                        . " , `lessons`.`name`"
                        . " FROM `testing_results`"
                        . " LEFT JOIN `lessons` ON `lessons`.`id` = `testing_results`.`lesson_id`"
                        . " WHERE `testing_results`.`status` = 1"
                        . " ORDER BY `id` DESC"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        /*public function question_image_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.questions');                
            $questions = new Questions($this->m);
            
            $id = (int)$_GET['id'];
            $data = $questions->getGiven($id);
            
            //получаем список ответов для селекта
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        . " , `images`.`id` as image_id"
                        . " , `images`.`filename`"                    
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                        . " WHERE `answer_collections`.`question_id` = ".$id                        
                    );
            $data->answers = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }*/
        
        public function answers_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `answers`.* "
                        . " FROM `answers` "
                        . " WHERE `answers`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function lesson_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            xload('class.admin.lessons');
            $lessons = new Lessons($this->m);
            
            $data = $lessons->getGiven($id);
            
            $data->terms = unserialize($data->terms);
            
            echo json_encode($data);
        }
        
        public function question_collectionsAction(){
            $this->m->lesson_id = $this->m->_path[2];
            
            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $row->question_id = (int)$_POST['question'];
                $row->lesson_id = (int)$this->m->_path[2];
            
                $check = $questionCollections->getGiven(null,$row->question_id,$row->lesson_id);
                
                if($check){
                    echo '{"status":"error"}';
                    return false;
                }
                    
                if($questionCollections->addNew($row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }else{
                xload('class.admin.questions');
                $questions = new Questions($this->m);
                
                $this->m->list = $questions->getList();                
                $this->m->data = $questionCollections->getData($this->m->_path[2]);
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
                            . " , `answers`.`text`"
                             . " FROM `answer_collections`"
                             . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
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
                                "UPDATE `answers` SET `answers`.`text` = '".$row->text."'"
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
                    "SELECT COUNT(`answers`.`id`)"
                    . " FROM `answers`"
                    . " WHERE `answers`.`status` = 1"
                );
                $this->m->total = $this->m->_db->loadResult(); 

                $xNav = new xNav("/lessons/answers/", $this->m->total, "GET");
                $xNav->limit = 50;
                $this->m->pagesNav = $xNav->showPages();
                
                $this->m->_db->setQuery(
                            "SELECT `answers`.* "
                            . " , COUNT(`answer_collections`.`id`) as questions"
                            . " FROM `answers` "
                            . " LEFT JOIN `answer_collections` ON `answer_collections`.`answer_id` = `answers`.`id`"
                            . " WHERE `answers`.`status` = 1"
                            . " GROUP BY `answers`.`id`"
                            . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function get_images_listAction(){
            $this->disableTemplate();
            $this->disableView();
            $this->m->_db->setQuery(
                        "SELECT `images`.* "
                        . " FROM `images` WHERE `images`.`status` = 1"                       
                    );
            $data = $this->m->_db->loadObjectList();
            echo json_encode($data);
        }
        
        public function questionsAction(){
            xload('class.admin.questions');
            $questions = new Questions($this->m);

            $this->m->data = $questions->getData();

            /*$this->m->_db->setQuery(
                        "SELECT `answers`.`id` "
                         . " , `answers`.`text`"
                         . " FROM `answers` "
                         . " WHERE `answers`.`status` = 1"
                    );
            $this->m->answers = $this->m->_db->loadObjectList();*/
        }
    }
?>