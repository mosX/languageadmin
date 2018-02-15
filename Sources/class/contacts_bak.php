<?php
class Contacts{
    protected $_table = 'users';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function getUserInfoDetails($user_id){
        
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` "
                    . " WHERE `users`.`id` = ".$user_id
                    . " AND `users`.`status` = 1"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);
        
        $this->m->_db->setQuery(
                    "SELECT `tags_linked`.* "
                    . " ,`tags`.`name`"
                    . " FROM `tags_linked` "
                    . " LEFT JOIN `tags` ON `tags`.`id` = `tags_linked`.`tag_id`"
                    //. " WHERE `tags_linked`.`user_id` = ".$user->id
                    //. " AND `tags_linked`.`status` = 1"
                );
        $this->m->tags = $this->m->_db->loadObjectList();
        
        //получаем контакты
        $this->m->_db->setQuery(
                    "SELECT `contacts`.`type`,`contacts`.`id`,`contacts`.`value`,`contacts`.`group` "
                    . " FROM `contacts` "
                    . " WHERE `contacts`.`user_id` = ".$user_id  
                    . " AND `contacts`.`status` = 1"                        
                );
        $contacts = $this->m->_db->loadObjectList();
        

        foreach($contacts as $item){
            $user->contacts[$item->group][] = $item;
        }
        
        $user->phoneTypes = $this->m->config->phoneTypes;
        $user->emailTypes = $this->m->config->emailTypes;
        $user->messangerTypes = $this->m->config->messangerTypes;
                
        $this->m->data = $user;
        //p($this->m->data);
        
        return json_encode($this->m->data);

        //получаем Заметки
        /*$this->m->_db->setQuery(
                    "SELECT `notes`.* "
                    . " , `supers`.`email` as 'parent_name'"
                    . " FROM `notes` "
                    . " LEFT JOIN `supers` ON `supers`.`id` = `notes`.`parent_id`"
                    . " WHERE `notes`.`user_id` = ".$user_id
                    . " AND `notes`.`status` = 1"
                    . " ORDER BY `date` ASC"
                );
        $user->notes = $this->m->_db->loadObjectList();*/                
    }
    
    public function getDetails($user_id){
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` "
                    . " WHERE `users`.`id` = ".$user_id
                    . " AND `users`.`status` = 1"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);

        $this->m->_db->setQuery(
                    "SELECT `tags_linked`.* "
                    . " ,`tags`.`name`"
                    . " FROM `tags_linked` "
                    . " LEFT JOIN `tags` ON `tags`.`id` = `tags_linked`.`tag_id`"
                    . " WHERE `tags_linked`.`user_id` = ".$user_id
                    . " AND `tags_linked`.`status` = 1"
                );
        $this->m->tags = $this->m->_db->loadObjectList();

        //получаем контакты
        $this->m->_db->setQuery(
                    "SELECT `contacts`.* "
                    . " FROM `contacts` "
                    . " WHERE `contacts`.`user_id` = ".$user_id  
                    . " AND `contacts`.`status` = 1"                        
                );
        $contacts = $this->m->_db->loadObjectList();

        foreach($contacts as $item){
            $user->contacts[$item->group][] = $item;
        }

        $this->m->data = $user;

