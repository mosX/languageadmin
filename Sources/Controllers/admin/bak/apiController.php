<?php
    class apiController extends Model {
        public function init(){
            $this->disableTemplate();
            $this->disableView();
        }
        
        public function indexAction(){
            
        }
        
        public function nimbleAction(){
            // Log entire incoming request to see what we have in it.
            /*$fp = fopen('/var/tmp/request.log', 'w');
            fwrite($fp, $HTTP_RAW_POST_DATA);
            fclose($fp);*/
            
            file_put_contents(XPATH.DS.'log.txt', $HTTP_RAW_POST_DATA);
            
            // Use this object for accessing each viewer's ID, IP and stream name.
            $sync_data = json_decode($HTTP_RAW_POST_DATA);
            // Return IDs of clients which needs to be denied. Their IDs are 1 and 2 here.
            // Those viewers will be disconnected immediatelly and will not be allowed to connect anymore.
            
            
            /*header('Content-Type: application/json');
            $arr = array('DenyList' => array('ID' => array(1, 2)));
            echo json_encode($arr);*/
        }
        
        public function handleJSON($postdata){
            $this->m->_db->setQuery(    //получаем каналы и их ассоциации
                        "SELECT `channels`.`id`,`channels`.`association` "
                        . "FROM `channels`"
                        . " WHERE `channels`.`status` = 1"
                    );
            $this->channels = $this->m->_db->loadObjectList('association');
            
            //$postdata = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            //p($postdata['PayPerViewInfo']['VHost'][0]['Application']);
            foreach($postdata['PayPerViewInfo']['VHost'][0]['Application'] as $item){                
                //p($item['Instance'][0]['Stream'][0]['name']);
                foreach($item['Instance'][0]['Stream'][0]['Player'] as $user){
                    
                    //проверяем или есть пользователь с таким айди и сессией
                    $this->m->_db->setQuery(
                                "SELECT `views`.`id` "
                                . " FROM `views` "
                                . " WHERE `views`.`user_id` = ".(int)$user['id']
                                . " AND `views`.`sessid` = '".$user['sessionid']."'"
                            );
                    $check = $this->m->_db->loadResult();
                    //p($check);
                    
                    if($check){ //обновляем
                        p('CHECK');     
                        $this->m->_db->setQuery(
                                    "UPDATE `views` SET `views`.`last_check` = '".date("Y-m-d H:i:s")."'"
                                    . " , `views`.`status` = 1"
                                    . " WHERE `views`.`user_id` = ".$user['id']
                                    . " AND `views`.`sessid` = '".$user['sessionid']."'"
                                    . " LIMIT 1"
                                );
                        $this->m->_db->query();
                    }else{      //добавляем                        
                        
                        p('ADD');
                        $row->user_id = $user['id'];
                        $row->channel_id = $this->channels[$item['Instance'][0]['Stream'][0]['name']]->id;
                        $row->ip = $user['ip'];
                        $row->sessid = $user['sessionid'];
                        $row->user_agent = $user['user_agents'][0];
                        $row->date = date("Y-m-d H:i:s");
                        $row->last_check = date("Y-m-d H:i:s");                        
                        
                        $this->m->_db->insertObject('views',$row);
                    }                    
                }
            }
            
            //обновляем все старые записи как не активные 
            $this->m->_db->setQuery(
                        "UPDATE `views` SET `views`.`status` = 0"
                        . " WHERE `views`.`last_check` <= '".date('Y-m-d H:i:s',(time()-10))."'"
                        . " AND `views`.`status` = 1"
                    );
            $this->m->_db->query();
        }
    }
?>