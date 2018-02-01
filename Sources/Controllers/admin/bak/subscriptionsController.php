<?php
    class subscriptionsController extends Model {
        public function init(){
            
        }
        
        public function indexAction(){
            
        }
        
        public function plansAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();

                $_POST = json_decode(file_get_contents('php://input'), true);
                $id = (int)$_POST['id'];
                
                $name = $_POST['name'];
                $price = (int)$_POST['price']*100;
                $description = $_POST['description'];
                
                if($id){
                    $this->m->_db->setQuery(
                                "UPDATE `plans` SET `plans`.`name` = '".$name."'"
                                . " , `plans`.`price` = ".(int)$price
                                . " , `plans`.`description` = '".$description."'"
                                . " WHERE `plans`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    $row->name = $name;
                    $row->price = (int)$price;
                    $row->description = $description;
                    $row->date = date("Y-m-d H:i:s");
                    if($this->m->_db->insertObject('plans',$row)){
                        echo '{"status":"success"}';
                    }else{
                        //p($this->m->_db->_sql);
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `plans`.* "
                            . " , COUNT(`plan_channels`.`id`) as channels"
                            . " FROM `plans` "
                            . " LEFT JOIN `plan_channels` ON `plan_channels`.`plan_id` = `plans`.`id` AND `plan_channels`.`status` = 1"
                            . " WHERE `plans`.`status` = 1"
                            . " GROUP BY `plan_channels`.`plan_id`"
                            . " ORDER BY `plans`.`id` DESC"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function deleteAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id){
                echo '{"status":"error"}';
                return false;
            }
            
            $this->m->_db->setQuery(
                        "UPDATE `plans` SET `plans`.`status` = 0"
                        . " WHERE `plans`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function delete_plan_channelAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id){
                echo '{"status":"error"}';
                return false;
            }
            
            $this->m->_db->setQuery(
                        "UPDATE `plan_channels` "
                        . " SET `plan_channels`.`status` = 0"
                        . " WHERE `plan_channels`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"erorr"}';
            }
        }
        
        public function plan_channelsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);            
                
                $row->channel_id  = (int)$_POST['channel'];
                $row->plan_id = (int)$this->m->_path[2];
                $row->date  = date("Y-m-d H:i:s");
                
                //проверяем или такого канала нету в списке
                $this->m->_db->setQuery(
                            "SELECT `plan_channels`.* "
                            . " FROM `plan_channels`"
                            . " WHERE `plan_channels`.`channel_id` = ".$row->channel_id
                            . " AND `plan_channels`.`plan_id` = ".$row->plan_id
                            . " AND `plan_channels`.`status` = 1"
                            . " LIMIT 1"
                        );
                $check = $this->m->_db->loadResult();
                if($check){
                    echo '{"status":"error","message":"Такой канал уже был добавлен"}';
                    return false;
                }
                
                if($this->m->_db->insertObject('plan_channels',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }else{
                //получаем список каналов
                $this->m->_db->setQuery(
                            "SELECT `channels`.* "
                            . " FROM `channels` "
                            . " WHERE `channels`.`status` = 1"
                        );
                $this->m->channels = $this->m->_db->loadObjectList();

                $this->m->_db->setQuery(
                            "SELECT `plan_channels`.* "
                            . " , `channels`.`name`"
                            . " , `channel_logos`.`filename`"
                            . " FROM `plan_channels` "
                            . " LEFT JOIN `channels` ON `channels`.`id` = `plan_channels`.`channel_id`"
                            . " LEFT JOIN `channel_logos` ON `channel_logos`.`id` = `channels`.`logo_id`"
                            . " WHERE `plan_channels`.`status` = 1"
                            . " AND `plan_channels`.`plan_id` = ".$this->m->_path[2]
                        );
                $this->m->data = $this->m->_db->loadObjectList();                
            }
        }
        
        public function plans_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `plans`.* "
                        . " FROM `plans` WHERE `plans`.`id` = ".$id
                        . " AND `plans`.`status` = 1"
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
    }
?>