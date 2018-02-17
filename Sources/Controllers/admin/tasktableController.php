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
            
            /*xload('class.admin.tasktable');
            $tasktable = new Tasktable($this->m);
            $tasktable->checkPermanents();            */
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                //надо их предавать взависимости от выбранного дня
                /*$year = $_GET['year'];
                $month = $_GET['month'];
                $day = $_GET['day'];*/
                $year = 2018;
                $month = 2;
                $day = 15;
                
                $message = strip_tags(trim($_POST['message']));

                $start = $_POST['start'];
                $end = $_POST['end'];

                $start_date = $year.'-'.$month.'-'.$day.' '.$start;
                $end_date = $year.'-'.$month.'-'.$day.' '.$end;

                if(strtotime($end_date) < strtotime($start_date)){
                    $this->validation = false;
                    $json->error->date = 'Дата окончания не может быть раньше даты начала';
                }

                if(!$this->validation){
                    //return false;
                    $json->status = 'error';
                    echo json_encode($json);
                    //die('{"status":"error"}');
                    return false;
                }
                
                //проверяем есть постоянные или занятия или нет
                $permanent_status = false;
                foreach($_POST['permanent'] as $item){
                    if($item) $permanent_status = true;
                    
                    break;                    
                }
                
                if($permanent_status ){
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
                }else{
                    if($tasktable->addTaskElement(strtotime($start_date), $start, $end,0)){
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