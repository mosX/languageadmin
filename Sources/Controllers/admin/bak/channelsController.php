<?php
    class channelsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        /*public function userAction(){
            $user_id = $this->m->_path[2];
            
            //получаем все каналы данного пользователя            
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " FROM `channels` "
                        . " WHERE `channels`.`user_id` = ".$user_id
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }*/
        
        public function indexAction(){
            xload('class.admin.channels');
            $channels = new Channels($this->m);
            $this->m->data = $channels->getChannels();
            
            xload('class.admin.settings');
            $settings = new Settings($this->m);
            $this->m->settings = $settings->getSettingsList();
            
            //получаем список групп
            $this->m->_db->setQuery(
                        "SELECT `groups`.* "
                        . " FROM `groups` "
                        . " WHERE `groups`.`status` = 1"
                        
                    );
            $this->m->groups = $this->m->_db->loadObjectList();
        }
        
        public function publishAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            
            $status = $status ? 1 : 0;
            
            if(!$id)return;
                        
            $this->m->_db->setQuery(
                        "UPDATE `channels` SET `channels`.`status` = ".$status
                        . " WHERE `channels`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","result":"'.$status.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
    
        public function hideAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true);            
            $user_id = (int)$_POST['user_id'];
            $channels = array_unique($_POST['channels']);
            
            //удаляем старые если есть
            $this->m->_db->setQuery(
                        "DELETE FROM `sequences` "
                        . " WHERE `sequences`.`user_id` = ".$user_id
                    );
            $this->m->_db->query();
            
            //теперь добавляем порядок 
            $row->user_id = $user_id;
            $row->date = date("Y-m-d H:i:s");
            
            foreach($channels as $number=>$item){
                $row->number = $number+1;
                $row->channel_id = $item;
                $this->m->_db->insertObject('sequences',$row);
            }
            
            echo '{"status":"success"}';
        } 
        
        public function viewsAction(){
            $this->m->_db->setQuery(
                        "SELECT `views`.* "
                        . " , `channels`.`name`"
                        . " , `users`.`login`"
                        . " FROM `views`"
                        . " LEFT JOIN `channels` ON `channels`.`id` = `views`.`channel_id`"
                        . " LEFT JOIN `users` ON `users`.`id` = `views`.`user_id`"
                        . " WHERE `views`.`status` = 1"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
            //p($this->m->data);
        }
        
        public function personalizationAction(){
            $this->m->addJS('angular-drag-and-drop-lists.min');
            $user_id = (int)$this->m->_path[2];
            //получаем все доступные каналы
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " , `channel_logos`.`filename`" 
                        . " FROM `channels` "
                        . " LEFT JOIN `channel_logos` ON `channels`.`logo_id` = `channel_logos`.`id`"
                        . " WHERE `channels`.`status` = 1"
                        . " AND (`channels`.`user_id` = 0 || `channels`.`user_id` = ".$user_id.")"
                        . " ORDER BY `channels`.`number`"
                    );
            $this->m->channels = json_encode($this->m->_db->loadObjectList());
            
            //получаем все каналы и лефтджойним из таблицы с порядком 
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " , `channel_logos`.`filename`"
                        . " , `sequences`.`number` as 'order'"
                        . " FROM `channels` "
                        . " LEFT JOIN `channel_logos` ON `channels`.`logo_id` = `channel_logos`.`id`"
                        . " LEFT JOIN `sequences` ON `sequences`.`channel_id` = `channels`.`id`"
                        . "  WHERE `channels`.`status` = 1"
                        . " AND `sequences`.`user_id` = ".$user_id
                        . " ORDER BY `sequences`.`number`,`channels`.`number`"
                    );
            //$this->m->personal = json_encode($this->m->_db->loadObjectList());
            $data = $this->m->_db->loadObjectList();
            $cnt = 0;
            foreach($data as $item){    //пробегаемся по массиву и отбераем отсортированнЫе... если сортировки нету то выводим все.
                if($item->order){
                    $cnt ++;
                    $sequences[] = $item;    
                }                
            }
            if($cnt > 0){
                $this->m->personal = json_encode($sequences);
            }else{
                $this->m->personal = json_encode($data);
            }
        }
        
        public function deleteAction(){
            $this->disableTemplate();
            $this->disableView();
            $channel_id = (int)$_GET['id'];
            $this->m->_db->setQuery(
                        "UPDATE `channels` "
                        . " SET `channels`.`status` = 0"
                        . " WHERE `channels`.`id` = ".$channel_id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{                
                echo '{"status":"error"}';
            }            
        }
        
        public function sequenceAction(){
            //получаем каналы
            $this->m->addJS('angular-drag-and-drop-lists.min');
            
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " , `channel_logos`.`filename`" 
                        . " FROM `channels` "
                        . " LEFT JOIN `channel_logos` ON `channels`.`logo_id` = `channel_logos`.`id`"
                        //. " WHERE `channels`.`status` = 1"
                        . " ORDER BY `number`"
                    );
            $channels = $this->m->_db->loadObjectList();
            
            $this->m->json = json_encode($channels);
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json    
                //обновляем нумерацию каналам
                
                if(!$_POST) return;
                
                foreach($_POST as $item){
                    //p($item['status']);
                    $this->m->_db->setQuery(
                                "UPDATE `channels` SET "
                                . " `channels`.`status` = ".$item['status']
                                . ($item['status'] == 1? " , `channels`.`number` = '".$item['number']."'":'')
                                
                                . " WHERE `channels`.`id` = ".$item['id']
                                . " LIMIT 1"
                            );
                    $this->m->_db->query();  
                    
                    
                }
                
                echo '{"status":"success"}';                
            }
        }
        
        public function loadeditlogoAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'logos');
                //if($images->validation == true) $images->saveThumbs(array(array(65,44,'')));
                
                if($images->validation == true){
                    $images->saveThumbs(array(array(200,200,''),array(70,70,'thumb_'),array(30,30,'small_')));
                }
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    xload('class.admin.channels');
                    $channels = new Channels($this->m);

                    $this->m->filename = $images->filename;
                    $this->m->status = 'success';

                    //$photos->unlinkOld(Auth::user()->ava,['thumb','small','']); //удалить старые файлы
                    $this->m->logo_id = $channels->addLogo($this->m->filename);
                    p($this->m->logo_id);
                    $this->m->filename = $channels->filename;
                    //p($this->m->filename);
                }
            }
        }
        
        public function blockAction(){
            $this->disableTemplate();
            $this->disableView();
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            
            $id = (int)$_POST['id'];
            $date = $_POST['date'];
            
            //проверяем или такой пользователь есть 
            $this->m->_db->setQuery(
                        "SELECT * FROM `users` WHERE `users`.`id` = ".$id  
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($user);
            
            if(!$user){
                echo '{"status":"error"}';
                return;
            }
            
            $row->user_id = $id;
            if($date){
                $row->till_date = date("Y-m-d H:i:s",strtotime($date));
            }
            
            $row->date = date("Y-m-d H:i:s");
            if($this->m->_db->insertObject('blocks',$row)){
                echo '{"status":"success"}';
            }else{
                
                echo '{"status":"error"}';
            }            
        }
        
        public function editdataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.channels');
            $channels = new Channels($this->m);
            $channel = $channels->editForm($_GET['id']);
            
            xload('class.admin.epg');
            $epg = new EPG($this->m);
            $epgs = json_encode($epg->getEpgs($channel->setting_id));
            
            echo '{"list":'.$epgs.',"channel":'.json_encode($channel).'}';  
        }
        
        public function listAction(){
            $this->disableTemplate();
            
        $this->disableView();
            xload('class.admin.epg');
            $epg = new EPG($this->m);
            $data = $epg->getEpgChannelsList((int)$_GET['id']);
            
            echo json_encode($data);
        }
        
        public function editAction(){
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.channels');
            $channels = new Channels($this->m);
            if($channels->edit()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error","message":'.json_encode($channels->errors).'}';
            }
        }
        
        public function addAction(){
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.channels');
            $channels = new Channels($this->m);
            if($channels->add()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error","message":'.json_encode($channels->errors).'}';
            }           
        }
    }
?>