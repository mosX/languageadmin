<?php
    class tasktableController extends Model {
        public function init(){
            /*$this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');*/
            $this->m->addCSS('bootstrap-datetimepicker.min');
            $this->m->addJS('moment')->addJS('bootstrap-datetimepicker.min');
            
            $this->m->addCSS('calendar');
            //$this->m->addJS('calendar');
            $this->m->addJS('clockpicker/clockpicker')->addJS('jscolor');
            $this->m->addCSS('clockpicker/clockpicker')->addCSS('clockpicker/standalone');
            /********TEST*********/
            
            xload('class.admin.tasktable');            
            $tasktable = new Tasktable($this->m);
            $tasktable->checkPermanents();
        }
        
        public function delete_studentAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "UPDATE `tasktable_students` SET `tasktable_students`.`status` = 0"
                        . " WHERE `tasktable_students`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function delete_taskAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "UPDATE `tasktable_tasks` SET `tasktable_tasks`.`status` = 0"
                        . " WHERE `tasktable_tasks`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function delete_lessonAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "UPDATE `tasktable_lessons` SET `tasktable_lessons`.`status` = 0"
                        . " WHERE `tasktable_lessons`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }                    
        }
        
        public function lesson_edit_dataAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_GET['id'];
                
                $this->m->_db->setQuery(
                            "SELECT `tasktable_lessons`.* "
                            . " FROM `tasktable_lessons`"
                            . " WHERE `tasktable_lessons`.`id` = ".$id
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($data);
                
                echo json_encode($data);
            }
        }
        
        public function student_edit_dataAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_GET['id'];
                
                $this->m->_db->setQuery(
                            "SELECT `tasktable_students`.* "
                            . " FROM `tasktable_students`"
                            . " WHERE `tasktable_students`.`id` = ".$id
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($data);
                
                echo json_encode($data);
            }
        }
        
        public function clear_permanentAction(){
            //xload('class.admin.tasktable');
            $tasktable = new Tasktable($this->m);
            
            $this->disableTemplate();
            $this->disableView();
            $id = $this->m->_path[2];
            
            $date = date("Y-m-d H:i:s",strtotime($_GET['date']));
            
            $tasktable->clearPermanent($id,$date);
        }
        
        
        public function getdataAction(){
            //xload('class.admin.tasktable');
            $tasktable = new Tasktable($this->m);
            
            $this->disableTemplate();
            $this->disableView();
            
            $date = date("Y-m-d",strtotime($_GET['date']));
            
            $data = $tasktable->getData($date);
            
            echo $data ? json_encode($data): '[]';
        }
        
        public function indexAction(){
            //xload('class.admin.tasktable');
            $tasktable = new Tasktable($this->m);
                
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                $this->validation = true;
                
                $date = date("Y-m-d",strtotime($_POST['date']));    //надо их предавать взависимости от выбранного дня
                
                $message = strip_tags(trim($_POST['message']));

                $start = $_POST['start'];
                $end = $_POST['end'];

                $start_date = $date.' '.$start;
                $end_date = $date.' '.$end;

                if(strtotime($end_date) < strtotime($start_date)){
                    $this->validation = false;
                    $json->error->date = 'Дата окончания не может быть раньше даты начала';
                }

                if(!$this->validation){
                    $json->status = 'error';
                    echo json_encode($json);                    
                    return false;
                }
                
                //проверяем есть постоянные или занятия или нет
                $permanent_status = false;
                foreach($_POST['permanent'] as $item){
                    if($item) $permanent_status = true;
                    
                    break;                    
                }
                
                if($permanent_status){
                    //получаем текущий день недели
                    $tempDay = date("N",strtotime($start_date));
                    $tempTimestamp = strtotime($start_date);

                    do{
                        if($_POST['permanent'][date("N",$tempTimestamp)]){
                            $tasktable->addTaskElement($tempTimestamp, $start, $end);
                        }
                        $tempTimestamp += 60*60*24;
                    }while($tempDay != date("N",$tempTimestamp));

                    die('{"status":"success"}');                    
                }else{
                    if($tasktable->addTaskElement(strtotime($start_date), $start, $end,0)){
                        die('{"status":"success"}');
                    }
                }
            }else{
                $this->m->data = $tasktable->getData(date("Y-m-d H:i:s"));
                //$this->filled_data = $tasktable->getFilledDates(date("Y-m-d H:i:s"));
                                
                $this->m->_db->setQuery(
                            "SELECT `tasktable_lessons`.* "
                            . " FROM `tasktable_lessons`"
                            . " WHERE `tasktable_lessons`.`status` = 1"                        
                        );
                $this->m->lessons = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `tasktable_students`.* "
                            . " FROM `tasktable_students`"
                            . " WHERE `tasktable_students`.`status` = 1"
                        );
                $this->m->students = $this->m->_db->loadObjectList();
            }
        }
        
        public function editAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $tasktable = new Tasktable($this->m);
                
                $id = (int)$_POST['id'];
                
                if($tasktable->edit($id)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }
        }
        
        public function edit_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            $tasktable = new Tasktable($this->m);
            
            echo json_encode($tasktable->getEditData((int)$_GET['id']));
        }
        
        public function filledAction(){
            $this->disableTemplate();
            $this->disableView();
            $tasktable = new Tasktable($this->m);
            
            $data = $tasktable->getFilledDates(date("Y-m-d",strtotime($_GET['date'])));
            
            echo json_encode($data);
        }
 
        public function lessonsAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                
                if($id){    //UPDATE
                    $this->m->_db->setQuery(
                                "UPDATE `tasktable_lessons` SET `tasktable_lessons`.`name` = '".$row->name."'"
                                . " WHERE `tasktable_lessons`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{                        
                        echo '{"status":"error"}';
                    }
                }else{      //ADD
                    $row->date = date("Y-m-d H:i:s");
                    if($this->m->_db->insertObject('tasktable_lessons',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `tasktable_lessons`.* "
                            . " FROM `tasktable_lessons`"
                            . " WHERE `tasktable_lessons`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function studentsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $row->firstname = strip_tags(trim($_POST['firstname']));
                $row->lastname = strip_tags(trim($_POST['lastname']));
                $row->phone = strip_tags(trim($_POST['phone']));
                
                if($id){    //UPDATE
                    $this->m->_db->setQuery(
                                "UPDATE `tasktable_students` SET `tasktable_students`.`firstname` = '".$row->firstname."'"
                                . " , `tasktable_students`.`lastname` = '".$row->lastname."'"
                                . " , `tasktable_students`.`phone` = '".$row->phone."'"
                                . " WHERE `tasktable_students`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';    
                    }                    
                }else{      //ADD
                    $row->date = date("Y-m-d H:i:s");
                    if($this->m->_db->insertObject('tasktable_students',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `tasktable_students`.* "
                            . " FROM `tasktable_students`"
                            . " WHERE `tasktable_students`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
    }
?>