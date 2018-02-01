<?php
xload("class.lib.dbtable");
xload("class.lib.session");
xload("class.lib.users");

class xAuth {
    public function __construct(mainframe & $mainframe){
        $this->m = $mainframe;
        $this->_session = new xSession($this->m);
    }

    public function generateSession(){
        $remote_addr    = explode('.',$_SERVER['REMOTE_ADDR']);
        $ip             = $remote_addr[0] .'.'. $remote_addr[1] .'.'. $remote_addr[2];
        $browser = @$_SERVER['HTTP_USER_AGENT'];

        $value = md5(session_id().$ip . $browser );
        return $value;
    }
    
    public function addSession($user){
        $sessionCookieName  = md5('cookiename');
        
        $this->_session->session_id  = $this->generateSession();
        
        $row->session_id  = session_id();
        $row->time       = time();
        $row->guest       = '0';
        $row->username    = $user->email;
        $row->userid      = (int)$user->id;
        $row->usertype    = "admin";
        $row->gid         = (int)$user->gid;
        $row->ip          = $_SERVER["REMOTE_ADDR"];
        $row->user_agent  = $_SERVER['HTTP_USER_AGENT'];
        $row->cookie      = $_COOKIE[$refcookiename];
        
        if($this->m->_db->insertObject('x_session',$row)){
            setcookie($sessionCookieName, $row->session_id, false, '/');
        }
    }
    
