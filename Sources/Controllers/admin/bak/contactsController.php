<?php
    class contactsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');            
            $this->m->addCSS('jquery-ui.min');
            
            $this->m->addCSS('bootstrap-datetimepicker.min');
            $this->m->addJS('moment')->addJS('bootstrap-datetimepicker.min');                    
        }
        
        public function indexAction(){
            /*$this->m->_db->setQuery(
                        "SELECT * FROM `views` WHERE `views`.`user_id` = 8"
                    );
            $views = $this->m->_db->loadObjectList();
            p($views);*/
            
            //FILTERS
            $search_filter = trim($_GET['search']);
            $tag_filter = trim($_GET['tag']);
            
            $sql = ($search_filter ? " AND `users`.`fullname` LIKE '%".$search_filter."%' OR `users`.`email` LIKE '%".$search_filter."%' OR `users`.`email` LIKE '%".$search_filter."%'":"")
                    ;
            
            $this->m->_db->setQuery(
                        "SELECT COUNT(`users`.`id`) "
                        . " FROM `users` "                                            
                        . " WHERE `users`.`status` = 1"
                        . $sql
                        . ($tag_filter ? " HAVING tags_cnt > 0" : "")
                    );
            $total = $this->m->_db->loadResult();
            
            $xNav = new xNav("/contacts/".($filter? 'index/'.$filter.'/':'' ), $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
            
            //получаем пользователей
            $this->m->_db->setQuery(
                        "SELECT `users`.* "
                        . ", `contacts`.`value`"
                        .($tag_filter ? " , COUNT(`tags_linked`.`tag_id`) as tags_cnt" : '') 
                        . " FROM `users` "
                        
                        . ($tag_filter ? " LEFT JOIN `tags` ON `tags`.`name` = '".$tag_filter."'" : "") 
                        . ($tag_filter ? " LEFT JOIN `tags_linked` ON `tags_linked`.`user_id` = `users`.`id` AND `tags_linked`.`tag_id` = `tags`.`id`" : "")
                        
                        . " LEFT JOIN `contacts` ON `contacts`.`value` LIKE '%334543%' AND `contacts`.`user_id` = `users`.`id`" 
                    
                        . " WHERE `users`.`status` = 1"
                        . $sql
                        . ($tag_filter ? " HAVING tags_cnt > 0" : "")
                        . " GROUP BY `users`.`id`"
                        . " ORDER BY `id` DESC"
                        . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                    );
            $this->m->data = $this->m->_db->loadObjectList('id');
            //p($this->m->data);
            
            foreach($this->m->data as $item)$ids[] = $item->id;
            
            //получаем каналы которые сейчас просматриваются
            $this->m->_db->setQuery(
                        "SELECT `views`.* "
                        . " , `channels`.`name`"
                        . " FROM `views` "
                        . " LEFT JOIN `channels` ON `channels`.`id` = `views`.`channel_id`"
                        . " WHERE `views`.`user_id` in (".implode(',',$ids).")"
                        . " AND `views`.`status` = 1"
                    );
            $views = $this->m->_db->loadObjectList();
            
            foreach($views as $item){
                if($this->m->data[$item->user_id]){
                    $this->m->data[$item->user_id]->views[] = $item;
                }
            }
            //p($this->m->data);
            
            //получаем блокировки которых может быть несколько
            $this->m->_db->setQuery(
                        "SELECT `blocks`.* "
                        . " FROM `blocks`"
                        . " WHERE `blocks`.`user_id` IN (".implode(",",$ids).")"
                        . " AND (`blocks`.`till_date` = '0000-00-00 00:00:00' OR `blocks`.`till_date` > '".date("Y-m-d H:i:s")."')"
                        . " AND `blocks`.`status` = 1"
                    );
            $blocks = $this->m->_db->loadObjectList();
            foreach($blocks as $item){
                $this->m->data[$item->user_id]->blocks[] = $item;
            }
            
            //получаем их контакты
            $this->m->_db->setQuery(
                        "SELECT `contacts`.* "
                        . " FROM `contacts` "
                        . " WHERE `contacts`.`user_id` IN (".implode(',',$ids).")"
                        . " AND `contacts`.`status` = 1"
                    );
            $contacts = $this->m->_db->loadObjectList();
            
            foreach($contacts as $item){
                $this->m->data[$item->user_id]->contacts[$item->group][] = $item;
            }
            
            //получаем их теги
            $this->m->_db->setQuery(
                        "SELECT `tags_linked`.* "
                        . " , `tags`.`name`"
                        . " FROM `tags_linked` "
                        . " LEFT JOIN `tags` ON `tags`.`id` = `tags_linked`.`tag_id`"
                        . " WHERE `tags_linked`.`user_id` IN (".implode(',',$ids).") "
                        . " AND `tags_linked`.`status` = 1"
                    );
            $tags = $this->m->_db->loadObjectList();
            
            foreach($tags as $item){
                $this->m->data[$item->user_id]->tags[] = $item;
            }            
        }
        
        public function getviewshistoryAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $this->m->_db->setQuery(
                        "SELECT `views`.* "
                        . " ,`channels`.`name`"
                        . " FROM `views` "
                        . " LEFT JOIN `channels` ON `channels`.`id` = `views`.`channel_id`"
                        . " WHERE `views`.`user_id` = 8"
                    );
            $views = $this->m->_db->loadObjectList();
            foreach($views as $item){
                $item->start = strtotime($item->date)*1000;
                $item->end = strtotime($item->last_check)*1000;
            }
            
            echo json_encode($views);
        }
        
        /*public function note_itemAction(){
            $this->disableTemplate();

            if($_SERVER['REQUEST_METHOD'] != 'POST'){
                $this->disableView();
            }
        }*/
        
        public function addAction(){
            $this->m->addJS('maps');
            if($_SERVER['REQUEST_METHOD'] == 'POST'){   //создаем новый контакт
                $this->disableTemplate();
                $this->disableView();
                
                xload('class.contacts');
                $contacts = new Contacts($this->m);
                $contacts->addNew();
            }
        }
        
        public function updateAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){   //создаем новый контакт
                $this->disableTemplate();
                $this->disableView();
                
                xload('class.contacts');
                $contacts = new Contacts($this->m);
                $contacts->update();
                
                echo '{"status":"success"}';
            }
        }
        
        /*public function add_noteAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.notes');
                $notes = new Notes($this->m);

                $notes->addNew();
            }
        }*/
        
        public function detailsAction(){
            $this->m->addJS('maps');
            xload('class.contacts');
            $contacts = new Contacts($this->m);
            $contacts->getDetails($this->m->_path[2]);
            //получаем пользователя и все его данные
            //$user_id = $this->m->_path[2];
        }
    }
?>