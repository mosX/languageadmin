<?php
class Tasktable{
    protected $_table = 'tasktable';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    
    
    public function edit($id){
        $this->validation = true;
        //получаем заявку
        $this->m->_db->setQuery(
                    "SELECT `tasktable_tasks`.* "
                    . " FROM `tasktable_tasks` "
                    . " WHERE `tasktable_tasks`.`id` = ".$id
//                    . " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($task);
        
        if(!(int)$_POST['date']){
            $this->validation = false;
            $this->error->message = 'Вы должны ввести дату';
        }
        
        $year = date("Y",strtotime($_POST['date']));
        $month = date("m",strtotime($_POST['date']));
        $day = date("d",strtotime($_POST['date']));
        
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
            
            return false;
        }
        
        $row->id = $task->id;
        $row->user_id = $this->m->_user->id;
        //$row->color = $_POST['color'];
        $row->lesson = $_POST['type'];
        $row->start = $start_date;
        $row->end = $end_date;
        //$row->permanent = $_POST['permanent'] ? 1:0;
        
        if(strtotime($row->start) > time()){
            $row->permanent_update = $row->start;
        }
        
        //$row->permanent_update = $row->permanent ? $row->start : 0;
        //$row->message = $message;
        $row->date = date('Y-m-d H:i:s');
        
        if($this->m->_db->updateObject('tasktable_tasks',$row,'id')){
            //добавляем студентов
            
            if($_POST['students']){
                foreach($_POST['students'] as $item){
                    if($item['act'] == 'insert'){
                        $student->student_id = (int)$item['student_id'];
                        $student->task_id = (int)$row->id;
                        $student->date = date('Y-m-d H:i:s');
                        
                        $this->m->_db->insertObject('tasktable_task_students',$student);
                    }else if($item['act'] == 'update'){
                        $this->m->_db->setQuery(
                                    "UPDATE `tasktable_task_students` "
                                    . " SET `tasktable_task_students`.`student_id` = ".(int)$item['student_id']
                                    . " WHERE `tasktable_task_students`.`id` = ".(int)$item['id']
                                    . " LIMIT 1"
                                );
                        $this->m->_db->query();
                    }else if($item['act'] == 'delete'){
                        $this->m->_db->setQuery(
                                    "UPDATE `tasktable_task_students` "
                                    . " SET `tasktable_task_students`.`status` = 0"
                                    . " WHERE `tasktable_task_students`.`id` = ".(int)$item['id']
                                    . " LIMIT 1"
                                );
                        $this->m->_db->query();
                    }
                }
            }
            
            /*xload('class.admin.tasktable_students');
            $class = new Tasktable_students($this->m);
            $class->removeStudents($row->id);
            //получаем выбранных студентов
            foreach($_POST['students'] as $item){
                if($item != 0)$students[] = $item;
            }
            $students = array_unique($students);
            foreach($students as $item)$class->addStudent($item,$row->id);*/
            
            //echo '{"status":"success"}';
            return true;
        }else{
            //echo '{"status":"error"}';
            return false;
        }
    }
    
