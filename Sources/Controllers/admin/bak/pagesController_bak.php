<?php
    class pagesController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
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
    }
?>