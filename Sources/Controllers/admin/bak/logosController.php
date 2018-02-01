<?php
    class logosController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
                
        public function indexAction(){
            $this->m->_db->setQuery(
                        "SELECT COUNT(`channel_logos`.`id`) "
                        . " FROM `channel_logos`"                        
                    );
            $total = $this->m->_db->loadResult();
            
            $xNav = new xNav("/logos/".($filter? 'index/'.$filter.'/':'' ), $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
            
            $this->m->_db->setQuery(
                        "SELECT `channel_logos`.* "
                        . " , `channels`.`id` as channel_id"
                        . " , `channels`.`name` as channel_name"
                        . " FROM `channel_logos`"
                        . "LEFT JOIN `channels` ON `channels`.`logo_id` = `channel_logos`.`id`"
                        . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                    );
            $data = $this->m->_db->loadObjectList();
            
            foreach($data as $item){
                $this->m->data[$item->id]->id = $item->id;
                $this->m->data[$item->id]->filename = $item->filename;
                $this->m->data[$item->id]->date = $item->date;
                $this->m->data[$item->id]->status = $item->status;
                
                $this->m->data[$item->id]->channels[$item->channel_id]->id = $item->channel_id;
                $this->m->data[$item->id]->channels[$item->channel_id]->name = $item->channel_name;
            }
            
            //Получаем Группы для списка груп при добавление нового канала
            //получаем список групп
            $this->m->_db->setQuery(
                        "SELECT `groups`.* "
                        . " FROM `groups` "
                        . " WHERE `groups`.`status` = 1"
                        
                    );
            $this->m->groups = $this->m->_db->loadObjectList();
        }
        
        public function removeAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `channel_logos`.* "
                        . " FROM `channel_logos`"
                        . " WHERE `channel_logos`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($logo);
            
            if(!$logo){
                echo '{"status":"error","message":"Не найден такой айдишник"}';
                return false;
            }
            
            $this->m->_db->setQuery(
                        "DELETE FROM `channel_logos` "
                        . " WHERE `channel_logos`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                xload('class.images');
                $images = new Images($this->m);
                $images->unlinkOld($logo->filename,[''],$this->m->config->assets_path);
                
                //отвязываем иконку от каналов
                $this->m->_db->setQuery(
                            "UPDATE `channels` "
                            . " SET `channels`.`logo_id` = 0 "
                            . " WHERE `channels`.`logo_id` = ".$id
                        );
                $this->m->_db->query();
                
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error","message":"ошибка удаления"}';
                return;
            }
        }
        
        public function addlogoAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path);
                if($images->validation == true) $images->saveThumbs(array(array(65,44,'')));
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    $row->filename = $images->filename;
                    $row->hash = md5(file_get_contents($this->m->config->assets_path.DS.$images->filename));
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('channel_logos',$row);
                    
                    /*$this->m->_db->setQuery(
                                "UPDATE `channel_logos` "
                                . " SET `channel_logos`.`filename` = '".$images->filename."'"
                                . " WHERE `channel_logos`.`id` = ".$logo->id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        
                        
                    }*/
                }
                
            }
        }
        
        public function editlogoAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                //проверяем или такой лого есть
                $id = (int)$_GET['id'];
                $this->m->_db->setQuery(
                            "SELECT `channel_logos`.`id` "
                            . " , `channel_logos`.`filename`"
                            . " FROM `channel_logos`"
                            . " WHERE `channel_logos`.`id` = ".$id
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($logo);
                if(!$logo) return;
                
                xload('class.images');
                $images = new Images($this->m);
                
                $images->initImage($_FILES, $this->m->config->assets_path);
                if($images->validation == true) $images->saveThumbs(array(array(65,44,'')));
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    /*xload('class.admin.channels');
                    $channels = new Channels($this->m);

                    $this->m->logo_id = $channels->addLogo($this->m->filename);
                    $this->m->filename = $channels->filename;*/
                    
                    $this->m->_db->setQuery(
                                "UPDATE `channel_logos` "
                                . " SET `channel_logos`.`filename` = '".$images->filename."'"
                                . " WHERE `channel_logos`.`id` = ".$logo->id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        $images->unlinkOld($logo->filename,['']);
                    }else{
                        p($this->m->_db->_sql);
                    }
                    
                    
                }                
            }
        }
        
        public function getfilenameAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "SELECT `channel_logos`.`filename` "
                        . " FROM `channel_logos` "
                        . " WHERE `channel_logos`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $result = $this->m->_db->loadResult();
            if($result){
                echo '{"status":"success","filename":"'.$result.'"}';
            }else{                
                echo '{"status":"error"}';
            }
        }
    }
?>