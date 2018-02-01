<?php
    class collectionsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $this->disableTemplate();
                $this->disableView();
                
                $row->id = (int)$_POST['id'];
                $row->name = trim($_POST['name']);
                $row->type = (int)$_POST['type'];
                $row->description = $_POST['description'];
                $row->date = date("Y-m-d H:i:s");
                
                if($row->id){
                    $this->m->_db->setQuery(
                                "UPDATE `cms_collections` "
                                . " SET `cms_collections`.`name` = '".$row->name."'"
                                . " , `cms_collections`.`description` = '".$row->description."'"
                                . " WHERE `cms_collections`.`id` = " .$row->id 
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        //p($this->m->_db->_sql);
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    if($this->m->_db->insertObject('cms_collections',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $sub_page = $_GET['act'];
                
                $this->m->_db->setQuery(
                            "SELECT `cms_collections`.* "
                            . " FROM `cms_collections`"
                            . " WHERE `cms_collections`.`status` = 1"
                            . ($sub_page == 'published' ? " AND `cms_collections`.`published` = 1" : "")
                            . ($sub_page == 'unpublished' ? " AND `cms_collections`.`published` = 0" : "")
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function publishAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            
            $status = $status ? 1 : 0;
            
            if(!$id)return;
                        
            $this->m->_db->setQuery(
                        "UPDATE `cms_collections` SET `cms_collections`.`published` = ".$status
                        . " WHERE `cms_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","result":"'.$status.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
         public function publish_bannerAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            
            $status = $status ? 1 : 0;
            
            if(!$id)return;
                        
            $this->m->_db->setQuery(
                        "UPDATE `cms_banner_collections` SET `cms_banner_collections`.`published` = ".$status
                        . " WHERE `cms_banner_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","result":"'.$status.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
         public function publish_channelAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            
            $status = $status ? 1 : 0;
            
            if(!$id)return;
                        
            $this->m->_db->setQuery(
                        "UPDATE `cms_channel_collections` SET `cms_channel_collections`.`published` = ".$status
                        . " WHERE `cms_channel_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","result":"'.$status.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function channelsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $channel_id = (int)$_POST['channel_id'];
                $position = (int)$_POST['position'];
                $collection_id = (int)$this->m->_path[2];
                
                if($id){
                    $this->m->_db->setQuery(
                                "UPDATE `cms_channel_collections` "
                                . " SET `cms_channel_collections`.`channel_id` = ".(int)$channel_id
                                . " , `cms_channel_collections`.`position` = ".(int)$position
                                . " WHERE `cms_channel_collections`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    $row->channel_id  = $channel_id;
                    $row->collection_id  = $collection_id;
                    $row->position = $position;
                    $row->date = date('Y-m-d H:i:s');
                    if($this->m->_db->insertObject('cms_channel_collections',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(    //получаем список каналов
                            "SELECT `channels`.* "
                            . " FROM `channels`"
                            . " WHERE `channels`.`status` = 1"
                            //. " ORDER BY `position` DESC"
                        );
                $this->m->channels = $this->m->_db->loadObjectList();
                
                $sub_page = strip_tags(trim($_GET['act']));
                
                $this->m->_db->setQuery(
                            "SELECT `cms_channel_collections`.* "
                            . " , `channels`.`name`"
                            . " , `channel_logos`.`filename`"
                            . " FROM `cms_channel_collections`"
                            . " LEFT JOIN `channels` ON `cms_channel_collections`.`channel_id` = `channels`.`id`"
                            . " LEFT JOIN `channel_logos` ON `channel_logos`.`id` = `channels`.`logo_id`"
                            . " WHERE `cms_channel_collections`.`collection_id` = ".(int)$this->m->_path[2]
                            . " AND `cms_channel_collections`.`status` = 1"
                            . ($sub_page == 'published' ? " AND `cms_channel_collections`.`published` = 1" : "")
                            . ($sub_page == 'unpublished' ? " AND `cms_channel_collections`.`published` = 0" : "")
                            . " ORDER BY `position` ASC"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function delete_channelAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id){
                echo '{"status":"error"}';
                return;
            }
            
            $this->m->_db->setQuery(
                        "DELETE FROM `cms_channel_collections` WHERE `cms_channel_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function collection_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $this->m->_db->setQuery(
                        "SELECT `cms_collections`.* "
                        . " FROM `cms_collections`"
                        . " WHERE `cms_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function channel_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `cms_channel_collections`.* "
                        . " FROM `cms_channel_collections` "
                        . " WHERE `cms_channel_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function bannersAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $_POST = json_decode(file_get_contents('php://input'), true);
                $this->disableTemplate();
                $this->disableView();
                
                $id = (int)$_POST['id'];
                
                $row->banner_id = (int)$_POST['banner_id'];
                $row->collection_id = (int)$this->m->_path[2];
                $row->position = (int)$_POST['position'];
                $row->date = date('Y-m-d H:i:s');
                
                //$url = $_POST['url'];
                //$name = $_POST['name'];
                
                if($id){    //редактирование
                    $this->m->_db->setQuery(        //обновляем коллекцию
                                "UPDATE `cms_banner_collections` "
                                . " SET `cms_banner_collections`.`position` = ".(int)$row->position
                                . " , `cms_banner_collections`.`banner_id` = ".(int)$row->banner_id
                                . " WHERE `cms_banner_collections`.`id` = ".$id
                            );
                    $this->m->_db->query();
                    
                    /*$this->m->_db->setQuery(        //обновляем баннер
                                "UPDATE `cms_banners` SET `cms_banners`.`name` = '".$name."'"
                                . " , `cms_banners`.`url` = '".$url."'"
                                . " WHERE `cms_banners`.`id` = ".$row->banner_id
                                . " LIMIT 1"
                            );
                    $this->m->_db->query();*/
                    
                    echo '{"status":"success"}';
                    
                    /*if($this->m->_db->updateObject('cms_banner_collections',$row,'id')){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }                */
                }else{
                    if($this->m->_db->insertObject('cms_banner_collections',$row)){
                        /*if($_POST['type'] == 'add'){    //обновляем баннер                            
                            $this->m->_db->setQuery(
                                        "UPDATE `cms_banners` SET `cms_banners`.`name` = '$name'"
                                        . " , `cms_banners`.`url` = '".$url."'"
                                        . " WHERE `cms_banners`.`id` = ".$row->banner_id
                                    );
                            $this->m->_db->query();
                        }*/
                        
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $sub_page = strip_tags(trim($_GET['act']));
                
                $this->m->_db->setQuery(
                        "SELECT `cms_banner_collections`.* "
                        . " , `cms_banner_resizes`.`filename`"
                        . " , `cms_banners`.`name`"
                        . " , `cms_banners`.`url`"
                        //. " , `cms_banners`.`id` as banner_id"
                        . " FROM `cms_banner_collections`"
                        
                        . " LEFT JOIN `cms_banners` ON `cms_banners`.`id` = `cms_banner_collections`.`banner_id`"
                        . " LEFT JOIN `cms_banner_resizes` ON `cms_banner_resizes`.`banner_id` = `cms_banners`.`id` AND `cms_banner_resizes`.`type` = 1 "
                        . " WHERE `cms_banner_collections`.`collection_id` = ".$this->m->_path[2]
                        . " AND `cms_banner_collections`.`status` = 1"
                        . ($sub_page == 'published' ? " AND `cms_banner_collections`.`published` = 1" : "")
                        . ($sub_page == 'unpublished' ? " AND `cms_banner_collections`.`published` = 0" : "")
                        . " ORDER BY `id`,`position` DESC"
                    );
                $this->m->data = $this->m->_db->loadObjectList();
                
                //получаем список баннеров для выбора
                $this->m->_db->setQuery(
                            "SELECT `cms_banners`.* "
                            . " , `cms_banner_resizes`.`filename`"
                            . " FROM `cms_banners`"
                            . " LEFT JOIN `cms_banner_resizes` ON `cms_banner_resizes`.`banner_id` = `cms_banners`.`id` AND `cms_banner_resizes`.`type` = 1 "
                            . " WHERE `cms_banners`.`status` = 1"
                            
                            . " ORDER BY `id` DESC"
                        );
                $this->m->banners_list = $this->m->_db->loadObjectList();
            }
        }
                
        public function banner_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            
/*            $this->m->_db->setQuery(
                        "SELECT `cms_banner_collections`.* "
                        . " , `cms_banners`.`filename`"
                        . " , `cms_banners`.`name`"
                        . " , `cms_banners`.`url`"
                        . " FROM `cms_banner_collections`"                        
                        . " LEFT JOIN `cms_banners` ON `cms_banners`.`id` = `cms_banner_collections`.`banner_id`"
                        . " WHERE `cms_banner_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            $data->filepath = $this->m->config->assets_url.'/banners/'.$data->filename;*/
            
            $this->m->_db->setQuery(
                        "SELECT `cms_banner_collections`.* "
                        . " FROM `cms_banner_collections`"
                        . " WHERE `cms_banner_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
         /*public function banner_editAction(){
            $this->disableTemplate();
            $this->disableView();
            $_POST = json_decode(file_get_contents('php://input'), true);
            $id = (int)$_POST['id'];
            $key = strip_tags(trim($_POST['key']));
            $description = strip_tags(trim($_POST['description']));
            //$title = strip_tags(trim($_POST['title']));
            
            if(!$id){
                echo '{"status":"error"}';
                return;
            }
            
            $this->m->_db->setQuery(
                        "UPDATE `cms_banners` SET `cms_banners`.`key` = '".$key."'"
                        . " , `cms_banners`.`description` = '".$description."'"
                        . " WHERE `cms_banners`.`id` = ".$id        
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }    
        }*/
        
        public function delete_assignmentAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "DELETE FROM `cms_banner_collections` WHERE `cms_banner_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        /*public function delete_bannerAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            //проверяем или есть такой баннер
            $this->m->_db->setQuery(
                        "SELECT * FROM `cms_banners`"
                        . " WHERE `cms_banners`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($banner);
            if(!$banner){
                echo '{"status":"error"}';
                return false;
            }
            
            //удаляем все связи его 
            $this->m->_db->setQuery(
                        "DELETE FROM `cms_banner_assignment` WHERE `cms_banner_assignment`.`banner_id` = ".$id
                    );
            $this->m->_db->query();
            
            //стираем с базы
            $this->m->_db->setQuery(
                        "DELETE FROM `cms_banners` WHERE `cms_banners`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->query();
            
            //стираем файл
            xload('class.images');
            $images = new Images($this->m);
            $images->unlinkOld($banner->filename,[''],$this->m->config->assets_path.'/banners/');
            
            echo '{"status":"success"}';
        }*/
        
        public function editbanner_assignmentAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'banners');
                if($images->validation == true) $images->saveOriginal();
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    $row->filename = $images->filename;                    
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('cms_banners',$row,'id');
                    
                    $this->m->id = $row->id;
                }
            }
        }
       
        /*public function addbannerAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'banners');
                if($images->validation == true) $images->saveOriginal();
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    $row->filename = $images->filename;                    
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('cms_banners',$row,'id');
                    
                    $this->m->id = $row->id;
                }
            }
        }*/
        
    }
?>