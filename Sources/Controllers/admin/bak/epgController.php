<?php
    class epgController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        public function indexAction(){
            
        }
        
        public function getepgchannelslistAction(){
            $this->disableView();
            $this->disableTemplate();
            
            $setting_id = (int)$_GET['setting_id'];
            $this->m->_db->setQuery(
                        "SELECT `epg_channels`.* "
                        . " FROM `epg_channels` "
                        . " WHERE `epg_channels`.`setting_id` = ".$setting_id
                        . " AND `epg_channels`.`status` = 1"
                    );            
            $data = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
        
        public function visualAction(){
            $this->m->filter->date_from = $_GET['date_from']?$_GET['date_from'] : date("Y-m-d 00:00:00");
            $this->m->filter->date_to = $_GET['date_to']?$_GET['date_to'] : date("Y-m-d 23:59:59");
            $this->m->filter->channel = (int)$_GET['channel'];
            
            $this->m->filter->setting_id = (int)$_GET['settings'];
            $this->m->filter->ch_id = (int)$_GET['ch_id'];
            
            //получаем список каналов
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " FROM `channels`"
                        . " WHERE `channels`.`status` = 1"
                        //. " AND `channels`.`EPG` = 1"
                    );
            $this->m->channels = $this->m->_db->loadObjectList();
            
            //получаем список сетингов
            $this->m->_db->setQuery(
                        "SELECT `epg_settings`.* "
                        . " FROM `epg_settings`"
                        . " WHERE `epg_settings`.`status` = 1"
                    );
            $this->m->settings = $this->m->_db->loadObjectList();
            
            if($this->m->filter->setting_id && $this->m->filter->ch_id){    //если выбран сеттинг и епг канал то он в приоритете
                $epg_id = $this->m->filter->ch_id;
                $setting_id = $this->m->filter->setting_id;
            }else if($this->m->filter->channel){  //если есть выбраный канал то получаем откуда он тянет ЕПГ
                $this->m->_db->setQuery(
                            "SELECT `channels`.* "
                            . " , `epg_channels`.`ch_id`"
                            . " , `epg_channels`.`setting_id`"
                            . " FROM `channels`"
                            . " LEFT JOIN `epg_channels` ON `epg_channels`.`id` = `channels`.`epg`"
                            . " WHERE `channels`.`id` = ".$this->m->filter->channel
                            
                            . " AND `channels`.`status` = 1"
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($channel);
                
                $epg_id = $channel->ch_id;
                $setting_id = $channel->setting_id;
            }
            
            //получаем ЕПГ по какому то каналу за сегодня 
            $this->m->_db->setQuery(
                        "SELECT `epg_programs`.* "
                        . " FROM `epg_programs`"
                        . " WHERE `epg_programs`.`channel_id` = ".$epg_id
                        . " AND `epg_programs`.`setting_id` = ".$setting_id
                        
                        . " AND ((`epg_programs`.`start` < '".$this->m->filter->date_to."' AND `epg_programs`.`start` > '".$this->m->filter->date_from."')"
                        . " OR (`epg_programs`.`stop` > '".$this->m->filter->date_from."' AND `epg_programs`.`stop` < '".$this->m->filter->date_to."'))"
                    );
            
            $this->m->data = $this->m->_db->loadObjectList();
            
            foreach($this->m->data as $item){
                $item->start = strtotime($item->start);
                $item->stop = strtotime($item->stop);
            }            
        }
        
        public function getchannelslistAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `epg_channels`.* "
                        . " FROM `epg_channels` "
                        . " WHERE `epg_channels`.`setting_id` = ".$id
                        . " AND `epg_channels`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
        
        public function updateAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.epg');
            $epg = new EPG($this->m);
            $epg->updateEPG($_GET['id']);            
        }
        
        public function addsettingAction(){
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            
            $this->disableTemplate();
            $this->disableView();
            $this->validation = true;
            
            $row->url = strip_tags(trim($_POST['link']));
            $row->name = strip_tags(trim($_POST['name']));
            $row->status = 1;
            $row->date = date("Y-m-d H:i:s");
            
            if(!$row->url){
                $this->error->link = 'Вы должны ввести ссылку';
                $this->validation = false;
            }
            
            if(!$row->name){
                $this->error->name = 'Вы должны ввести название';
                $this->validation = false;
            }
            
            if($this->validation == false){
                echo '{"status":"error","messages":'.json_encode($this->error).'}';
                return false;
            }
            
            if($this->m->_db->insertObject('epg_settings',$row)){
                echo '{"status":"success"}';    
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function settingsAction(){
            $this->m->_db->setQuery(
                        "SELECT `epg_settings`.* "
                        . " FROM `epg_settings`"
                        . " WHERE `epg_settings`.`status` = 1"
                    );
            $this->m->data = $this->m->_db->loadObjectList();            
        }
        
        public function programsAction(){
            //Получаем список сеттингов для селекта
            $this->m->_db->setQuery(
                        "SELECT `epg_settings`.* "
                        . " FROM `epg_settings` "
                        . " WHERE `epg_settings`.`status` = 1"
                    );
            $this->m->settings = $this->m->_db->loadObjectList();
            ///////////////
            
            $this->m->filter->setting = (int)$_GET['setting'];
            $this->m->filter->channel = (int)$_GET['channel'];
            
            //выставляем даты начала и конца по умолчанию если их нету
            $this->m->filter->start = $_GET['date_from']? date("Y-m-d H:i:s",strtotime($_GET['date_from'])) : date("Y-m-d 00:00:00",strtotime('-7 days'));
            $this->m->filter->end = $_GET['date_to'] ? date("Y-m-d H:i:s",strtotime($_GET['date_to'])) : date("Y-m-d 23:59:59");
                        
            $sql = ($this->m->filter->setting ? " AND `epg_programs`.`setting_id` = ".$this->m->filter->setting : '')
                        .($this->m->filter->channel ? " AND `epg_programs`.`channel_id` = ".$this->m->filter->channel : '')
                        . ($this->m->filter->start ? " AND `epg_programs`.`start` > '".$this->m->filter->start."'" : "")
                        . ($this->m->filter->start ? " AND `epg_programs`.`start` < '".$this->m->filter->end."'" : "")
                    ;
            
            $this->m->_db->setQuery(
                        "SELECT COUNT(`epg_programs`.`id`) "
                        . " FROM `epg_programs`"
                        . " WHERE 1"
                        . $sql
                    );
            $total = $this->m->_db->loadResult();
            
            $xNav = new xNav("/epg/programs/".($subpage? 'index/'.$subpage.'/':'' ), $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
            
            $this->m->_db->setQuery(
                        "SELECT `epg_programs`.* "
                        . " , `epg_channels`.`name` as 'channel_name'"
                        . " , `epg_settings`.`name` as 'setting_name'"
                        . " FROM `epg_programs`"
                        . " LEFT JOIN `epg_channels` ON `epg_channels`.`ch_id` = `epg_programs`.`channel_id`"
                        . " LEFT JOIN `epg_settings` ON `epg_settings`.`id` = `epg_programs`.`setting_id`"
                        . " WHERE 1"
                        . $sql
                        . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""                        
                    );            
            $this->m->data = $this->m->_db->loadObjectList();            
        }
        
        public function channelsAction(){
            $this->m->_db->setQuery(
                        "SELECT COUNT(`epg_channels`.`id`) "
                        . " FROM `epg_channels`"
                    );
            $total = $this->m->_db->loadResult();
            
            $xNav = new xNav("/contacts/".($filter? 'index/'.$filter.'/':'' ), $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
            
            $this->m->_db->setQuery(
                        "SELECT `epg_channels`.* "
                        . " FROM `epg_channels`"
                        . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                    );
            $this->m->data = $this->m->_db->loadObjectList();
            
            //получаем канал
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " FROM `channels`"
                        . " WHERE `channels`.`id` = 2"
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($channel);
                        
            //получаем айдиканала
            $this->m->_db->setQuery(
                        "SELECT * FROM `epg_channels`"
                        . " WHERE `epg_channels`.`id` = 75"
                    );
            $this->m->_db->loadObject($epg_channel);
            
            //получаем епг которое сейчас идет 
            $this->m->_db->setQuery(
                        "SELECT `epg_programs`.* "
                        . " FROM `epg_programs` "
                        . " WHERE `epg_programs`.`channel_id` = ".$epg_channel->ch_id
                        . " AND `epg_programs`.`setting_id` = ".$epg_channel->setting_id
                        . " AND `epg_programs`.`start` < '".date("Y-m-d H:i:s")."'"
                        //. " AND `epg_programs`.`stop` > '".date("Y-m-d H:i:s")."'"
                        . " LIMIT 2"
                        
                    );
            $programs = $this->m->_db->loadObjectList();            
        }
    }
?>
