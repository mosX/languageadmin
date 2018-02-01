<?php
    class tagsController extends Model {
        public function init(){

        }
        public function indexAction(){
            
        }
        
        public function addAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.tags');
            $tags = new Tags($this->m);
            $tag_id = $tags->addNew($_POST['name']);
            $user_id = (int)$_POST['user_id'];
            
            if($tags->linkTag($user_id,$tag_id)){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error","message":"'.$tags->error.'"}';
            }            
        }
        
        public function editAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true);
            $id = (int)$_POST['id'];
            $tag = strip_tags(trim($_POST['tag']));
            
            //проверяем или есть такой тег уже созданный 
            $this->m->_db->setQuery(
                        "SELECT `tags`.* "
                        . " FROM `tags`"
                        . " WHERE `tags`.`name` = '".$tag."'"
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($result);
            if(!$result){ //создаем новый 
                $row->name = $tag;
                $row->parent = $this->m->_user->id;
                $row->date = date("Y-m-d H:i:s");
                $this->m->_db->insertObject('tags',$row,'id');
                $tag_id = $row->id;
            }else{  //используем данный айди
                $tag_id = $result->id;
            }
            
            //обновляем айдишник
            $this->m->_db->setQuery(
                        "UPDATE `tags_linked` "
                        . " SET `tags_linked`.`tag_id` = ".$tag_id
                        . " WHERE `tags_linked`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","id":"'.$tag_id.'"}';
            }else{
                echo '{"status":"error"}';
            }            
        }
        
        public function delAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            //проверяем или такой тег есть..   
            /*$this->m->_db->setQuery(
                        "SELECT * FROM `tags_linked`"
                        . " WHERE `tags_linked`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($tag);
            if(!$tag){
                echo '{"status":"error"}';
                return false;
            }*/
            
            $this->m->_db->setQuery(
                        "UPDATE `tags_linked` SET `tags_linked`.`status` = 0"
                        . " WHERE `tags_linked`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
    }
?>