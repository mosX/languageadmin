<?php
    class vodController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');            
            $this->m->addCSS('jquery-ui.min');
            
            $this->m->addCSS('bootstrap-datetimepicker.min');
            $this->m->addJS('moment')->addJS('bootstrap-datetimepicker.min');                    
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
            
                $_POST = json_decode(file_get_contents('php://input'), true);            
                $row->poster = strip_tags(trim($_POST['filename']));
                
                $row->name = trim($_POST['name']);
                $row->original_name = trim($_POST['original_name']);
                $row->description = trim($_POST['description']);
                
                //$row->type = $_POST['type'];
                $row->start_year = (int)$_POST['start_year'];
                $row->type = 1;    //1- movies , 2 - cartoons ...
                $row->date = date("Y-m-d H:i:s");
                
                if($this->m->_db->insertObject('cms_vod',$row,'id')){
                    xload('class.images');
                    $images = new Images($this->m);
                    $images->move($this->m->config->assets_path.DS.'posters_temp'.DS.$row->poster, $this->m->config->assets_path.DS.'posters'.DS.$row->poster);
                    
                    foreach($_POST['actors'] as $item){
                        if(!$item)continue;
                        
                        $actor->vod_id = (int)$row->id;
                        $actor->people_id = (int)$item;
                        $actor->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('cms_vod_people',$actor);
                    }
                    
                    foreach($_POST['countries'] as $item){
                        if(!$item)continue;
                        
                        $country->vod_id = (int)$row->id;
                        $country->country_id = (int)$item;
                        $country->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('cms_vod_countries',$country);
                    }
                    
                    //добавляем жанры
                    foreach($_POST['genres'] as $item){
                        if(!$item) continue;
                        
                        $genre->vod_id = (int)$row->id;
                        $genre->genre_id = (int)$item;
                        $genre->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('cms_vod_genres',$genre);
                    }
                    
                    echo '{"status":"success"}';
                }else{
                    //p($this->m->_db->_sql);
                    echo '{"status":"error"}';
                }
            }else{
                //Список стран 
                $this->m->_db->setQuery(
                            "SELECT `country`.* FROM `country`"
                        );
                $this->m->country_list = $this->m->_db->loadObjectList();
                
                //получить список актеров
                $this->m->_db->setQuery(
                            "SELECT `cms_people`.* "
                            . " FROM `cms_people`"
                            . " WHERE `cms_people`.`status` = 1 "
                        );
                $this->m->people_list = $this->m->_db->loadObjectList();
                
                //получаем список жанров
                $this->m->_db->setQuery(
                            "SELECT `groups`.* "
                            . " FROM `groups` "
                            . " WHERE `groups`.`status` = 1"
                        );
                $this->m->genres_list = $this->m->_db->loadObjectList('id');
                
                $this->m->_db->setQuery(
                            "SELECT `cms_vod`.* "
                            . " FROM `cms_vod` "
                            . " WHERE `cms_vod`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList('id');
                foreach($this->m->data as $item)$ids[] = $item->id; 
                
                $this->m->_db->setQuery(
                            "SELECT `cms_vod_genres`.* "
                            . " FROM `cms_vod_genres` "
                            . " WHERE `cms_vod_genres`.`vod_id` IN (".implode(',',$ids).")"
                            . " AND `cms_vod_genres`.`status` = 1"
                        );
                $genres = $this->m->_db->loadObjectList();
                
                foreach($genres as $item){
                    $this->m->data[$item->vod_id]->genres[] = $item->genre_id;
                }
            }
        }
        
        public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `cms_vod`.* "
                        . " FROM `cms_vod`"
                        . " WHERE `cms_vod`.`id` = ".$id    
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            $this->m->_db->setQuery(
                        "SELECT `cms_vod_genres`.* "
                        . " FROM `cms_vod_genres`"
                        . " WHERE `cms_vod_genres`.`vod_id` = ".$id
                    );
            $data->genres = $this->m->_db->loadObjectList();
            
            $this->m->_db->setQuery(
                        "SELECT `cms_vod_people`.* "
                        . " FROM `cms_vod_people`"
                        . " WHERE `cms_vod_people`.`vod_id` = ".$id
                    );
            $data->actors = $this->m->_db->loadObjectList();
            
            $this->m->_db->setQuery(
                        "SELECT `cms_vod_countries`.* "
                        . " FROM `cms_vod_countries`"
                        . " WHERE `cms_vod_countries`.`vod_id` = ".$id
                    );
            $data->countries = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
                
        public function addposterAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'posters_temp');
                if($images->validation == true) $images->saveOriginal();
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;
                    
                    /*$row->filename = $images->filename;                    
                    $row->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('cms_banners',$row,'id');
                    
                    $this->m->id = $row->id;*/
                }
            }
        }
    }
?>