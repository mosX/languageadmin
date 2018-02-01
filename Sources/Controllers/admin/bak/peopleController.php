<?php
    class peopleController extends Model{
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                xload('class.images');
                $images = new Images($this->m);
                
                $_POST = json_decode(file_get_contents('php://input'), true);            
                
                $id = (int)$_POST['id'];
                $row->name = $_POST['name'];
                $row->filename = $_POST['filename'];
                $row->date = date("Y-m-d H:i:s");
                
                if($id){
                    $this->m->_db->setQuery(
                                "SELECT `cms_people`.* "
                                . " FROM `cms_people` "
                                . " WHERE `cms_people`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    $this->m->_db->loadObject($person);
                    
                    $this->m->_db->setQuery(
                                "UPDATE `cms_people` SET `cms_people`.`name` = '".$row->name."'"
                                . " , `cms_people`.`filename` = '".$row->filename."'"
                                . " WHERE `cms_people`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                    
                    if($person->filename != $row->filename){ //UNSET OLD FILE
                        
                        $images->unlinkOld($person->filename,[''],$this->m->config->assets_path.DS.'people');
                        
                        $images->move($this->m->config->assets_path.DS.'people_temp'.DS.$row->filename,$this->m->config->assets_path.DS.'people'.DS.$row->filename);
                    }
                }else{                
                    if($this->m->_db->insertObject('cms_people',$row)){
                        $images->move($this->m->config->assets_path.DS.'people_temp'.DS.$row->filename,$this->m->config->assets_path.DS.'people'.DS.$row->filename);
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `cms_people`.* "
                            . " FROM `cms_people` "
                            . " WHERE `cms_people`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id){
                echo '{"status":"error"}';
                return;
            }
            
            $this->m->_db->setQuery(
                        "SELECT `cms_people`.* "
                        . " FROM `cms_people`"
                        . " WHERE `cms_people`.`id` = ".$id
                        . " AND `cms_people`.`status` = 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function addimageAction(){
            $this->disableTemplate();
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'people_temp');
                if($images->validation == true) $images->saveThumbs(array(array(65,44,'')));
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;                    
                }                
            }
        }
        
        public function editimageAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'people_temp');
                if($images->validation == true) $images->saveThumbs(array(array(65,44,'')));
                
                if($images->validation == false){
                    $this->m->status = 'error';
                    $this->m->error = $images->error;
                }else{
                    $this->m->status = 'success';
                    $this->m->filename = $images->filename;                    
                }                
            }
            
            
        }
        
    }
?>