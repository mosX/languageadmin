<?php
    class genresController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
  
        public function delete_genreAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = $_GET['id'];
            
            $this->m->_db->setQuery(
                        "UPDATE `groups` SET `groups`.`status` = 0"
                        . " WHERE `groups`.`id` = ".$id 
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function loadeditlogoAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'genres_temp');
                
                if($images->validation == true){
                    $images->saveThumbs(array(array(200,200,''),array(70,70,'thumb_'),array(30,30,'small_')));
                }
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    /*xload('class.admin.channels');
                    $channels = new Channels($this->m);

                    $this->m->filename = $images->filename;
                    

                    //$photos->unlinkOld(Auth::user()->ava,['thumb','small','']); //удалить старые файлы
                    $this->m->logo_id = $channels->addLogo($this->m->filename);
                    */
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                }
            }
        }
        
        public function loadaddlogoAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'genres_temp');
                
                if($images->validation == true){
                    $images->saveThumbs(array(array(200,200,''),array(70,70,'thumb_'),array(30,30,'small_')));
                }
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{                    
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                }
            }
        }
        
        public function genre_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = $_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `groups`.* "
                        . " FROM `groups` "
                        . " WHERE `groups`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);            
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $id = (int)$_POST['id'];
                
                $sequence = $_POST['sequence'];
                $filename = trim($_POST['filename']);
                if($filename){
                    xload('class.images');
                    $images = new Images($this->m);                
                }
                
                $name = strip_tags(trim($_POST['name']));
                if($id){    //edit
                    //проверяем или есть такой елемент
                    $this->m->_db->setQuery(
                                "SELECT `groups`.`id` "
                                . " , `groups`.`filename`"
                                . " FROM `groups` "
                                . " WHERE `groups`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    $this->m->_db->loadObject($group);
                    if(!$group) return;
                    
                    if($filename){
                        $images->unlinkOld($group->filename,['thumb_','small_',''],$this->m->config->assets_path.DS.'genres'); //удалить старые файлы
                        
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.$filename, $this->m->config->assets_path.DS.'genres'.DS.$filename);
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.'thumb_'.$filename, $this->m->config->assets_path.DS.'genres'.DS.'thumb_'.$filename);
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.'small_'.$filename, $this->m->config->assets_path.DS.'genres'.DS.'small_'.$filename);
                    }
                    
                    $this->m->_db->setQuery(
                                "UPDATE `groups` SET `groups`.`name` = '".$name."'"
                                . " , `groups`.`sequence` = ".(int)$sequence
                                . ($filename ? " , `groups`.`filename` = '".$filename."'" : '')
                                . " WHERE `groups`.`id` = ".$id
                                
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{      //add
                    $row->name = strip_tags(trim($_POST['name']));
                    $row->date = date("Y-m-d H:i:s");
                    $row->sequence = $sequence;
                    $row->filename = $filename;
                    if($filename){
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.$filename, $this->m->config->assets_path.DS.'genres'.DS.$filename);
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.'thumb_'.$filename, $this->m->config->assets_path.DS.'genres'.DS.'thumb_'.$filename);
                        $images->move($this->m->config->assets_path.DS.'genres_temp'.DS.'small_'.$filename, $this->m->config->assets_path.DS.'genres'.DS.'small_'.$filename);
                    }
                    
                    if($this->m->_db->insertObject('groups',$row)){
                        echo '{"status":"success"}';
                    }else{                        
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `groups`.* "
                            //. " , `channels`.`group_id`"
                            //. " , `channels`.`id` as channel_id"
                            . " , `groups`.`id` as group_id"
                            . " , `groups`.`name` as group_name"
                            //. " , COUNT(`channels`.`id`) as cnt"
                            . " , (SELECT COUNT(`channels`.`id`) FROM `channels` WHERE `channels`.`group_id` = `groups`.`id`) as cnt"
                            . " FROM `groups`"
                            //. "  LEFT JOIN `channels` ON `channels`.`group_id` = `groups`.`id`"
                            . " WHERE `groups`.`status` = 1"
                            //. " GROUP BY `channels`.`group_id`"
                        );
                $this->m->data = $this->m->_db->loadObjectList();            
                
            }
        }
    }
?>