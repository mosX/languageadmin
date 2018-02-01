<?php
    class archiveController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            //получаем архивы
            $this->m->_db->setQuery(
                        "SELECT `archive`.* "
                        . " , `channels`.`name` as channel_name"
                        . " FROM `archive`"
                        . " LEFT JOIN `channels` ON `channels`.`id` =  `archive`.`channel_id`"
                        . " WHERE `archive`.`status` = 1"
                    );
            $this->m->data = $this->m->_db->loadObjectList();            
            
            //получаем список каналов
            $this->m->_db->setQuery(
                        "SELECT `channels`.* "
                        . " FROM `channels` "
                        . " WHERE `channels`.`status` = 1"
                    );
            $this->m->list = $this->m->_db->loadObjectList();
        }
        
        public function addAction(){
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            
            $this->disableTemplate();
            $this->disableView();
            
            $row->channel_id = (int)$_POST['channel_id'];
            $row->start = date("Y-m-d H:i:s",strtotime($_POST['start']));
            $row->stop = date("Y-m-d H:i:s",strtotime($_POST['stop']));
            
            if($this->m->_db->insertObject('archive',$row)){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
    }
?>