<?php
/**
* Session database table class
*/
class xSession extends DBTable {
    public $session_id               = null;
    public $time                     = null;
    public $userid                   = null;
    public $usertype                 = null;
    public $username                 = null;
    public $gid                      = null;
    public $guest                    = null;
    public $_session_cookie          = null;
    public $ip                       = null;
    public $user_agent               = null;
    

    public function __construct(mainframe & $mainframe) {
        $this->m = $mainframe;
        $this->DBTable( 'x_session', 'session_id', $this->m->_db );
    }
        
    public function get( $key, $default=null ) {
        return getParam( $_SESSION, $key, $default );
    }

    public function set( $key, $value ) {
        $_SESSION[$key] = $value;
        return $value;
    }

    public function setFromRequest( $key, $varName, $default=null ) {
        if (isset( $_REQUEST[$varName] )) {
            return Session::set( $key, $_REQUEST[$varName] );
        } else if (isset( $_SESSION[$key] )) {
            return $_SESSION[$key];
        } else {
            return Session::set( $key, $default );
        }
    }

    public function insert() {
        $ret = $this->m->_db->insertObject( $this->_tbl, $this );

        if( !$ret ) {
            $this->_error = strtolower(get_class( $this ))."::".__STORE_FAILED." <br />" . $this->m->_db->stderr();
            return false;
        } else {
            return true;
        }
    }

    public function update( $updateNulls=false ) {
        $ret = $this->m->_db->updateObject( $this->_tbl, $this, 'session_id', $updateNulls );

        if( !$ret ) {
            $this->_error = strtolower(get_class( $this ))."::".__STORE_FAILED." <br />" . $this->m->_db->stderr();
            return false;
        } else {
            return true;
        }
    }

    public function generateId() {
        $failsafe = 20;
        $randnum = 0;

        while ($failsafe--) {
            $randnum = md5( uniqid( microtime(), 1 ) );
            $new_session_id = xAuth::sessionCookieValue( $randnum );
            
            if ($randnum != '') {
                $query = "SELECT $this->_tbl_key"
                       . "\n FROM $this->_tbl"
                       . "\n WHERE $this->_tbl_key = " . $this->m->_db->Quote( $new_session_id )
                       ;
                $this->m->_db->setQuery( $query );
                if(!$result = $this->m->_db->query()) {
                    die( $this->m->_db->stderr( true ));
                }
                
                if ($this->m->_db->getNumRows($result) == 0) {
                    break;
                }
            }
        }
        
        $this->_session_cookie = $randnum;
        $this->session_id       = $new_session_id;
    }

    public function getCookie() {
        return $this->_session_cookie;
    }

    function purge( $inc=1800, $and='' ) {
        $past_logged    = time() - 6000; //1800
        $past_guest     = time() - 7200;

        $query = "DELETE FROM $this->_tbl"
        . "\n WHERE "
        // purging expired logged sessions
        . "\n ( time < '" . (int)$past_logged . "' "
        . "\n AND (gid = 1 OR gid = 2)"
        . "\n ) OR "
        . "\n ( time < '" . (time() - 14400) . "' "
        . "\n AND guest = 0"
        . "\n AND (gid = 0 OR gid > 2)"
        . "\n ) OR "
        // purging expired guest sessions
        . "\n ( time < '" . (int)$past_guest . "' "
        . "\n AND guest = 1"
        . "\n )"
        ;
        $this->m->_db->setQuery($query);

        return $this->m->_db->query();
    }
    
    public function delete() {
        $query = "DELETE FROM $this->_tbl"
                . "\n WHERE $this->_tbl_key = " . $this->m->_db->Quote( $this->session_id )
                . "\n LIMIT 1;"
                ;

        $this->m->_db->setQuery( $query );

        if ($this->m->_db->query()) {
            return true;
        } else {
            $this->_error = $this->m->_db->getErrorMsg();
            return false;
        }
    }
}
?>