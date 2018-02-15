<?php
class EPG{
    protected $_table = 'epg';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function getEpgs($setting_id){
        $this->m->_db->setQuery(
                    "SELECT `epg_channels`.* "
                    . " FROM `epg_channels` "
                    . " WHERE `epg_channels`.`setting_id` = ".$setting_id
                    . " AND `epg_channels`.`status` = 1"
                );            
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    
    public function getEpgChannelsList($id){
        $this->m->_db->setQuery(
                    "SELECT `epg_channels`.`id` "
                    . " , `epg_channels`.`name`"
                    . " FROM `epg_channels` "
                    . " WHERE `epg_channels`.`setting_id` = ".$id
                    . " AND `epg_channels`.`status` = 1"
                );
        $data = $this->m->_db->loadObjectList();
        
        return $data;
    }
    
    public function updateEPG($id){
        //ini_set("memory_limit", "256M");
        ini_set('max_execution_time', 600); 
        //получаем  
        $this->m->_db->setQuery(
                    "SELECT `epg_settings`.* "
                    . " FROM `epg_settings` "
                    . " WHERE `epg_settings`.`status` = 1"
                    . " AND `epg_settings`.`id` = ".$id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($settings);
        
        if(!$settings){
            return false;
        }
        
        $handle = gzopen($settings->url, 'r');        
        //$handle = gzopen(XPATH.DS.'xmltv.xml.gz', 'r');
        
        $tmpfname = tempnam(XPATH.DS."tmp", "xmltv");        
        $fp = fopen($tmpfname, "w");
        
        while (!gzeof($handle)){
            $contents = gzread($handle, 1000000);
            fwrite($fp, $contents);
        }
        gzclose($handle);
        
        //$xml = simplexml_load_file(XPATH.DS.'xmltv.xml');
        $xml = simplexml_load_file($tmpfname); 
        $updated_to = strtotime($settings->updated_to);
        //p($settings->updated_to);
        $max_time = time();
        
        $row->setting_id = $settings->id;
        foreach ($xml->programme as $programme){
            $row->stop = strtotime(strval($programme->attributes()->stop));
            
            $max_time = $max_time < $row->stop ? $row->stop : $max_time;
            
            if($row->stop <= $updated_to) continue;
            $row->stop = date('Y-m-d H:i:s',$row->stop);
                
            $row->channel_id = strval($programme->attributes()->channel);
            $row->start = date("Y-m-d H:i:s",strtotime(strval($programme->attributes()->start)));
            
            $row->title = strval($this->getElementByLangCode($programme->title, 'ru'));
            $row->description = strval($this->getElementByLangCode($programme->desc, 'ru'));
            $row->category = strval($this->getElementByLangCode($programme->category, 'ru'));
            
            //проверяем или такой не добавлен
            $this->m->_db->insertObject('epg_programs',$row);            
        }
        
        //после єтого обновляем время в сеттингах 
        $this->m->_db->setQuery(
                    "UPDATE `epg_settings` "
                    . " SET `epg_settings`.`updated_to` = '".date("Y-m-d H:i:s",$max_time)."'"
                    . " WHERE `epg_settings`.`id` = ".$settings->id
                );                
        $this->m->_db->query();
        //p($this->m->_db->_sql);
        
        foreach ($xml->channel as $channel){
            $ch_row->ch_id = strval($channel->attributes()->id);
            $ch_row->setting_id = $settings->id;
            $ch_row->name = strval($this->getElementByLangCode($channel->{'display-name'}, 'ru'));            
            $ch_row->status = 1;
            
            $this->m->_db->insertObject('epg_channels',$ch_row);
        }

    }
    
    public function getElementByLangCode($element, $lang_code){
        if (empty($lang_code) || count($element) <= 1) {
            return $element;
        }
        $lang_code = explode(',', $lang_code);
        foreach ($lang_code as $lang) {
            foreach ($element as $row) {
                if ($row->attributes()->lang == $lang) {
                    return $row;
                }
            }
        }
        return $element;
    }
    
}
?>