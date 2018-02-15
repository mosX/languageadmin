<?php
    class tasktableController extends Model {
        public function init(){
            /*$this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');*/
            $this->m->addCSS('calendar');
            $this->m->addJS('calendar');
            $this->m->addJS('clockpicker/clockpicker')->addJS('jscolor');
            $this->m->addCSS('clockpicker/clockpicker')->addCSS('clockpicker/standalone');
            /********TEST*********/
            
            return;
            xload('class.admin.tasktable');
            $tasktable = new Tasktable($this->m);
            $tasktable->checkPermanents();            
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $year = $_GET['year'];
                $month = $_GET['month'];
                $day = $_GET['day'];

                $message = strip_tags(trim($_POST['message']));

                $start = $_POST['start'];
                $end = $_POST['end'];

                $start_date = $year.'-'.$month.'-'.$day.' '.$start;
                $end_date = $year.'-'.$month.'-'.$day.' '.$end;

                if(strtotime($end_date) < strtotime($start_date)){
                    $this->validation = false;
                    $this->error->date = 'Дата окончания не может быть раньше даты начала';
                }

                if(!$this->validation){
                    //return false;
                    die('{"status":"error"}');
                }
                //p($_POST);
                if($_POST['permanent']){
                    xload('class.admin.tasktable');
                    $tasktable = new Tasktable($this->m);
                    
                    //получаем текущий день недели
                    $tempDay = date("N",strtotime($start_date));
                    $tempTimestamp = strtotime($start_date);

                    do{
                        //p(date('Y-m-d',$tempTimestamp));
                        if($_POST['permanent'][date("N",$tempTimestamp)]){
                            $tasktable->addTaskElement($tempTimestamp, $start, $end);
                        }
                        $tempTimestamp += 60*60*24;
                    }while($tempDay != date("N",$tempTimestamp));

                    die('{"status":"success"}');
                    //return true;            
                    //redirect('/?date='.date("Y-m-d",strtotime($start)));
                }else{
                    if($tasktable->addTaskElement(strtotime($start_date), $start, $end,0)){
                        //redirect('/?date='.date("Y-m-d",strtotime($start)));
                        //return true;
                        die('{"status":"success"}');
                    }
                }
            }else{
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
        
       /* public function addTaskElement($timestamp, $start, $end, $permanent = 1){
            $row->user_id = $this->m->_user->id;
            $row->color = $_POST['color'];
            $row->lesson = $_POST['type'];
            $row->start = date("Y-m-d ".$start,$timestamp);
            $row->end = date("Y-m-d ".$end,$timestamp);
            $row->permanent = $permanent;

            $row->permanent_update = $row->start;
            $row->message = strip_tags(trim($_POST['message']));
            $row->date = date('Y-m-d H:i:s');

            if($this->m->_db->insertObject('tasktable_tasks',$row,'id')){
                //xload('class.students');
                //$class = new Students($this->m);
                //получаем выбранных студентов
                foreach($_POST['students'] as $item){
                    if($item != 0)$students[] = $item;
                }
                $students = array_unique($students);
                foreach($students as $item){
                    $row->user_id = $this->m->_user->id;
                    $row->task_id = $row->id;
                    $row->student_id = $item;
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('tasktable_students',$row);
                }
                    
                //redirect('/?date='.date("Y-m-d",strtotime($start)));
            }

            return true;
        }*/
        
        public function lessonsAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                
                if($id){    //UPDATE
                    
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