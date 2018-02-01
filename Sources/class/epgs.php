<?php
class EPGS{
    protected $_table = 'users';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function updateEPG(){
        //ini_set("memory_limit", "256M");
        ini_set('max_execution_time', 300); 
        //получаем  
        $this->m->_db->setQuery(
                    "SELECT `epg_settings`.* "
                    . " FROM `epg_settings` "
                    . " WHERE `epg_settings`.`status` = 1"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($settings);
        
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
        $xml = simplexml_load_file($contents);
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