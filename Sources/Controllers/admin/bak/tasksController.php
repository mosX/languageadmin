<?php
    class tasksController extends Model {
        public function init(){
            $this->m->addCSS('jquery-ui.min');
            $this->m->addJS('jquery-ui.min');
        }
        
        public function deleteAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "UPDATE `tasks` "
                        . " SET `tasks`.`status` = 0"
                        . " WHERE `tasks`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function completeAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true);
            
            $row->result = strip_tags(trim($_POST['result']));
            $row->status = 2;
            $row->id = (int)$_POST['id'];
            
            if($this->m->_db->updateObject('tasks',$row,'id')){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function getdataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`login`"
                        . " , `supers`.`email` parentname"
                        . " FROM `tasks` "
                        . " LEFT JOIN `users` ON `users`.`id` = `tasks`.`user_id`"
                        . " LEFT JOIN `supers` ON `supers`.`id` = `tasks`.`parent`"
                        . " WHERE `tasks`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->loadObject($task)){
                echo '{"status":"success","data":'.json_encode($task).'}';
            }else{
                echo '{"status":"error"}';
            }            
        }
        
        public function moveAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $type = strip_tags(trim($this->m->_path[2]));
            $id = (int)$_GET['id'];
            //получаем заметку
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " FROM `tasks`"
                        . " WHERE `tasks`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($task);
            
            
            //проверяем какое время ставить нужно
            switch($type){
                case 'yesterday':
                    $time = strtotime('-1 day');
                    break;
                case 'today': 
                    $time = time();
                    break;
                case 'tomorrow': 
                    $time = strtotime('+1 day');
                    break;
                case 'deleted': 
                    $this->m->_db->setQuery(
                                "UPDATE `tasks` SET `tasks`.`status` = 0 "
                                . " WHERE `tasks`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){                        
                        echo '{"status":"success"}';
                    }else{                        
                        echo '{"status":"error"}';
                    }
                    return;
                    break;
                
            }
            $date = date('Y-m-d',$time) . ' '.date('H:i:s',strtotime($task->date));
            
            //обновляем
            $this->m->_db->setQuery(
                        "UPDATE `tasks` "
                        . " SET `tasks`.`date` = '".date("Y-m-d H:i:s",strtotime($date))."'"
                        . " WHERE `tasks`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function checkloginAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $value  = strip_tags(trim($_GET['value']));
            
            //получаем пользователей у кого ник похож
            $this->m->_db->setQuery(
                        "SELECT `users`.`login`,`users`.`id` "
                        . " FROM `users`"
                        . " WHERE `users`.`status` = 1 "
                        . " AND `users`.`login` LIKE '%".$value."%'"
                        . " LIMIT 10"
                    );
            $data = $this->m->_db->loadObjectList();
            
            echo json_encode($data);            
        }
        
        public function monthlyAction(){
            $start_month = strtotime(date("Y-m-01 H:i:s"));
            $num_of_day = date("N",$start_month);
            
            $this->m->start = $start_month - (($num_of_day-1) *86400);
            
            $this->m->start_date = $start_month;
            $this->m->end_date = strtotime(date("Y-m-".date("t",$start_month)." H:i:s"));
            
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`fullname`"
                        . " FROM `tasks` "
                        . " LEFT JOIN `users` ON `users`.`id` = `tasks`.`user_id`"
                        . " WHERE `tasks`.`date` > '".date("Y-m-d 00:00:00",$this->m->start_date)."'"
                        . " AND `tasks`.`date` < '".date("Y-m-d 23:59:59",$this->m->end_date)."'"
                        . " AND `tasks`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            
            foreach($data as $item){
                $this->m->data[date("Y-m-d",strtotime($item->date))][] = $item;
            }            
        }
        
        public function weeklyAction(){
            $start_week = date("Y-m-d H:i:s",strtotime('monday'));
            $end_week = date("Y-m-d H:i:s",strtotime('monday')+518400);
            
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`fullname`"
                        . " FROM `tasks` "
                        . " LEFT JOIN `users` ON `users`.`id` = `tasks`.`user_id`"
                        . " WHERE `tasks`.`date` > '".$start_week."'"
                        . " AND `tasks`.`date` < '".$end_week."'"
                        . " AND `tasks`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            
            foreach($data as $item){
                //p(date("N",strtotime($item->date)));
                $start = strtotime(date('Y-m-d 00:00:00',strtotime($item->date)));
                $this->m->data[date("N",strtotime($item->date))][strtotime($item->date) - $start][] = $item;
            }            
        }
        
        public function dailyAction(){
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`fullname`"
                        . " FROM `tasks` "
                        . " LEFT JOIN `users` ON `users`.`id` = `tasks`.`user_id`"
                        . " WHERE `tasks`.`date` > '".date("Y-m-d 00:00:00")."'"
                        . " AND `tasks`.`date` < '".date("Y-m-d 23:59:59")."'"
                        . " AND `tasks`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            
            foreach($data as $item){
                $this->m->data[strtotime($item->date)][] = $item;
            }
        }
        
        public function pipeAction(){
            $this->m->addJS('angular-drag-and-drop-lists.min');
            
            //получаем задачи за сегодня и за завтра 
            $date_start = date("Y-m-d 00:00:00",strtotime('-1 day'));   //на день раньше
            
            $date_end = date("Y-m-d 23:59:59",strtotime('tomorrow'));
            
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`fullname` as username"
                        . " , `supers`.`email` as partnername"
                        . " FROM `tasks`"
                        . " LEFT JOIN `users` ON `users`.`id`  = `tasks`.`user_id`"
                        . " LEFT JOIN `supers` ON `supers`.`id`  = `tasks`.`parent`"
                        . " WHERE `tasks`.`status` = 1"
                        . " AND `tasks`.`date` >= '".$date_start."' "
                        . " AND `tasks`.`date` <= '".$date_end."' "
                    );
            $data = $this->m->_db->loadObjectList();
            
            //делим на сегодня и на завтра 
            foreach($data as $item){
                
                if(date("Y-m-d",time()) > date("Y-m-d",strtotime($item->date))){
                    $this->m->data->yesterday[] =  $item;  
                }else if(date("Y-m-d",time()) == date("Y-m-d",strtotime($item->date))){
                    $this->m->data->today[] =  $item;  
                }else{
                    $this->m->data->tomorrow[] = $item;
                }
                $item->date = strtotime($item->date);
            }            
        }
        
        public function indexAction(){
            //получаем задачи за сегодня и за завтра 
            $date_start = date("Y-m-d 00:00:00",time());
            $date_end = date("Y-m-d 23:59:59",strtotime('tomorrow'));
                        
            $this->m->_db->setQuery(
                        "SELECT COUNT(`tasks`.`id`) "
                        . " FROM `tasks`"
                        . " WHERE 1"
                    );
            $total = $this->m->_db->loadresult();
            
            $xNav = new xNav("/tasks/".($filter? 'index/'.$filter.'/':'' ), $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
            
            $this->m->_db->setQuery(
                        "SELECT `tasks`.* "
                        . " , `users`.`fullname` as username"
                        . " , `supers`.`email` as parentname"
                        . " FROM `tasks`"
                        . " LEFT JOIN `users` ON `users`.`id`  = `tasks`.`user_id`"
                        . " LEFT JOIN `supers` ON `supers`.`id`  = `tasks`.`parent`"
                        . " WHERE 1"
                        //. " AND `tasks`.`date` >= '".$date_start."' "
                        //. " AND `tasks`.`date` <= '".$date_end."' "
                        . " ORDER BY `date` DESC"
                        . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        public function addAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if(empty($_POST)){
                $_POST = json_decode(file_get_contents('php://input'), true);
            }
            
            if($_POST['user_id']){
                $row->user_id = $_POST['user_id'];
            }else{
                $login = strip_tags(trim($_POST['login']));
                //получаем айди по логину
                $this->m->_db->setQuery(
                            "SELECT `users`.`id` "
                            . " FROM `users` "
                            . " WHERE `users`.`login` = '".$login."'"
                            . " LIMIT 1"
                        );
                $row->user_id = $this->m->_db->loadResult();
            }
            $row->parent = $this->m->_user->id;
            $row->comment = $_POST['comment'];
            $row->date = date("Y-m-d H:i:s",strtotime($_POST['date']) + $_POST['time']);
            $row->created = date("Y-m-d H:i:s");
            $row->status = 1;
            
            if($this->m->_db->insertObject('tasks',$row)){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
    }
?>