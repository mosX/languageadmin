<?php
    class indexController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui-1.9.2.custom.min');
            $this->m->addCSS('ui-lightness/jquery-ui-1.9.2.custom.min');
            redirect('/contacts/');
        }
        public function indexAction(){
            //получаем пользователей
            $this->m->_db->setQuery(
                        "SELECT `users`.* "
                        . " FROM `users` "
                        . " WHERE `users`.`status` = 1"
                        
                    );
            $this->m->data = $this->m->_db->loadObjectList('id');
            
            foreach($this->m->data as $item)$ids[] = $item->id;
            
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
    }
?>