    public function initSession(){
        $sessionCookieName  = md5('cookiename');
        $sessioncookie      = strval( getParam( $_COOKIE, $sessionCookieName, null ) );
        
        if(!$sessioncookie || $sessioncookie == '-'){   //���� ���� �� ������ �������� ������
            return false;
            session_start();
        }
        
        //$sessionValueCheck  = self::sessionCookieValue( $sessioncookie );
                        
        session_start();
        
        $this->_session->session_id = session_id();
        $this->m->_db->setQuery(
                    "SELECT `x_session`.* "
                    . " FROM `x_session`"
                    . " WHERE `x_session`.`session_id` = '".$this->_session->session_id."'"
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($session);
        
        $this->_session->userid = $session->userid;
        $this->_session->time = $session->time;
        $this->_session->username = $session->username;
        $this->_session->usertype = $session->usertype;
        $this->_session->ip = $session->ip;
        $this->_session->gid = $session->gid;
        $this->_session->user_agent = $session->user_agent;
        
        return session_id();
    }
    
   /* public function initSession(){
        //$this->_session->purge('core');
        
        $sessionCookieName  = md5('cookiename');
        $sessioncookie      = strval( getParam( $_COOKIE, $sessionCookieName, null ) );

        $sessionValueCheck  = self::sessionCookieValue( $sessioncookie );
        
        if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $this->_session->load($sessionValueCheck) ) {
            //die('session');    
            $this->_session->time = time();
            $this->_session->update();
        } else {
            $remCookieName = self::remCookieName_User();

            $cookie_found = false;
            if ( isset($_COOKIE[$sessionCookieName]) || isset($_COOKIE[$remCookieName]) ) {
                $cookie_found = true;
            }
            
            if (!$cookie_found) {
                setcookie($sessionCookieName, '-', false, '/');
            } else {
                $url = strval( getParam( $_SERVER, 'REQUEST_URI', null ) );

                $this->_session->guest       = 1;
                $this->_session->username    = '';
                $this->_session->time        = time();
                $this->_session->gid         = 0;

                $this->_session->generateId();

                if (!$this->_session->insert()) {
                    die( $this->_session->getError() );
                }

                setcookie($sessionCookieName, $this->_session->getCookie(), false, '/');
            }

            if (getParam($_GET,'lang')) {
                setcookie('lang', getParam($_GET, 'lang', ''), false, '/');
            }

            $remCookieValue = strval( getParam( $_COOKIE, $remCookieName, null ) );

            if ( strlen($remCookieValue) > 64 ) {
                $remUser    = substr( $remCookieValue, 0, 32 );
                $remPass    = substr( $remCookieValue, 32, 32 );
                $remID      = intval( substr( $remCookieValue, 64  ) );

                if ( strlen($remUser) == 32 && strlen($remPass) == 32 ) {
                    $this->login( $remUser, $remPass, 1, $remID );
                }
            }
        }
        
        if (!empty($this->_session->session_id)) {
            session_id($this->_session->session_id);
        }
        session_start();
    }*/

    public function sessionCookieName() {
        global $mainframe, $bConfig_live_site;

        if( substr( $bConfig_live_site, 0, 7 ) == 'http://' ) {
            $hash = md5( 'site' . substr( $bConfig_live_site, 7 ) );
        } elseif( substr( $bConfig_live_site, 0, 8 ) == 'https://' ) {
            $hash = md5( 'site' . substr( $bConfig_live_site, 8 ) );
        } else {
            $hash = md5( 'site' . $mainframe->getCfg( 'live_site' ) );
        }

        return $hash;
    }

    public function sessionCookieValue( $id=null ) {
        global $mainframe;
        $type = 2;

        $browser = @$_SERVER['HTTP_USER_AGENT'];

        switch ($type) {
            case 2:
                $value          = md5( $id . $_SERVER['REMOTE_ADDR'] );
                break;
            case 1:
                $remote_addr    = explode('.',$_SERVER['REMOTE_ADDR']);
                $ip             = $remote_addr[0] .'.'. $remote_addr[1] .'.'. $remote_addr[2];
                $value          = mosHash( $id . $ip . $browser );
                break;
            default:
                $ip             = $_SERVER['REMOTE_ADDR'];
                $value          = mosHash( $id . $ip . $browser );
                break;
        }

        return $value;
    }

    public function remCookieName_User() {
        $value = 'remembercookie';
        return $value;
    }

    public function remCookieName_Pass() {
        $value = mosHash( 'remembermecookiepassword'. mainframe::sessionCookieName() );
        return $value;
    }

    public function remCookieValue_User( $username ) {
        $value = md5( $username . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );
        return $value;
    }

    public function remCookieValue_Pass( $passwd ) {
        $value  = md5( $passwd . mosHash( @$_SERVER['HTTP_USER_AGENT'] ) );
        return $value;
    }
    
    public function ajaxlogin($url = '/'){
        $email = stripslashes(strval(getParam($_POST, 'email', '')));
        $passwd   = stripslashes(strval(getParam($_POST, 'password', '')));
                
        if (!$email || !$passwd) {
            $this->error = "Вы ввели не все авторизационные данные";
            return false;
        }

        $this->m->_db->setQuery(
                " SELECT `supers`.*"
                . " FROM `supers` "
                . " WHERE `supers`.`email` = " . $this->m->_db->Quote($email)
                . " AND `supers`.`gid` >= 10 "
                . " LIMIT 1;"
        );
        //$row  = null;
        $this->m->_db->loadObject($row);
       
       
        if(!(int)$row->id){            
            $this->error = "Не правильные авторизационные данные";
            return false;
        }
            
        /*if(strlen($row->allow_ip) > 10) {
            $ips = explode("\n", $row->allow_ip);
            if (!in_array($_SERVER["REMOTE_ADDR"], $ips)) {
                $this->error = "С вашего айпи адреса доступ ограничен";
                return false;
            }
        }*/
        
        if((int)$row->status < 0){
            //redirect("/signin/?error=login-blocked");
            $this->error = "Ваш акаунт заблокирован";
            return false;
        }
        
        /*if((int)$row->bad_auth >= 5){
            //redirect("/signin/forgot/?failedlogin=true");
            $this->error = "Вы были заблокированы";
            return false;
        }*/

        //$refcookiename = "999be3440691882c7227dfad792c7833";//md5("refcookiename-keygames");

        list($hash, $salt) = explode(':', $row->password);

        $cryptpass = md5(md5($passwd).$salt);
        
        if ($hash != $cryptpass){
            //$this->m->add_to_history($row->id, "login", "failedlogin");

            $this->m->_db->setQuery(
                "UPDATE `supers` "
                    . " SET `supers`.`bad_auth` = `supers`.`bad_auth` + 1 "
                    . " ,`supers`.`last_modified` = NOW() "
                    . " WHERE `supers`.`id` = " . (int)$row->id
                    . " LIMIT 1;"
                );
            $this->m->_db->query();

            if ($row->bad_auth >= 4) {
                ///redirect("/signin/forgot/?failedlogin=true");
                $this->error = "Вы были заблокированы";
                return false;
            }

            //redirect("/signin/?error=login-incorrect");
            return false;
        }
        
        //$this->m->add_to_history($row->id);
	
        $this->addSession($row);
        
        $this->m->_db->setQuery(
            " UPDATE `supers` "
            . " SET `supers`.`last_login` = NOW() "
            . " ,`supers`.`last_ip` = " . $this->m->_db->Quote($_SERVER["REMOTE_ADDR"])
            . ($row->bad_auth ? " ,`supers`.`bad_auth` = 0 " : "")
            . " WHERE `supers`.`id` = " . (int)$row->id
            . " LIMIT 1;"
            );
        if (!$this->m->_db->query()) {
            //redirect('/');
            return false;
        }

        return true;
        //redirect($url);
    }
    
    public function login($url = '/stats/gamers'){
        $email = stripslashes(strval(getParam($_POST, 'email', '')));
        $passwd   = stripslashes(strval(getParam($_POST, 'password', '')));
                
        if (!$email || !$passwd) {
            redirect("/signin/?error=login-incomplete");
            return;
        }
        
        if (!$email || !$passwd) {
            redirect("/signin/?error=login-incorrect");
            return;
        }
        
/*        if (!$this->_session->session_id) {
            redirect('/');
            return;
        }*/
        
        $this->m->_db->setQuery(
                " SELECT `supers`.*"
                . " FROM `supers` "
                . " WHERE `supers`.`email` = " . $this->m->_db->Quote($email)
                . " AND `supers`.`gid` >= 10 "
                . " LIMIT 1;"
        );
        $row  = null;
        $this->m->_db->loadObject($row);
       
        if(!(int)$row->id){
            redirect("/signin/?error=login-incorrect");
            return;
        }
            
        if(strlen($row->allow_ip) > 10) {
            $ips = explode("\n", $row->allow_ip);
            if (!in_array($_SERVER["REMOTE_ADDR"], $ips)) {
                redirect("/signin/?error=ip-restricted");
                return;
            }
        }
        
        if((int)$row->status < 0){
            redirect("/signin/?error=login-blocked");
            return;
        }
        
        if((int)$row->bad_auth >= 5){
            redirect("/signin/forgot/?failedlogin=true");
            return;
        }

        if((int)$row->bad_withdraw_answer >= 5){
            redirect("/signin/forgot/?blockedlogin=true");
            return;
        }

        $refcookiename = "999be3440691882c7227dfad792c7833";//md5("refcookiename-keygames");
//p($row);
//           die();
        list($hash, $salt) = explode(':', $row->password);

        $cryptpass = md5(md5($passwd).$salt);
        
        if ($hash != $cryptpass){
            $this->m->add_to_history($row->id, "login", "failedlogin");

            $this->m->_db->setQuery(
                "UPDATE `supers` "
                    . " SET `supers`.`bad_auth` = `supers`.`bad_auth` + 1 "
                    . " ,`supers`.`last_modified` = NOW() "
                    . " WHERE `supers`.`id` = " . (int)$row->id
                    . " LIMIT 1;"
                );
            $this->m->_db->query();

            if ($row->bad_auth >= 4) {
                redirect("/signin/forgot/?failedlogin=true");
                return;
            }

            redirect("/signin/?error=login-incorrect");
            return;
        }
        
        $this->m->add_to_history($row->id);
	//��������� ������
        $this->addSession($row);

/*        $this->_session->guest       = '0';
        $this->_session->username    = $row->email;
        $this->_session->userid      = (int)$row->id;
        $this->_session->usertype    = "admin";
        $this->_session->gid         = (int)$row->gid;
        $this->_session->ip          = $_SERVER["REMOTE_ADDR"];
        $this->_session->user_agent  = $_SERVER['HTTP_USER_AGENT'];
        $this->_session->cookie      = $_COOKIE[$refcookiename];
        
        $this->_session->update();   */
        
        $this->m->_db->setQuery(
            " UPDATE `supers` "
            . " SET `supers`.`last_login` = NOW() "
            . " ,`supers`.`last_ip` = " . $this->m->_db->Quote($_SERVER["REMOTE_ADDR"])
            . ($row->bad_auth ? " ,`supers`.`bad_auth` = 0 " : "")
            . " WHERE `supers`.`id` = " . (int)$row->id
            . " LIMIT 1;"
            );
        if (!$this->m->_db->query()) {
            redirect('/');
            return;
        }

        redirect($url);
    }
    
    public function logout($url = '/') {
        $session =& $this->_session;                    
        $session->delete();

        $lifetime       = time() - 86400;
        setcookie(md5('cookiename'), ' ', $lifetime, '/');

        @session_destroy();
        
        redirect($url);
    }
    
    public function set( $property, $value=null ) {
        $this->$property = $value;
    }

    public function get($property, $default=null) {
        if(isset($this->$property)) {
                return $this->$property;
        } else {
                return $default;
        }
    }

    public function getUser() {
        static $instance;
        
        if (is_object($instance)) return $instance;
        
        $user_id = intval($this->_session->userid);

        if (!$user_id) return array();
        
        $this->m->_db->setQuery(
                " SELECT `supers`.* "
                . " FROM `supers` "
                . " WHERE `supers`.`gid` >= 10 "
                . " AND `supers`.`status` > -1 "
                . " AND `supers`.`id` = " . $this->m->_db->Quote($user_id)
            );
        $this->m->_db->loadObject($instance);
        unset($instance->password);

        return $instance;
    }
    
    function checkUserPassword($password) {
        $user_id = intval($this->_session->userid);
        
        if (!$user_id)
            return false;
        
        $this->m->_db->setQuery(
            " SELECT `password` "
            . " FROM `users` "
            . " WHERE `users`.`gid` >= 10 "
            . " AND `users`.`status` > -1 "
            . " AND `users`.`id` = " . $this->m->_db->Quote($user_id)
            );

        $this->m->_db->loadObject($row);

        list($hash, $salt) = explode(':', $row->password);

        if (empty($hash))
            return false;

        $cryptpass = md5(md5($password).$salt);

        if ($hash == $cryptpass) {
            return true;
        } else {
            return false;
        }
    }
    
    function checkUserAnswer($answer) {
        $user_id = intval($this->_session->userid);

        if (!$user_id)
            return false;
        
        $this->m->_db->setQuery(
            " SELECT `answer` "
            . " FROM `users` "
            . " WHERE `users`.`gid` >= 10 "
            . " AND `users`.`status` > -1 "
            . " AND `users`.`id` = " . $this->m->_db->Quote($user_id)
            );
                
        $user_answer = iconv("WINDOWS-1251","UTF-8",$this->m->_db->loadResult());

        if (empty($user_answer))
            return false;

        if ($user_answer == $answer) {
            return true;
        } else {
            return false;
        }
    }   
}

function mosErrorAlert($err) {
    echo "<script>alert('".$err."');</script>";
}
?>