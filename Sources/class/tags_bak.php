<?php
class Tags{
    protected $_table = 'tags';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function addNew($name){
        $name = strip_tags(trim($name));
        
        if(!$name){
            echo '{"status":"error"}';
            return;
        }
            
        //проверяем или такой тег уже не добавлен
        $this->m->_db->setQuery(
                    "SELECT `tags`.`id` "
                    . " FROM `tags` "
                    . " WHERE `tags`.`name` = '".$name."'"
                    . " LIMIT 1"
                );
        $tag_id = $this->m->_db->loadResult();

        if(!$tag_id){
            $row->name = $name;
            $row->parent = $this->m->_user->id;
            $row->date = date("Y-m-d H:i:s");
            $row->status = 1;

            $this->m->_db->insertObject('tags',$row,'id');
            $tag_id = $row->id;
        }
        
        return $tag_id;
    }
    
    public function linkTag($user_id, $tag_id){
        //проверяем или пользователь уже не подвязан за єтим тегом
        $this->m->_db->setQuery(
                    "SELECT COUNT(`tags_linked`.`id`) "
                    . " FROM `tags_linked` "
                    . " WHERE `tags_linked`.`user_id` = ".$user_id
                    . " AND `tags_linked`.`tag_id` = ".$tag_id
                    . " AND `tags_linked`.`status` = 1"
                    . " LIMIT 1"
                );
        $check = $this->m->_db->loadResult();

        if(!$check && $user_id){
            //подвязываем под него пользователя
            $row->user_id = (int)$user_id;
            $row->tag_id = $tag_id;
            $row->parent = $this->m->_user->id;
            $row->status = 1;
            $row->date = date("Y-m-d H:i:s");
            if($this->m->_db->insertObject('tags_linked',$row)){
                return true;
            }else{
                return false;
            }
        }else{
            $this->error = "Такой Тег уже добавлен";
            return false;
        }
    }
}
?>