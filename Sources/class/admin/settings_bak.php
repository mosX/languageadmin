<?php
class Settings{
    protected $_table = 'settings';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function getSettingsList(){
        //получаем список настроек для селекта
        $this->m->_db->setQuery(
                    "SELECT `epg_settings`.* "
                    . " FROM `epg_settings`"
                    . " WHERE `epg_settings`.`status` = 1"
                );
        $data = $this->m->_db->loadObjectList();
        return $data;
    }
   

}
?>