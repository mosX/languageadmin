<?php
    class pagesController extends Model {
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
                        
                $id = (int)$_POST['id'];
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                $type = (int)$_POST['type'];
                
                if($id){
                    $this->m->_db->setQuery(
                                "UPDATE `cms_pages` SET `cms_pages`.`name` = '".$name."'"
                                . " , `cms_pages`.`description` = '".$description."'"
                                . " WHERE `cms_pages`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    $row->name = $name;
                    $row->description = $description;
                    $row->type = $type;

                    $row->channel_collection = (int)$_POST['channel_collection'];
                    $row->banner_collection = (int)$_POST['banner_collection'];

                    $row->date = date("Y-m-d H:i:s");

                    if($this->m->_db->insertObject('cms_pages',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $sub_page = $_GET['act'];
                $name_filter = $_GET['name'];
                    
                $this->m->_db->setQuery(
                            "SELECT `cms_pages`.* "
                            . " , `col1`.`name` as banner_name"
                            . " , `col2`.`name` as channel_name"
                            . " FROM `cms_pages` "
                            . " LEFT JOIN `cms_collections` as col1 ON `col1`.`id` = `cms_pages`.`banner_collection`"
                            . " LEFT JOIN `cms_collections` as col2 ON `col2`.`id` = `cms_pages`.`channel_collection`"
                            . " WHERE `cms_pages`.`status` = 1"
                            . ($sub_page == 'published'? " AND `cms_pages`.`published` = 1" : "")
                            . ($sub_page == 'unpublished'? " AND `cms_pages`.`published` = 0" : "")
                            . ($name_filter  ? " AND `cms_pages`.`name` = '".$name_filter."'" : '')
                            . " ORDER BY `id` DESC"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `cms_collections`.* "
                            . " FROM `cms_collections`"
                            . " WHERE `cms_collections`.`status` = 1"
                        );
                $data = $this->m->_db->loadObjectlist();
                
                foreach($data as $item){
                    $this->m->collections[$item->type][] = $item;
                }
            }
        }       
        
        public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            if(!$id)return false;
            
            $this->m->_db->setQuery(
                        "SELECT `cms_pages`.* "
                        . " FROM `cms_pages` "
                        . " WHERE `cms_pages`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function publishAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            $status = (int)$_GET['status'];
            
            $status = $status ? 1 : 0;
            
            if(!$id)return;
                        
            $this->m->_db->setQuery(
                        "UPDATE `cms_pages` SET `cms_pages`.`published` = ".$status
                        . " WHERE `cms_pages`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success","result":"'.$status.'"}';
            }else{
                p($this->m->_db->_sql);
                echo '{"status":"error"}';
            }
        }
    }
?>