<?php
class Tasktable{
    protected $_table = 'tasktable';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
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
                                //. " AND `tasktable_tasks`.`permanent` = 0"
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
            $item->start = strtotime($item->start);
        }
        
        
        if($ret){
            foreach($ret as $key=>$item){
                if($item->ignore) unset($ret[$key]);
            }
        }
        
        if($ret){
            foreach($ret as $item){
                $data[] = $item;
            }
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
                            . " AND `tasktable_tasks`.`user_id` = ".$this->_user->id
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
                                    . " FROM `tasktable_tasks` WHERE DATE_FORMAT(`tasks`.`start`,'%Y-%m-%d') = '".date("Y-m-d",$temp_date)."'"
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
                $row->user_id = $this->m->_user->id;
                $row->task_id = $row->id;
                $row->student_id = $item;
                $row->date = date("Y-m-d H:i:s");
                $this->m->_db->insertObject('tasktable_students',$row);
            }

            //redirect('/?date='.date("Y-m-d",strtotime($start)));
        }

        return true;
    }
}
?>