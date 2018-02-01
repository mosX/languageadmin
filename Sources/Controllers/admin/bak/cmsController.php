<?php
    class cmsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            
        }
        
        /*public function delete_genreAction(){
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
        
        public function genresAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $id = (int)$_POST['id'];
                $name = strip_tags(trim($_POST['name']));
                if($id){    //edit
                    $this->m->_db->setQuery(
                                "UPDATE `groups` SET `groups`.`name` = '".$name."'"
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
                    if($this->m->_db->insertObject('groups',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `groups`.* "
                            . " , `channels`.`group_id`"
                            . " , `channels`.`id` as channel_id"
                            . " , `groups`.`id` as group_id"
                            . " , `groups`.`name` as group_name"
                            . " , COUNT(`channels`.`id`) as cnt"
                            . " FROM `groups`"
                            . "  LEFT JOIN `channels` ON `channels`.`group_id` = `groups`.`id`"
                            . " WHERE `groups`.`status` = 1"
                            . " GROUP BY `channels`.`group_id`"
                        );
                $this->m->data = $this->m->_db->loadObjectList();            
            }
        }*/
        
        public function pagesAction(){
            $this->m->_db->setQuery(
                        "SELECT `cms_pages`.* "
                        . " FROM `cms_pages`"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $this->disableTemplate();
                $this->disableView();
                
                $row->name = $_POST['name'];
                $row->description = $_POST['description'];
                $row->date = date("Y-m-d H:i:s");
                if($this->m->_db->insertObject('cms_pages',$row)){
                    echo '{"status":"success"}';
                }else{                    
                    echo '{"status":"error"}';
                }
            }
        }
        
        public function bannersAction(){
            $this->m->_db->setQuery(
                        "SELECT `cms_banners`.* "
                        . " FROM `cms_banners` "
                        . " WHERE `cms_banners`.`status` = 1"
                        . " ORDER BY `id` DESC"
                   );
            $this->m->data = $this->m->_db->loadObjectList('id');
           
            foreach($this->m->data as $item)$ids[] = $item->id;
           
            $this->m->_db->setQuery(
                    "SELECT `cms_banner_assignment`.* "
                    . " , `cms_pages`.`name` as page_name"
                    . " FROM `cms_banner_assignment` "
                    . " LEFT JOIN `cms_pages` ON `cms_pages`.`id` = `cms_banner_assignment`.`page_id`"
                    . " WHERE `cms_banner_assignment`.`banner_id` IN (".implode(',',$ids).")"                    
                   );
            $data = $this->m->_db->loadObjectList();
           
            foreach($data as $item){
                $this->m->data[$item->banner_id]->pages[$item->page_id] = $item->page_name;
            }
        }
        
        public function banner_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `cms_banner_assignment`.* "
                        . " , `cms_banners`.`filename`"
                        . " FROM `cms_banner_assignment`"
                        . " LEFT JOIN `cms_banners` ON `cms_banners`.`id` = `cms_banner_assignment`.`banner_id`"
                        . " WHERE `cms_banner_assignment`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            $data->filepath = $this->m->config->assets_url.'/banners/'.$data->filename;
            
            echo json_encode($data);
        }
        
        
        /*public function page_bannersAction(){
            $this->m->_db->setQuery(
                        "SELECT `cms_banner_assignment`.* "
                        . " , `cms_banners`.`filename`"
                        . " FROM `cms_banner_assignment`"
                        . " LEFT JOIN `cms_banners` ON `cms_banners`.`id` = `cms_banner_assignment`.`banner_id`"
                        . " WHERE `cms_banner_assignment`.`page_id` = ".$this->m->_path[2]
                        . " AND `cms_banner_assignment`.`status` = 1"
                        . " ORDER BY `id`,`sequence` DESC"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $this->disableTemplate();
                $this->disableView();
                
                $id = (int)$_POST['id'];
                
                $row->banner_id = (int)$_POST['banner_id'];
                $row->page_id = (int)$this->m->_path[2];    
                $row->key = strip_tags(trim($_POST['key']));
                $row->sequence = (int)$_POST['sequence'];
                $row->description = $_POST['description'];
                $row->title = $_POST['title'];
                $row->date = date('Y-m-d H:i:s');
                
                if($id){
                    $row->id = $id;
                    if($this->m->_db->updateObject('cms_banner_assignment',$row,'id')){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }                
                }else{                    
                    if($this->m->_db->insertObject('cms_banner_assignment',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }                
                }
            }
        }*/
        
        public function banner_editAction(){
            $this->disableTemplate();
            $this->disableView();
            $_POST = json_decode(file_get_contents('php://input'), true);
            $id = (int)$_POST['id'];
            //$key = strip_tags(trim($_POST['key']));
            $description = strip_tags(trim($_POST['description']));
            //$title = strip_tags(trim($_POST['title']));
            p($_POST);
            if(!$id){
                echo '{"status":"error","message":"No ID!"}';
                return;
            }
            
            $this->m->_db->setQuery(
                        "UPDATE `cms_banners` SET `cms_banners`.`description` = '".$description."'"
                        . " WHERE `cms_banners`.`id` = ".$id        
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                p($this->m->_db->sql);
                echo '{"status":"error"}';
            }    
        }
        
        /*public function delete_assignmentAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "DELETE FROM `cms_banner_assignment` WHERE `cms_banner_assignment`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }*/
        
        public function delete_bannerAction(){
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
        }
        
        public function addbannerAction(){
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
        
        /*public function editbanner_assignmentAction(){
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