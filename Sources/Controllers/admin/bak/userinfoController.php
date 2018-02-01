<?php
    class userinfoController extends Model {
        public function init(){
            if(!$this->m->_user->id) redirect('/');
        }
        
        public function updateAction(){
            $this->disableTemplate();
            $this->disableView();
            $_POST = json_decode(file_get_contents('php://input'), true);   //для Content-Type: application/json
            
            xload('class.contacts');
            $contacts = new Contacts($this->m);            
            if($contacts->updateUserinfoData()){
                echo '{"status":"success","user":'.$contacts->getUserInfoDetails($_POST['id']).'}';
            }else{
                echo '{"status":"error"}';
            }                       
        }
        
        public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.contacts');
            $contacts = new Contacts($this->m);
            echo $contacts->getUserInfoDetails($_GET['id']);
            
            /*xload('class.contacts');
            $user = new Contacts($this->m);
            $this->m->rows = $user->userinfo();
            
            $this->m->rows->date = strtotime($this->m->rows->date)*1000;
            $this->m->rows->last_login = strtotime($this->m->rows->last_login)*1000;
            $this->m->rows->birthday = strtotime($this->m->rows->birthday)*1000;
            $this->m->rows->ip_country = $this->m->getCountryByIP($this->m->rows->last_ip);
            //$this->m->rows->upass_decrypted = RC4_decrypt($this->m->rows->upass, md5(md5($this->m->rows->email . 'SecKeyword').'-keygames'));
            
            echo json_encode($this->m->rows);*/
        }
        
        /*public function dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.users');
            $user = new Users($this->m);
            $this->m->rows = $user->userinfo();
            
            $this->m->rows->date = strtotime($this->m->rows->date)*1000;
            $this->m->rows->last_login = strtotime($this->m->rows->last_login)*1000;
            $this->m->rows->birthday = strtotime($this->m->rows->birthday)*1000;
            $this->m->rows->ip_country = $this->m->getCountryByIP($this->m->rows->last_ip);
            //$this->m->rows->upass_decrypted = RC4_decrypt($this->m->rows->upass, md5(md5($this->m->rows->email . 'SecKeyword').'-keygames'));
            
            echo json_encode($this->m->rows);
        }
        
        public function gainAction(){
            $this->disableTemplate();
            $this->disableView();
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->setGain($_GET['id'],$_POST['value'],$_POST['type']);
            }
        }
        
        public function maxbalanceAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if($_GET['id']){                
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->changeMaxBalance($_GET['id']);
                
            }
        }

        public function changeautogainAction(){
            $this->disableTemplate();
            $this->disableView();
            
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->changeAutogain($_GET['id']);
            }
        }
        
        public function changestatusAction(){
            $this->disableTemplate();
            $this->disableView();
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->changeStatus($_GET['id']);
                
            }
        }
        public function blockAction(){
            $this->disableTemplate();
            $this->disableView();
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->block($_GET['id']); 
            }
        }
        public function depositfunbalanceAction(){
            $this->disableTemplate();
            $this->disableView();
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->depositFunBalance($_GET['id']);
            }
        }
        
        public function badauthAction(){
            $this->disableTemplate();
            $this->disableView();
            if($_GET['id']){
                xload('class.admin.users');
                $users = new Users($this->m);
                $users->setBadAuth($_GET['id'],$_POST['value']);
            }
        }
        
        public function indexAction(){
            $this->disableTemplate();
            xload('class.admin.users');
            $user = new Users($this->m);
            $this->m->rows = $user->userinfo();
        }

        public function geturlcrmAction() {
            $this->disableTemplate();
            $this->disableView();

            if ($_GET['id']) {
                xload('class.apicrm.apicrm');
                $user = new Users($this->m);
                $user->getURLCRM($_GET['id']);
            }
        }*/
    }
?>