        $this->m->_db->setQuery(
                "(SELECT `notes`.`message`"
                    . " ,`notes`.`id`"
                    . ", 'note' as type"
                    . " , NULL  as task_result"
                    . " ,`notes`.`date`"
                    . " ,`supers`.`email` as parent_name "
                    . " , NULL as 'channel_name'"
                    . " , NULL as 'channel_logo'"
                    . " FROM `notes`"
                    ." LEFT JOIN `supers` ON `supers`.`id` = `notes`.`parent_id`"
                    . " WHERE `notes`.`user_id` = ".$user_id
                    . " AND `notes`.`status` = 1"
                . " )UNION("                                                //HISTORY    
                    ."SELECT `history`.`type` as message "
                    . " ,`history`.`id`"
                    . " , 'history' as type"
                    . " , NULL "
                    . " ,`history`.`date`"
                    . " ,`supers`.`email` as parent_name "
                    . " , NULL "
                    . " , NULL "
                    . " FROM `history`"
                    . " LEFT JOIN `supers` ON `supers`.`id` = `history`.`user_id`"
                    . " WHERE `history`.`user_id` = ".$user_id                    
                . " )UNION("                                                //TASKS
                    . " SELECT `tasks`.`comment` as message"
                    . " ,`tasks`.`id`"
                    . " , 'tasks' as type"
                    . " , `tasks`.`result` "
                    . " , `tasks`.`date`"
                    . " ,`supers`.`email` as parent_name "
                    . " , NULL "
                    . " , NULL "
                    . " FROM `tasks`"
                    . " LEFT JOIN `supers` ON `supers`.`id` = `tasks`.`user_id`"
                    . " WHERE `tasks`.`user_id` = ".$user_id
                . ")UNION("                                                 //VIEWS
                    . " SELECT `views`.`channel_id` as message"
                    . " ,`views`.`id`"
                    . " , 'views' as type"
                    . " , NULL "
                    . " , `views`.`date`"
                    . " , NULL "
                    . " , `channels`.`name` "
                    . " , `channel_logos`.`filename` "
                    . " FROM `views`"
                    . " LEFT JOIN `channels` ON `channels`.`id` = `views`.`channel_id`"
                    . " LEFT JOIN `channel_logos` ON `channel_logos`.`id` = `channels`.`logo_id`"
                    . " WHERE `views`.`user_id` = ".$user_id
                . " ) "
                    . " ORDER BY `date` DESC"
                );        
        $this->m->notes = $this->m->_db->loadObjectList();
        
        
        foreach($this->m->notes as $item){
            $item->date = strtotime($item->date);            
        }        
    }
    
    public function userinfo($id){
        $this->m->_db->setQuery(
                    "SELECT `contacts`.* "
                    . " FROM `contacts`"
                    . " WHERE `contacts`.`id` = ".(int)$_GET['id']
                    . " AND `contacts`.`status` = 1"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);
        
        return $user;
    }
    
    public function editUser(){
        $this->m->_db->setQuery(
                    "UPDATE `users` SET ``"
                );
    }
    
    public function updateUserinfoData(){
        //получаем пользователя и проверяем или он такой есть
        $user_id = (int)$_POST['id'];
        
        $fullname = strip_tags(trim($_POST['fullname']));
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` WHERE `users`.`id` = ".$user_id 
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);
        
        if(!$user){
            echo '{"status":"error"}';
            return false;
        }
        //3461 NW 176th St
        $this->m->_db->setQuery(
                    "UPDATE `users` "
                    . " SET `users`.`fullname` = '".$fullname."'"
                    . " , `users`.`address` = '".$_POST['address']."'"
                    . " , `users`.`lat` = '".$_POST['lat']."'"
                    . " , `users`.`lng` = '".$_POST['lng']."'"
                    . " , `users`.`postal_code` = '".$_POST['postal_code']."'"
                    . " , `users`.`apartment` = '".$_POST['apartment']."'"
                    . " , `users`.`street` = '".$_POST['street']."'"
                    . " , `users`.`city` = '".$_POST['city']."'"
                    . " , `users`.`country_name` = '".$_POST['country']."'"
                    . " , `users`.`place_id` = '".$_POST['place_id']."'"
                    . " WHERE `users`.`id` = ".$user_id 
                    . " LIMIT 1"
                );
        if(!$this->m->_db->query()){
            p($this->m->_db->_sql);
            return false;
        }
        
        foreach($_POST['contacts'] as $key=>$group){
            foreach($group as $item){
                if($item['id']){  //update                
                    $this->updateContact($item['id'],$item['type'],$item['value'],$user_id);
                }else{  //add                    
                    $this->addContact(1,$item['type'],$item['value'],$user_id);
                }
            }
        }
        
        return true;
        
        //p($_POST['contacts']);
        //проверяем и обновляем контакты... или добавляем
        /*foreach($_POST['contacts'] as $key=>$item){
            if($_POST['phone_ids'][$key]){  //update
                $this->updateContact($_POST['phone_ids'][$key],$_POST['phone_types'][$key],$item,$user_id);     
            }else{  //add
                $this->addContact(1,$_POST['phone_types'][$key],$item,$user_id);     
            }
        }
        //3461 NW 176th St
        foreach($_POST['emails'] as $key=>$item){
            
            if($_POST['email_ids'][$key]){  //update
            
                $this->updateContact($_POST['email_ids'][$key],$_POST['email_types'][$key],$item,$user_id);     
            }else{  //add
                $this->addContact(2,$_POST['email_types'][$key],$item,$user_id);     
            }
        }
        
        foreach($_POST['messangers'] as $key=>$item){
            if($_POST['messanger_ids'][$key]){  //update
                $this->updateContact($_POST['messanger_ids'][$key],$_POST['messanger_types'][$key],$item,$user_id);     
            }else{  //add
                
                $this->addContact(3,$_POST['messanger_types'][$key],$item,$user_id);
            }
        }*/
    }
    
    public function update(){
        //получаем пользователя и проверяем или он такой есть
        $user_id = (int)$_POST['user_id'];
        $fullname = strip_tags(trim($_POST['fullname']));
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` WHERE `users`.`id` = ".$user_id 
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);
        if(!$user){
            echo '{"status":"error"}';
            return false;
        }
        //3461 NW 176th St
        $this->m->_db->setQuery(
                    "UPDATE `users` "
                    . " SET `users`.`fullname` = '".$fullname."'"
                    . " , `users`.`address` = '".$_POST['address']."'"
                    . " , `users`.`lat` = '".$_POST['lat']."'"
                    . " , `users`.`lng` = '".$_POST['lng']."'"
                    . " , `users`.`postal_code` = '".$_POST['postal_code']."'"
                    . " , `users`.`apartment` = '".$_POST['apartment']."'"
                    . " , `users`.`street` = '".$_POST['street']."'"
                    . " , `users`.`city` = '".$_POST['city']."'"
                    . " , `users`.`country_name` = '".$_POST['country']."'"
                    . " , `users`.`place_id` = '".$_POST['place_id']."'"
                    . " WHERE `users`.`id` = ".$user_id 
                    . " LIMIT 1"
                );
        $this->m->_db->query();
        
        /*if($user->fullname != $fullname ){  //обновляем
            $this->m->_db->setQuery(
                        "UPDATE `users` SET `users`.`fullname` = '".$fullname."'"
                        . " WHERE `users`.`id` = ".$user_id 
                        . " LIMIT 1"
                    );
            $this->m->_db->query();
        }*/
        
        //проверяем и обновляем контакты... или добавляем
        foreach($_POST['phones'] as $key=>$item){            
            if($_POST['phone_ids'][$key]){  //update
                $this->updateContact($_POST['phone_ids'][$key],$_POST['phone_types'][$key],$item,$user_id);     
            }else{  //add
                $this->addContact(1,$_POST['phone_types'][$key],$item,$user_id);     
            }
        }
        //3461 NW 176th St
        foreach($_POST['emails'] as $key=>$item){
            
            if($_POST['email_ids'][$key]){  //update
            
                $this->updateContact($_POST['email_ids'][$key],$_POST['email_types'][$key],$item,$user_id);     
            }else{  //add
                $this->addContact(2,$_POST['email_types'][$key],$item,$user_id);     
            }
        }
        
        foreach($_POST['messangers'] as $key=>$item){
            if($_POST['messanger_ids'][$key]){  //update
                $this->updateContact($_POST['messanger_ids'][$key],$_POST['messanger_types'][$key],$item,$user_id);     
            }else{  //add
                
                $this->addContact(3,$_POST['messanger_types'][$key],$item,$user_id);
            }
        }
    }
    
    public function updateContact($contact_id,$type,$item,$user_id){
        if(!$item){ //если значение пустое или было стерто то убираем этот контакт
            $this->m->_db->setQuery(
                        "UPDATE `contacts` SET `contacts`.`status` = 0"
                        . " WHERE `contacts`.`id` = ".$contact_id
                        . " AND `contacts`.`user_id` = ".$user_id
                        . " LIMIT 1"
                    );
        }else{
            $this->m->_db->setQuery(
                        "UPDATE `contacts` SET `contacts`.`type` = ".$type  
                        . " , `contacts`.`value` = '".$item."'"
                        . " WHERE `contacts`.`id` = ".$contact_id
                        . " AND `contacts`.`user_id` = ".$user_id
                        . " LIMIT 1"
                    );                    
        }
        $this->m->_db->query();
    }
    
    public function addNew(){
        xload('class.tags');
        $tags = new Tags($this->m);       
        
        $row->fullname = $_POST['fullname'];
        $row->login = $this->generateLogin();
        $row->password = $this->generatePassword();
        
        $row->email = '';
        $row->status = 1;
        $row->parent = $this->m->_user->id;
        
        $row->address = $_POST['address'];
        $row->lat = $_POST['lat'];
        $row->lng = $_POST['lng'];
        $row->postal_code = $_POST['postal_code'];
        $row->apartment = $_POST['apartment'];
        $row->street = $_POST['street'];
        $row->city = $_POST['city'];
        $row->country_name = $_POST['country'];
        $row->place_id = $_POST['place_id'];
        
        $row->date = date("Y-m-d H:i:s");
        
        $this->m->_db->insertObject('users',$row,'id');
        $user_id = $row->id;
        if($user_id){
            //добавляем контакты после добавления пользователя
            foreach($_POST['phones'] as $key=>$item){           
               $this->addContact(1,$_POST['phone_types'][$key],$item,$user_id);
            }

            foreach($_POST['emails'] as $key=>$item){
               $this->addContact(2,$_POST['email_types'][$key],$item,$user_id);
            }

            foreach($_POST['messangers'] as $key=>$item){
                $this->addContact(3,$_POST['messanger_types'][$key],$item,$user_id);
            }

            foreach($_POST['messages'] as $key=>$item){
                $this->addNote($_POST['messages'][$key],$_POST['messages_date'][$key],$user_id);
            }
            
            foreach($_POST['tags'] as $item){   //TODO CHECK
                $tag_id = $tags->addNew($item);
                $tags->linkTag($user_id,$tag_id);
            }

            echo '{"status":"success"}';
        }else{
            echo '{"status":"error"}';
        }
    }
    
    public function addNote($message,$date,$user_id){
        $row->user_id = $user_id;
        $row->parent_id = $this->m->_user->id;
        $row->message = strip_tags(trim($message));
        $row->date = date("Y-m-d H:i:s",strtotime($date));
        $row->status = 1;
        $this->m->_db->insertObject('notes',$row);
    }
    
    public function addContact($group,$type,$value,$user_id){
        $value = trim($value);
        if(!$value) return; //если ничего не введено то пропускаем
        
        $row->user_id = (int)$user_id;
        $row->value = $value;
        $row->type = $type;
        $row->group = $group;
        $row->status = 1;
        $row->date = date("Y-m-d H:i:s");
        p($row);
        
        $this->m->_db->insertObject('contacts',$row);
    }
    
    public function generateLogin(){
        $login = makeDigitPassword(8);
        //проверяем или он не занят
        $this->m->_db->setQuery(
                    "SELECT COUNT(`users`.*) "
                    . " FROM `users` WHERE `users`.`login` = '".$login."'"
                    . " LIMIT 1"
                );
        $cnt = $this->m->_db->loadResult();
        if($cnt){
            $login = generateLogin();
        }
        return $login;
    }
    
    public function generatePassword(){
        $password = makePassword(8);

        $salt = makePassword(16);
        $crypt = md5(md5($password).$salt);

        $password = $crypt . ':' . $salt;        
        return $password;
    }
}
?>