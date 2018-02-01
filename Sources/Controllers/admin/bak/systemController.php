<?php
    class systemController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            
        }
        
        public function generatePassword($password){
            //$password = 123456;
            $salt   = makePassword(16);
            $crypt  = md5(md5($password) . $salt);
            $password  = $crypt . ':' . $salt;
            
            return $password;
        }
        
        public function getdataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `supers`.* "
                        . " FROM `supers` "
                        . " WHERE `supers`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            $data->role = $data->gid;
            
            echo json_encode($data);
        }
        
        public function adminsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);            
                
                $id = (int)$_POST['id'];
                $role = (int)$_POST['role'];
                if($id){                    
                    $this->m->_db->setQuery(
                                "UPDATE `supers` SET `supers`.`gid` = ".$role
                                . " WHERE `supers`.`id` = ".$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{
                    $login = trim($_POST['login']);
                    
                    $password = $_POST['password'];
                    $conf_password = $_POST['conf_password'];

                    if(!$login){
                        echo '{"status":"error","message":"Вы должны ввести логин"}';
                        return false;
                    }

                    $this->m->_db->setQuery(
                                "SELECT `supers`.`id` "
                                . " FROM `supers` "
                                . " WHERE `supers`.`email` = '.$login.' "                             
                            );
                    $check = $this->m->_db->loadResult();
                    if($check){
                        echo '{"status":"error","message":"Такой Логин уже используется"}';
                        return false;
                    }

                    if($password != $conf_password){
                        echo '{"status":"error","message":"Такой Логин уже используется"}';
                        return false;
                    }

                    $row->email = $login;
                    $row->password = $this->generatePassword($password);
                    $row->partner = $this->m->_user->id;
                    $row->gid = $role;
                    $row->date = date("Y-m-d H:i:s");
                    if($this->m->_db->insertObject('supers',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $sub_page = strip_tags(trim($_GET['act']));
                
                $this->m->_db->setQuery(
                            "SELECT `supers`.* "
                            . " , `s`.`email` as partner_email"
                            . " FROM `supers`"
                            . " LEFT JOIN `supers` s ON `s`.`id` = `supers`.`partner`"
                            . " WHERE `supers`.`status` = 1"
                            . ($sub_page == 'admins' ? " AND `supers`.`gid` = 10" : "")
                            . ($sub_page == 'support' ? " AND `supers`.`gid` = 20" : "")
                            . ($sub_page == 'operators' ? " AND `supers`.`gid` = 30" : "")                            
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
    }
?>