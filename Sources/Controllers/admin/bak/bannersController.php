<?php
    class bannersController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
            
                $_POST = json_decode(file_get_contents('php://input'), true);
                $id = $_POST['id'];
                
                if($id){    //редактируем
                    $name = strip_tags(trim($_POST['name']));
                    $url = strip_tags(trim($_POST['url']));
                    
                    $this->m->_db->setQuery(
                                "UPDATE `cms_banners` "
                                . " SET `cms_banners`.`name` = '".$name."'"
                                . " , `cms_banners`.`url` = '".$url."'"
                                . " WHERE `cms_banners`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        //проверяем коллекции (обновляем/добавляем)
                        if($_POST['collections']){                            
                            foreach($_POST['collections'] as $item){
                                if($item['id']){  //обновляем
                                    $this->m->_db->setQuery(
                                                "UPDATE `cms_banner_collections` "
                                                . " SET `cms_banner_collections`.`position` = ".(int)$item['position']
                                                . " WHERE `cms_banner_collections`.`id` = ".$item['id']
                                                . " LIMIT 1"
                                            );
                                    $this->m->_db->query();
                                }else{  //добавляем
                                    $coll->position = (int)$item['position'];
                                    $coll->collection_id = (int)$item['collection_id'];
                                    $coll->banner_id = (int)$id;
                                    $coll->date = date("Y-m-d H:i:s");
                                    $this->m->_db->insertObject('cms_banner_collections',$coll);                                    
                                }
                            }
                        }
                        
                        //проверяем картинки (обновляем/добавляем)
                        if($_POST['resizes']){
                            xload('class.images');
                            $images = new Images($this->m);
                            
                            foreach($_POST['resizes'] as $item){
                                
                                if($item['id']){  //обновляем
                                    $this->m->_db->setQuery(
                                                "UPDATE `cms_banner_resizes` "
                                                . " SET `cms_banner_resizes`.`filename` = '".$item['filename']."'"
                                                . " WHERE `cms_banner_resizes`.`id` = ".$item['id']
                                                . " LIMIT 1"
                                            );
                                    $this->m->_db->query();
                                    
                                }else{  //добавляем
                                    if(!$item['filename']) continue;
                                    
                                    $ress->banner_id = $id;
                                    $ress->type = (int)$item['type'];
                                    $ress->filename = $item['filename'];
                                    $ress->date = date("Y-m-d H:i:s");
                                    $this->m->_db->insertObject('cms_banner_resizes',$ress);                                   
                                }
                                                                
                                $images->move($this->m->config->assets_path.DS.'banners_temp'.DS.$item['filename'], $this->m->config->assets_path.DS.'banners'.DS.$item['filename']);
                            }
                        }
                        
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `cms_banners`.* "
                            . " , `cms_banner_resizes`.`filename`"
                            . " FROM `cms_banners` "
                            . " LEFT JOIN `cms_banner_resizes` ON `cms_banner_resizes`.`banner_id` = `cms_banners`.`id` AND `cms_banner_resizes`.`type` = 1"
                            . " WHERE `cms_banners`.`status` = 1"
                            . " ORDER BY `id` DESC"
                       );
               $this->m->data = $this->m->_db->loadObjectList('id');

               foreach($this->m->data as $item)$ids[] = $item->id;
               $this->m->_db->setQuery(
                        "SELECT `cms_banner_collections`.* "
                        . " , `cms_collections`.`name` as collection_name"
                        . " , `cms_pages`.`name` as page_name"
                        . " , `cms_pages`.`name` as page_id"
                        . " FROM `cms_banner_collections` "
                        . " LEFT JOIN `cms_collections` ON `cms_collections`.`id` = `cms_banner_collections`.`collection_id`"
                        . " LEFT JOIN `cms_pages` ON `cms_pages`.`banner_collection` = `cms_collections`.`id`"
                        . " WHERE `cms_banner_collections`.`banner_id` IN (".implode(',',$ids).")"                    
                       );
               $data = $this->m->_db->loadObjectList();

                foreach($data as $item){
                    $ids[] = $item->collection_id;
                    $this->m->data[$item->banner_id]->pages[$item->page_id] = $item->page_name;
                    $this->m->data[$item->banner_id]->collections[$item->collection_id] = $item->collection_name;
                }

                //получаем страницы полученных коллекций
            }
        }
        
        public function getcollectionlistAction(){
            $this->disableTemplate();
            $this->disableView();
            $this->m->_db->setQuery(
                        "SELECT `cms_collections`.`name` "
                        . " , `cms_collections`.`id`"
                        . " FROM `cms_collections` "
                        . " WHERE `cms_collections`.`type` = 1"
                        . " AND `cms_collections`.`status` = 1"
                    );
            $data = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
        
        public function banner_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            $id = (int)$_GET['id'];
            
            /*$this->m->_db->setQuery(
                        "SELECT `cms_banner_assignment`.* "
                        . " , `cms_banners`.`filename`"
                        . " FROM `cms_banner_assignment`"
                        . " LEFT JOIN `cms_banners` ON `cms_banners`.`id` = `cms_banner_assignment`.`banner_id`"
                        . " WHERE `cms_banner_assignment`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);*/
            
            $this->m->_db->setQuery(
                        "SELECT `cms_banners`.* "
                        . " FROM `cms_banners`"
                        . " WHERE `cms_banners`.`id` = ".$id
                        . " AND `cms_banners`.`status` = 1"
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            $data->filepath = $this->m->config->assets_url.'/banners/'.$data->filename;
            
            //получаем коллекции
            $this->m->_db->setQuery(
                        "SELECT `cms_banner_collections`.`id`"
                        . " , `cms_banner_collections`.`position`"
                        . " , `cms_banner_collections`.`collection_id`"
                        . " FROM `cms_banner_collections`"
                        . " WHERE `cms_banner_collections`.`banner_id` = ".$id
                        . " AND `cms_banner_collections`.`status` = 1"
                    );
            $data->collections = $this->m->_db->loadObjectList();
            
            //получаем наши картинки
            $this->m->_db->setQuery(
                        "SELECT `cms_banner_resizes`.* "
                        . " FROM `cms_banner_resizes`"
                        . " WHERE `cms_banner_resizes`.`banner_id` = ".$id
                        . " AND `cms_banner_resizes`.`status` = 1"
                    );
            $data->resizes = $this->m->_db->loadObjectList('type');
            
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
            
            //$id = (int)$_POST['id'];
            
            $name = strip_tags(trim($_POST['name']));
            $url = strip_tags(trim($_POST['url']));
            
            $filename = trim($_POST['filename']);
            $collections = $_POST['collections'];
            
            /*if(!$id){
                echo '{"status":"error"}';
                return;
            }*/
            
            $row->name = $name;
            $row->url = $url;
            $row->date = date("Y-m-d H:i:s");
            $resizes = $_POST['resizes'];
            
            //переносим файл из временной папки
            
            if($this->m->_db->insertObject('cms_banners',$row,'id')){
                xload('class.images');
                $images = new Images($this->m);
                foreach($resizes as $type => $item){
                    if(!$item)continue;
                    $images->move($this->m->config->assets_path.DS.'banners_temp'.DS.$item, $this->m->config->assets_path.DS.'banners'.DS.$item);
                    //и добавляем в базу
                    $resize->banner_id = $row->id;
                    $resize->filename = $item;
                    $resize->type = $type;
                    $this->m->_db->insertObject('cms_banner_resizes',$resize);
                }
            
                //добавляем єтот баннер в нужные коллекции
                foreach($collections as $item){
                    if(!$item) continue;
                    $coll->banner_id  = $row->id;
                    $coll->collection_id  = $item;
                    $coll->published = 0;
                    $coll->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('cms_banner_collections',$coll);
                }
                
                echo '{"status":"success","id":"'.$row->id.'","filename":"'.$resizes[1].'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function delete_assignmentAction(){
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
        }
        
        public function delete_bannerAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            //проверяем или есть такой баннер
            $this->m->_db->setQuery(
                        "SELECT `cms_banners`.* "
                        . " FROM `cms_banners`"
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
                        "DELETE FROM `cms_banner_assignment` "
                        . " WHERE `cms_banner_assignment`.`banner_id` = ".$id
                    );
            $this->m->_db->query();
            
            //стираем с базы
            $this->m->_db->setQuery(
                        "UPDATE `cms_banners` SET `cms_banners`.`status` = 1 "
                        . " WHERE `cms_banners`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->query();
            
            //стираем файл
            xload('class.images');
            $images = new Images($this->m);
            $images->unlinkOld($banner->filename,[''],$this->m->config->assets_path.'/banners/');
            
            echo '{"status":"success"}';
        }
        
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
       
        public function editbannerAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                //$images->initImage($_FILES, $this->m->config->assets_path.DS.'banners');
                //if($images->validation == true) $images->saveOriginal();
                
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'banners_temp');
                if($images->validation == true){
                    switch((int)$_GET['type']){
                        case 1 :$images->saveOriginal(); break;
                        default:$images->saveOriginal();
                    }
                }
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    /*if($this->m->_path[2]){ //апдейтим текущий баннер
                        $this->m->_db->setQuery(
                                    "UPDATE `cms_banners` SET `cms_banners`.`filename` = '".$images->filename."'"
                                    . " WHERE `cms_banners`.`id` = ".(int)$this->m->_path[2]
                                    . " LIMIT 1"
                                );
                        $this->m->_db->query();
                    }else{  //добавляем новый банер
                        $row->filename = $images->filename;                    
                        $row->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('cms_banners',$row,'id');
                    }*/
                    
                    $this->m->id = $row->id;
                }
            }
        }
        
        public function addbannerAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                //$images->initImage($_FILES, $this->m->config->assets_path.DS.'banners');
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'banners_temp');
                
                if($images->validation == true){
                    switch((int)$_GET['type']){
                        case 1 :$images->saveOriginal(); break;
                        default:$images->saveOriginal();
                    }
                }
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    /*$row->filename = $images->filename;                    
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('cms_banners',$row,'id');*/
                    
                    //$this->m->id = $row->id;
                }
            }
        }
    }
?>