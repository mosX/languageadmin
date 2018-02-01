<?php
    class analiticsController extends Model {
        public function init(){
            
        }
        
        public function indexAction(){
            
        }
        
        public function viewsAction(){
            
            //получаем нужные нам просмотры
            $this->m->_db->setQuery(
                        "SELECT `views`.* "
                        . " FROM `views` "
                        . " WHERE `views`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            foreach($data as $item){
                $this->m->total ++;
                $this->m->report[$item->channel_id]->cnt += 1;
            }
            
            foreach($this->m->report as $item){
                $item->percents = round($item->cnt / ($this->m->total / 100),2);
            }
            
            $this->m->json = json_encode($this->m->report);
            //p($this->m->total);
            //p($this->m->report);
            //p($this->m->total);            
        }
    }
?>