    public function getEditData($id){
        $id = (int)$id;
        if(!$id) return false;
        
        $this->m->_db->setQuery(
                    "SELECT `tasktable_tasks`.* "
                    . " FROM `tasktable_tasks` "
                    . " WHERE `tasktable_tasks`.`id` = ".$id   
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($data);
        
        $data->date = date("Y-m-d",strtotime($data->start));
        $data->start = date("H:i",strtotime($data->start));
        $data->end = date("H:i",strtotime($data->end));
        
        //получаем студентов
        $this->m->_db->setQuery(
                    "SELECT `tasktable_task_students`.* "
                    . " , `tasktable_students`.`firstname`"
                    . " , `tasktable_students`.`lastname`"
                    . " FROM `tasktable_task_students`"
                    . " LEFT JOIN `tasktable_students` ON `tasktable_task_students`.`student_id` = `tasktable_students`.`id`"
                    . " WHERE `tasktable_task_students`.`task_id` = ".$data->id
                    . " AND `tasktable_task_students`.`status` = 1"
                );
        $students = $this->m->_db->loadObjectList();
        $data->students = $students;
        
        if(!$data) return false;
        
        return $data;
    }
    
    public function clearPermanent($id){
        $id = (int)$id;
        $date = date('Y-m-d 00:00:00',strtotime($_GET['date']));
        
        if(!$id){
            echo '{"status":"error","message":"Не верный айди"}';
            return false;
        }
        
        //получаем запись для проверки и получения предыдущих дней
        $this->m->_db->setQuery(
                    "SELECT `tasktable_tasks`.* "
                    . " FROM `tasktable_tasks` "
                    . " WHERE `tasktable_tasks`.`id` = ".$id
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " AND `tasktable_tasks`.`permanent` = 1"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($data);
        
        if(!$data){
            echo '{"status":"error","message":"Данные не были найдены"}';
            return false;
        }
        
        $this->setPastPermanentDates($data,$date);
        //p($dates);
        
        $this->m->_db->setQuery(
                    "UPDATE `tasktable_tasks` "
                    . " SET `tasktable_tasks`.`status` = 0"
                    . " WHERE `tasktable_tasks`.`id` = ".$id
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " AND `tasktable_tasks`.`permanent` = 1"
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            echo '{"status":"success"}';
        }else{
            echo '{"status":"error"}';
        }
    }
    
    public function setPastPermanentDates($data,$date){
        //foreach($data as $item)$ids[] = $item->id;
        
        //получаем исключения
        $this->m->_db->setQuery(
                    "SELECT `tasktable_permanent_exceptions`.* "
                    . " , UNIX_TIMESTAMP(`tasktable_permanent_exceptions`.`date`) as timestamp"
                    . " FROM `tasktable_permanent_exceptions`"
                    . " WHERE `tasktable_permanent_exceptions`.`task_id` = ".$data->id
                    . " AND `tasktable_permanent_exceptions`.`date` < '".$date."'"
                );
        $exseptions_tmp = $this->m->_db->loadObjectList();
        foreach($exseptions_tmp as $item){
            $exseptions[strtotime($item->date)][$item->task_id] = $item;
        }
        
        //получаем день недели начала 
        $dayOfWeek = date("N",strtotime($data->start));
        //$temp_date = strtotime(date("Y-m-d",strtotime($data->permanent_update)));
        $temp_date = strtotime(date("Y-m-d 00:00:00",strtotime($data->permanent_update) - 60*60*24)); //отнимаем что бы в вайле первым делом прибавить

        while($temp_date < strtotime($date)){
            $temp_date += 60*60*24;
            $upd_timestamp =  strtotime($item->permanent_update);
            if(date("N",$temp_date) != $dayOfWeek) continue;
            //if($exseptions[$temp_date]) continue;       //улучшить систему исключений тут


            if(date("Y-m-d",$temp_date) == date("Y-m-d",$upd_timestamp)){                       //если тот же день
                $end_date = date(date("Y",$upd_timestamp).'-'.date("m",$upd_timestamp).'-'.date("d",$upd_timestamp).' H:i:s',strtotime($item->end));                    

                if(date("Y-m-d H:i:s",$upd_timestamp) > $end_date) continue;
            }
            
            if($exseptions[$temp_date][$data->id]) continue;
            
                //добавляем в задачи поле
                $row = new stdClass();
                $row->user_id = $data->user_id;
                $row->message = $data->message;
                $row->lesson = $data->lesson;
                $row->color = $data->color;
                $row->permanent = 0;
                $row->permanent_id = $data->id;
                $row->start = date("Y-m-d ".date("H",strtotime($data->start)).":".date("i",strtotime($data->start)).":00",$temp_date);
                $row->end = date("Y-m-d ".date("H",strtotime($data->end)).":".date("i",strtotime($data->end)).":00",$temp_date);

                $row->date = date("Y-m-d H:i:s");
                $row->status = 1;
                
                //проверяем или такая запись уже есть
                $this->m->_db->setQuery(
                            "SELECT `tasktable_tasks`.* "
                            . " FROM `tasktable_tasks` WHERE DATE_FORMAT(`tasktable_tasks`.`start`,'%Y-%m-%d') = '".date("Y-m-d",$temp_date)."'"
                            . " AND `tasktable_tasks`.`permanent_id` = ".$row->permanent_id
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($check);
                if($check) continue;

                $this->m->_db->insertObject('tasktable_tasks',$row);            
        }
    }
    
    public function getFilledDates(){
        $date = strtotime(date('Y-m-d'));        
        if($_GET['date']){            
            $date = strtotime($_GET['date']);
        }
        
        $start = date('Y-m-01 00:00:00',$date);
        
        $end = date('Y-m-t 23:59:59',$date);
        $permanents_dates = array();
        
        //получаем перманентные записи
        $this->m->_db->setQuery(
                    "SELECT DATE_FORMAT(`tasktable_tasks`.`start`,'%Y-%m-%d') as start "
                    . " , DATE_FORMAT(`tasktable_tasks`.`permanent_update`,'%Y-%m-%d') as permanent_update "
                    . " , `tasktable_tasks`.`permanent_update` as updated "
                    . " , UNIX_TIMESTAMP(start) as timestamp"
                    . " , `tasktable_tasks`.`id`"
                    . " , `tasktable_tasks`.`start`"
                    . " , `tasktable_tasks`.`end`"                    
                    . " FROM `tasktable_tasks` "                    
                    . " WHERE `tasktable_tasks`.`status` = 1"
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " AND `tasktable_tasks`.`permanent` = 1"
                    //. " AND `tasktable_tasks`.`id` = 3"
                    . " GROUP BY start"
                );
        $permanents = $this->m->_db->loadObjectList();
        
        $this->m->_db->setQuery(
                    "SELECT `tasktable_permanent_exceptions`.* "
                    . " , UNIX_TIMESTAMP(date) as timestamp"
                    . " FROM `tasktable_permanent_exceptions` "
                    . " WHERE `tasktable_permanent_exceptions`.`date` > '".$start."'"
                    . " AND `tasktable_permanent_exceptions`.`date` < '".$end."'"
                    //. " AND `tasktable_permanent_exceptions`.`user_id` = ".$this->m->_user->id 
                );
        //$permanent_exceptions = $this->m->_db->loadObjectList('timestamp');
        $permanent_exceptions_tmp = $this->m->_db->loadObjectList();
        
        
        foreach($permanent_exceptions_tmp as $item){
            $permanent_exceptions[strtotime($item->date)][$item->task_id] = $item;            
        }
        
        $start_month = (int)date("m",strtotime($start));
        
        foreach($permanents as $item){
            $dayOfWeek = date('N',strtotime($item->start));
            //$startDayOfWeek = strtotime($item->start);
            $startDayOfWeek = strtotime($item->permanent_update);
            
            $temp_date = strtotime($start);
            
            while(date('m',$temp_date) == $start_month){    //пока тот же месяц
                if($temp_date < $startDayOfWeek){   //если дата создание больше чем дата счетчика
                    $temp_date += 60*60*24;    
                    continue;
                }
                
                if(date("N",$temp_date) == $dayOfWeek){
                    if($permanent_exceptions[$temp_date][$item->id]){   //проверяем исключения
                        $temp_date += 60*60*24;    
                        continue;
                    }
                    
                    if(date('Y-m-d',$temp_date) == date("Y-m-d")){  //если проверяем сегодняшнюю дату
                        if(time() > strtotime($item->updated)){
                            $temp_date += 60*60*24;
                            continue;
                        }
                    }
                    
                    $permanents_dates[] = $temp_date;
                    //$permanents_dates[] = date("Y-m-d",$temp_date);                    
                }
                
                $temp_date += 60*60*24;
                //p(date("Y-m-d",$temp_date));
            }
        }
        
        $this->m->_db->setQuery(
                    "SELECT DATE_FORMAT(`tasktable_tasks`.`start`,'%Y-%m-%d') as start "
                    . " , UNIX_TIMESTAMP(start) as timestamp"
                    . " FROM `tasktable_tasks` "
                    . " WHERE `tasktable_tasks`.`status` = 1"
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                    . " AND `tasktable_tasks`.`start` > '".$start."'"
                    . " AND `tasktable_tasks`.`end` < '".$end."'"
                    . " AND `tasktable_tasks`.`permanent` = 0"
                                    
                    . " GROUP BY start"
                );
        $data = $this->m->_db->loadObjectList();
        
        $single_dates = array();
        if($data){
            foreach($data as $item){
                $single_dates[] = strtotime($item->start);
                //$single_dates[] = date("Y-m-d H:i:s",strtotime($item->start));
            }
        }
        
        $result = array_merge($permanents_dates,$single_dates);
        $result = array_unique($result);
        
        return $result;
    }
    
    public function getData($date){
        $start = date("Y-m-d 00:00:00",strtotime($date));
        $end = date("Y-m-d 23:59:59",strtotime($date));
        //p(date('N',strtotime($start)));
        
        $this->m->_db->setQuery(
                    "SELECT `tasktable_tasks`.* "
                    . " , `tasktable_lessons`.`name` as lessons_name"
                    . " , `tasktable_permanent_exceptions`.`id` as 'ignore'"
                    . " , `tasktable_tasks`.`start` as test_start"
                    . " , DATE_FORMAT(`tasktable_tasks`.`permanent_update`,'%Y:%m:%d') as test_upd"
                    . " FROM `tasktable_tasks` "
                    . " LEFT JOIN `tasktable_lessons` ON `tasktable_lessons`.`id` = `tasktable_tasks`.`lesson`"
                    . " LEFT JOIN `tasktable_permanent_exceptions` ON `tasktable_permanent_exceptions`.`task_id` = `tasktable_tasks`.`id` AND DATE_FORMAT(`tasktable_permanent_exceptions`.`date`,'%Y-%m-%d') = DATE_FORMAT('".$start."','%Y-%m-%d')"   //проверка на исключение
                    . " WHERE 1 "
                    . " AND ("
                            ."("
                                ."`tasktable_tasks`.`start` > '".$start."'"
                                . " AND `tasktable_tasks`.`end` < '".$end."'"
                                . " AND `tasktable_tasks`.`permanent` = 0"
                            .") OR ( "
                                ." ( "
                                    . "`tasktable_tasks`.`permanent` = 1 AND DAYOFWEEK(`tasktable_tasks`.`start`)-1 = '".date('N',strtotime($start))."'"    //тот же день недели и перманент
                                    //. " AND `tasks`.`permanent_update` < '".$start."'"
                                    . " AND `tasktable_tasks`.`permanent_update` < '".$end."'"
                                    . " AND ( " //если тот же день
                                        ."( DATE_FORMAT(`tasktable_tasks`.`permanent_update`,'%H:%i:%s') < DATE_FORMAT(`tasktable_tasks`.`end`,'%H:%i:%s') AND DATE_FORMAT(`tasktable_tasks`.`permanent_update`,'%Y-%m-%d') = '".date("Y-m-d",strtotime($date))."')"
                                        ."OR"   //если не тот же день
                                        ."(DATE_FORMAT(`tasktable_tasks`.`permanent_update`,'%Y-%m-%d') != '".date("Y-m-d",strtotime($date))."') "
                                    ." )"
                                    
                                .")"
                            .")"

                    .") "
                    //. " AND `tasks`.`user_id` = ".$this->m->_user->id
                    . " AND `tasktable_tasks`.`status` = 1"
                    . " ORDER BY DATE_FORMAT(`start`,'%H:%i:%s') ASC"
                );
        $ret = $this->m->_db->loadObjectList();
        
        foreach($ret as $item){
            $item->start_timestamp = strtotime($item->start);
            $item->start = strtotime($item->start);
            $item->end = strtotime($item->end);
        }
        
        if($ret){
            foreach($ret as $key=>$item){
                if($item->ignore) unset($ret[$key]);
            }
        }
        
        if($ret){
            foreach($ret as $item){
                $ids[] = $item->id;
                $data[$item->id] = $item;
            }
        }
        
        //добавляем студентов к общему массиву
        $this->m->_db->setQuery(
                    "SELECT `tasktable_task_students`.* "
                    . " , `tasktable_students`.`firstname`"
                    . " , `tasktable_students`.`lastname`"
                    . " FROM `tasktable_task_students`"
                    . " LEFT JOIN `tasktable_students` ON `tasktable_students`.`id` = `tasktable_task_students`.`student_id`"
                    . " WHERE `tasktable_task_students`.`task_id` IN (".implode(',',$ids).")"
                    . " AND `tasktable_task_students`.`status` = 1"
                );
        $students = $this->m->_db->loadObjectList();
        foreach($students as $item){
            $data[$item->task_id]->students[] = $item;
        }

        return $data;
    }
    
    public function checkPermanents(){
        
        //получаем все активные перманентные
        $this->m->_db->setQuery(
                    "SELECT `tasktable_tasks`.* "
                    . " FROM `tasktable_tasks` "
                    . " WHERE `tasktable_tasks`.`permanent` = 1"
                    . " AND `tasktable_tasks`.`status` = 1"
                    //. " AND `tasktable_tasks`.`user_id` = ".$this->m->_user->id
                );
        $data = $this->m->_db->loadObjectList();

        foreach($data as $item)$ids[] = $item->id;

        $this->m->_db->setQuery( //получаем исключения
                    "SELECT `tasktable_permanent_exceptions`.* "
                    . " , UNIX_TIMESTAMP(`tasktable_permanent_exceptions`.`date`) as timestamp"
                    . " FROM `tasktable_permanent_exceptions`"
                    . " WHERE `tasktable_permanent_exceptions`.`task_id` IN (".implode(',',$ids).")"
                );
        $exseptions_tmp = $this->m->_db->loadObjectList();

        foreach($exseptions_tmp as $item){
            $exseptions[$item->timestamp][$item->task_id] = $item;
        }

        $current_dayOfWeek = date("N",time());
        //получаем день недели начала 
        
        foreach($data as $item){
            $dayOfWeek = date("N",strtotime($item->start));
            $temp_date = strtotime(date("Y-m-d 00:00:00",strtotime($item->permanent_update) - 60*60*24)); //отнимаем что бы в вайле первым делом прибавить

            while($temp_date < time()){
                $temp_date += 60*60*24;

                $upd_timestamp =  strtotime($item->permanent_update);

                if(date("N",$temp_date) != $dayOfWeek) continue;    //если не тот же день недели при переборе дней

                //будущее
                if($temp_date > time()) continue;    //если будущий день

                //настоящее
                if(date("Y-m-d",$temp_date) == date("Y-m-d",time())){   //если текущий день 
                    if(time() < strtotime(date("H:i:s",strtotime($item->end)))) continue;   //если время меньше окончания                    
                    if($upd_timestamp > strtotime(date("H:i:s",strtotime($item->end)))) continue;  //если уже обновлялось
                }

                //прошлое
                if(strtotime(date("Y-m-d 00:00:00",$upd_timestamp)) > strtotime(date("Y-m-d 00:00:00",$temp_date))) continue;

                if($exseptions[$temp_date][$item->id]) continue;    //если есть исключение

                //добавляем в задачи поле
                $row = new stdClass();
                $row->user_id = $item->user_id;
                $row->message = $item->message;
                $row->lesson = $item->lesson;
                $row->color = $item->color;
                $row->permanent = 0;
                $row->permanent_id = $item->id;
                $row->start = date("Y-m-d ".date("H",strtotime($item->start)).":".date("i",strtotime($item->start)).":00",$temp_date);
                $row->end = date("Y-m-d ".date("H",strtotime($item->end)).":".date("i",strtotime($item->end)).":00",$temp_date);

                $row->date = date("Y-m-d H:i:s");
                $row->status = 1;

                //проверяем или такая запись уже есть
                $this->m->_db->setQuery(
                            "SELECT `tasktable_tasks`.* "
                            . " FROM `tasktable_tasks` "
                            . " WHERE DATE_FORMAT(`tasktable_tasks`.`start`,'%Y-%m-%d') = '".date("Y-m-d",$temp_date)."'"
                            . " AND `tasktable_tasks`.`permanent_id` = ".$row->permanent_id
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($check);
                
                if($check) continue;
                
                $this->m->_db->insertObject('tasktable_tasks',$row);
            }
            //обновляем поле permanent_update
            $this->m->_db->setQuery(
                        "UPDATE `tasktable_tasks` SET `tasktable_tasks`.`permanent_update` = '".date("Y-m-d H:i:s")."'"
                        . " WHERE `tasktable_tasks`.`id` = ".$item->id
                        . " LIMIT 1"
                    );
            $this->m->_db->query();
        }
    }
    
     public function addTaskElement($timestamp, $start, $end, $permanent = 1){
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
                //$row->user_id = $this->m->_user->id;
                $student->task_id = $row->id;
                $student->student_id = $item;
                $student->date = date("Y-m-d H:i:s");
                $this->m->_db->insertObject('tasktable_task_students',$student);
                
            }

            //redirect('/?date='.date("Y-m-d",strtotime($start)));
        }

        return true;
    }
}
?>