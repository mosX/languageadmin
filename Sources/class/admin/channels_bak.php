<?php
class Channels{
    protected $_table = 'channels';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function add(){
        $this->validation = true;
        $row->description = strip_tags(trim($_POST['description']));
        //$row->number = $this->validateNumber((int)$_POST['number']);
        $row->association = (int)$_POST['streamid'];
        $row->name = $this->validateName($_POST['name']);        
        $row->user_id = (int)$_POST['user_id'];
        
        /*if($row->user_id){
            $row->url = strip_tags(trim($_POST['url']));
        }else{
            $row->association = strip_tags(trim($_POST['association']));
        }*/
        $row->url = strip_tags(trim($_POST['url']));
        
        //$row->url = $this->validateURL($_POST['url']);
        //$row->archive_url = $_POST['archive_url'];
        $row->logo_id = (int)$_POST['logo_id'];
        //$row->status = 1;
        $row->group_id = (int)$_POST['group_id'];
        $row->date = date("Y-m-d H:i:s");
        $row->epg = $_POST['epg'];        
        
        if(!$this->validation){
            return false;   
        }

        if($this->m->_db->insertObject('channels',$row)){
            return true;            
        }else{            
            
            return false;            
        }
    }
    
    public function addLogo($filename){
        //провреяем md5 файла
        //$hash = md5(file_get_contents(XPATH.DS.'assets'.DS.$filename));
        
        $hash = md5(file_get_contents($this->m->config->assets_path.DS.'logos'.DS.$filename));
        
        $this->m->_db->setQuery(
                    "SELECT `channel_logos`.* "
                    . " FROM `channel_logos`"
                    . " WHERE `channel_logos`.`hash` = '".$hash."'"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($logo);
                
        if($logo){
            if(file_exists($this->m->config->assets_path.DS.'logos'.DS.$logo->filename)){
                if(file_exists($this->m->config->assets_path.DS.'logos'.DS.$filename)){    //удаляем старый файли
                   unlink($this->m->config->assets_path.DS.'logos'.DS.$filename);
                   unlink($this->m->config->assets_path.DS.'logos'.DS.'thumb_'.$filename);
                   unlink($this->m->config->assets_path.DS.'logos'.DS.'small_'.$filename);
                }
            }else{
                $this->m->_db->setQuery(
                            "UPDATE `channel_logos` "
                            . " SET `channel_logos`.`filename` = '$filename'"
                            . " WHERE `channel_logos`.`id` = ".$logo->id
                            . " LIMIT 1"
                        );
                $this->m->_db->query();
                
                $logo->filename = $filename;
            }
            
            $this->filename = $logo->filename;
            return $logo->id;
        }else{
            $this->filename = $filename;
            $row->filename = strip_tags(trim($filename));
            $row->date = date("Y-m-d H:i:s");
            $row->hash = $hash;
            $row->status = 1;

            if($this->m->_db->insertObject('channel_logos',$row,'id')){
                return $row->id;
            }else{
                p($this->m->_db->_sql);
            }
        }
    }
    
    public function edit(){
        $this->validation = true;
        
        //$row->number = $this->validateNumber((int)$_POST['number']);
        $row->name = $this->validateName($_POST['name']);
        $row->association = (int)$_POST['streamid'];
        $row->description = strip_tags(trim($_POST['description']));
        
        //$row->url = $this->validateURL($_POST['url']);
        //$row->archive_url = $_POST['archive_url'];
        //$row->logo_id = $_POST['logo_id'];
        
        /*if($_POST['user_id']){
            $row->url = strip_tags(trim($_POST['url']));
        }else{
            $row->association = strip_tags(trim($_POST['association']));
        }*/
        $row->url = strip_tags(trim($_POST['url']));
        
        //$row->status = 1;
        $row->date = date("Y-m-d H:i:s");
        $row->group_id = (int)$_POST['group_id'];
        $row->epg = $_POST['epg'];
        $row->logo_id = (int)$_POST['logo_id'];
        
        $row->id = $_POST['id'];
        
        if(!$this->validation){
            return false;
        }

        if($this->m->_db->updateObject('channels',$row,'id')){
            return true;
        }else{
            return false;
        }
    }
    
    public function validateURL($url){
        $url = strip_tags(trim($url));
        
        if(!$url){
            $this->validation = false;
            $this->errors->url = 'Вы должны ввести адрес потока';
            return false;
        }
        
        return $url;
    }
    
    public function validateName($name){
        $name = strip_tags(trim($name));
        
        if(!$name){
            $this->validation = false;
            $this->errors->name = 'Вы не ввели название канала';
            return false;
        }
        
        return $name;
    }
    
    public function validateNumber($number){
        $number = (int)$number;
        if(!$number){   //если номер не ввели то получаем сами подходящий номер
            /*$this->validation = false;
            $this->errors->number = 'Вы не ввели номер канала';            
            return false;*/
            
            $this->m->_db->setQuery(
                        "SELECT MAX(`channels`.`number`) as max "
                        . " FROM `channels` "
                        . " WHERE `channels`.`status` != -1"                        
                    );
            $number = $this->m->_db->loadResult()+1;
        }
        
        //проверяем уникальность номера
        $this->m->_db->setQuery(
                    "SELECT COUNT(`channels`.*) "
                    . " FROM `channels` "
                    . " WHERE `channels`.`number` = ".$number
                    . " AND `channels`.`status` = 1"
                    . " LIMIT 1"
                );
        $cnt = $this->m->_db->loadResult();
        
        if($cnt){
            $this->validation = false;
            $this->errors->number = 'Такой номер уже существует. выберите другой';
            return false;
        }
        
        return $number;
    }
    
    public function getChannels(){
        $filter = $this->m->_path[2];
        $name = trim($_GET['name']);
        $date_from = $_GET['date_from'] ? date("Y-m-d H:i:s",strtotime($_GET['date_from'])):'';
        $date_to = $_GET['date_to'] ? date("Y-m-d H:i:s",strtotime($_GET['date_to'])):'';
        
        $sql = ($name ? " AND `channels`.`name` LIKE '%".$name."%'":'')
            .($date_from ? " AND `channels`.`date` > '".$date_from."'":'')
            .($date_to ? " AND `channels`.`date` < '".$date_to."'":'')
            ;
        
        $this->m->_db->setQuery(
                    "SELECT COUNT(`channels`.`id`) "
                    . " FROM `channels`"                                            
                    . " WHERE 1"
                    . $sql
                    . ($filter == 'active'?" AND `channels`.`status` = 1":'')
                    . ($filter == 'unactive'?" AND `channels`.`status` = 0":'')                                        
                );
        $total = $this->m->_db->loadResult();

        $xNav = new xNav("/channels/".($filter? 'index/'.$filter.'/':'' ), $total, "GET");
        $xNav->limit = 30;
        $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `channels`.* "
                    /*. " ,`epg_channels`.`setting_id`"
                    . " ,`epg_channels`.`name` as channel_name"
                    . " ,`epg_settings`.`name` as settings_name"*/
                    . " ,`channel_logos`.`filename` as filename"
                    . " , `groups`.`name` as group_name"
                    . " FROM `channels`"
                    . " LEFT JOIN `groups` ON `groups`.`id` = `channels`.`group_id`"
                    /*. " LEFT JOIN `epg_channels` ON `epg_channels`.`id` = `channels`.`epg`"
                    . " LEFT JOIN `epg_settings` ON `epg_settings`.`id` = `epg_channels`.`setting_id`"*/
                    . " LEFT JOIN `channel_logos` ON `channels`.`logo_id` = `channel_logos`.`id`"
                    . " WHERE 1"
                    . $sql
                    . ($filter == 'active'?" AND `channels`.`status` = 1":'')
                    . ($filter == 'unactive'?" AND `channels`.`status` = 0":'')                    
                    . " ORDER BY `id` ASC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        $data = $this->m->_db->loadObjectList('id');
        
        foreach($data as $item) $ids[] = $item->id;
        
        $this->m->_db->setQuery(
                    "SELECT COUNT(`views`.`id`) as cnt ,`views`.`channel_id`"
                    . " FROM `views` "
                    . " WHERE `views`.`channel_id` IN (".implode(",",$ids).")"
                    . " AND `views`.`status` = 1"
                    . " GROUP BY `views`.`channel_id`"
                );
        $views = $this->m->_db->loadObjectList();
        
        foreach($views as $item){
            $data[$item->channel_id]->watching = $item->cnt;
        }
        
        return $data;
    }
    
    public function editForm($id){
        $this->m->_db->setQuery(
                    "SELECT `channels`.* "
                    /*. " , `epg_settings`.`name` as setting_name"
                    . " , `epg_channels`.`setting_id`"*/
                    . " , `channel_logos`.`filename`"
                    . " FROM `channels` "
                    /*. " LEFT JOIN `epg_channels` ON `channels`.`id` = `epg_channels`.`id`"
                    . " LEFT JOIN `epg_settings` ON `epg_settings`.`id` = `epg_channels`.`setting_id`"*/
                    . " LEFT JOIN `channel_logos` ON `channels`.`logo_id` = `channel_logos`.`id` "
                    . " WHERE `channels`.`id` = ".$id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($channel);
        
        return $channel;
    }
}
?>