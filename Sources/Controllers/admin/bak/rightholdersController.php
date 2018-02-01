<?php
    class rightholdersController extends Model{
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $_POST = json_decode(file_get_contents('php://input'), true);            
                
                $id = (int)$_POST['id'];
                $row->name = trim($_POST['name']);
                $row->country = (int)$_POST['country'];
                
                if($id){
                    $this->m->_db->setQuery(
                                "UPDATE `cms_rightholders` SET `cms_rightholders`.`name` = '".$row->name."'"
                                . " , `cms_rightholders`.`country` = ".$row->country
                                . " WHERE `cms_rightholders`.`id` = ".$id
                                . " AND `cms_rightholders`.`status` = 1"
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    $row->date = date("Y-m-d H:i:s");
                    if($this->m->_db->insertObject('cms_rightholders',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
                
            }else{
                $this->m->_db->setQuery(
                            "SELECT `cms_rightholders`.* "
                            . " , `country`.`name_ru` as country_name"
                            . " FROM `cms_rightholders` "
                            . " LEFT JOIN `country` ON `country`.`id` = `cms_rightholders`.`country`"
                            . " WHERE `cms_rightholders`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
                
                //список стран
                $this->m->_db->setQuery(
                            "SELECT `country`.* FROM `country`"
                        );
                $this->m->country_list = $this->m->_db->loadObjectList();
            }
        }
        
        public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `cms_rightholders`.* "
                        . " FROM `cms_rightholders` WHERE `cms_rightholders`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
    }
?>