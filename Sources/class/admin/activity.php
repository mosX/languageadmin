<?php 
class Activity{
    protected $_table = 'visitors';
 
    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;
    }
    
    public function goalsStatistic(){
        $start = $_GET['date_from'] ? date('Y-m-d H:i:s',strtotime($_GET['date_from'])) : null;
        $end = $_GET['date_to'] ? date('Y-m-d H:i:s',strtotime($_GET['date_to'])) : null;
        $visitor = (int)$_GET['visitor'];
        $activity = (int)$_GET['activity'];
        $goal = (int)$_GET['goal'];
        
        $this->m->_db->setQuery(
                    "SELECT COUNT(`goals`.`id`) as cnt"
                    . " FROM `goals`"
                    . " WHERE 1"
                    . ($start ? " AND `goals`.`date` > '".$start."'" : "")
                    . ($end ? " AND `goals`.`date` < '".$end."'" : "")
                    . ($visitor ? " AND `goals`.`visitor_id` = ".$visitor:"")
                    . ($activity ? " AND `goals`.`activity_id` = ".$activity:"")
                    . ($goal ? " AND `goals`.`goal` = '".$this->m->config->goals[$goal]."'":"")
                );
        $total = $this->m->_db->loadResult();
        
        $xNav = new xNav("/activity/goals/", $total, "GET");
        $xNav->limit = 20;
        $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `goals`.`id` "
                    . ", `goals`.`visitor_id`"
                    . ", `goals`.`activity_id`"
                    . ", `goals`.`goal`"
                    . ", `goals`.`date`"
                    . ", `visitors_activity`.`user_agent`"
                    . " FROM `goals`"
                    . " LEFT JOIN `visitors_activity` ON `visitors_activity`.`id` = `goals`.`activity_id` "
                    . " WHERE 1"
                    . ($start ? " AND `goals`.`date` > '".$start."'" : "")
                    . ($end ? " AND `goals`.`date` < '".$end."'" : "")
                    . ($visitor ? " AND `goals`.`visitor_id` = ".$visitor:"")
                    . ($activity ? " AND `goals`.`activity_id` = ".$activity:"")
                    . ($goal ? " AND `goals`.`goal` = '".$this->m->config->goals[$goal]."'":"")
                    . " ORDER BY `goals`.`id` DESC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        $rows = $this->m->_db->loadObjectList();
        
        return $rows;
    }
    
    public function activityStatistic(){
        $ips = array('178.154.243.116','184.22.42.196','69.64.32.191','38.99.82.230');
        
        //178.154.243.116 YandexMetrika
        //184.22.42.196  compatible
        //69.64.32.191 	bot-pge.chlooe.com/1.0.0 (+http://www.chlooe.com/)
        //38.99.82.230 	Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0)
        
        /*. ( $sub_page_filter == 'balance' || $sub_page_filter == 'balanceonline'? " AND `".$this->_table."`.`balance` > 100" : "") 
        . ( $sub_page_filter == 'withoutbalance' ? " AND `".$this->_table."`.`balance` = 0" : "") 
        . ( $sub_page_filter == 'balanceonline' ? " AND `x_session`.`userid` = `users`.`id`" : "" )*/
        
        $start = $_GET['date_from'] ? date('Y-m-d H:i:s',strtotime($_GET['date_from'])) : null;
        $end = $_GET['date_to'] ? date('Y-m-d H:i:s',strtotime($_GET['date_to'])) : null;
        $visitor_id = $_GET['visitor'] ?:null;
        $user = $_GET['user'];
        $ip = $_GET['ip'];
        $user_agent = $_GET['useragent'];
        
        $sub_page_filter = strip_tags($this->m->_path[2]);
        
        switch($user_agent){
            case 1: $user_agent_sql = " AND `visitors_activity`.`user_agent` LIKE '%Firefox%'";break; //FireFox
            case 2: $user_agent_sql = " AND `visitors_activity`.`user_agent` LIKE '%Chrome%'";break; //Chrome
            case 3: $user_agent_sql = " AND `visitors_activity`.`user_agent` LIKE '%Trident%like Gecko%'";break; //IE
            case 4: $user_agent_sql = " AND `visitors_activity`.`user_agent` LIKE '%AppleWebKit%Version%Safari%'";break; //Safari
            default:'';
        }
        
        $table = 'visitors_activity';
        $sql = ($visitor_id ? " AND `".$table."`.`visitor_id` = '".$visitor_id."'":"")
                . ($ip ? " AND `".$table."`.`ip` = '".$ip."'":"")
                . ($user != ''? " AND `".$table."`.`user_id` = ".(int)$user:"")
                . ($start? " AND `".$table."`.`activity` > '".$start."'" : "")
                . ($end? " AND `".$table."`.`activity` < '".$end."'" : "")
            
                . ($ips? " AND `".$table."`.`ip` NOT IN ('".implode('\',\'',$ips)."')":"")
            
                . ($sub_page_filter == 'logged' ? " AND `".$table."`.`user_id` > 0" : "")
                . ($sub_page_filter == 'unlogged'? " AND `".$table."`.`user_id` = 0" : "")
                . $user_agent_sql;
        
        $this->m->_db->setQuery(
                    "SELECT COUNT(`".$table."`.`id`) as cnt "
                    . " FROM `".$table."`"
                    . " WHERE `".$table."`.`ip` != '87.118.126.64'" //наш айпи
                    .$sql
                );
        $total = $this->m->_db->loadResult();
        
            $xNav = new xNav("/activity/stat/", $total, "GET");
            $xNav->limit = 20;
            $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `".$table."`.* "
                    . " FROM `".$table."`"
                    . " WHERE `".$table."`.`ip` != '87.118.126.64'" //наш айпи
                    . $sql
                    . " ORDER BY `".$table."`.`id` DESC"
                . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        
        
        $row = $this->m->_db->loadObjectList();
        return $row;
    }
    
    public function visitorsStatistic(){
        $start = $_GET['date_from'] ? date('Y-m-d H:i:s',strtotime($_GET['date_from'])) : null;
        $end = $_GET['date_to'] ? date('Y-m-d H:i:s',strtotime($_GET['date_to'])) : null;
        $uid = $_GET['id'] ?:null; 
        $ip = $_GET['ip'];  
        $user_agent = $_GET['useragent'];
        
        switch($user_agent){
            case 1: $user_agent_sql = " AND `visitors`.`user_agent` LIKE '%Firefox%'";break; //FireFox
            case 2: $user_agent_sql = " AND `visitors`.`user_agent` LIKE '%Chrome%'";break; //Chrome
            case 3: $user_agent_sql = " AND `visitors`.`user_agent` LIKE '%Trident%like Gecko%'";break; //IE
            case 4: $user_agent_sql = " AND `visitors`.`user_agent` LIKE '%AppleWebKit%Version%Safari%'";break; //Safari
        }
               
        $this->m->_db->setQuery(
                    "SELECT COUNT(`visitors`.`id`) as cnt "
                    . " FROM `visitors`"
                    . " WHERE 1"
                    . ($uid? " AND `visitors`.`uid` = '".$uid."' OR `visitors`.`id` = '".$uid."'":"")
                    . ($ip ? " AND `visitors`.`ip` = '".$ip."'" : "")
                    . ($start? " AND `visitors`.`activity` > '".$start."'" : "")
                    . ($end? " AND `visitors`.`activity` < '".$end."'" : "")
                    . $user_agent_sql
                );
        $total = $this->m->_db->loadResult();
        
        $xNav = new xNav("/activity/visitors/", $total, "GET");
        $xNav->limit = 20;
        $this->m->pagesNav = $xNav->showPages();
        
        $this->m->_db->setQuery(
                    "SELECT `visitors`.* "
                    . " FROM `visitors`"
                    . " WHERE 1"
                    . ($uid? " AND `visitors`.`uid` = '".$uid."' OR `visitors`.`id` = '".$uid."'":"")
                    . ($ip ? " AND `visitors`.`ip` = '".$ip."'" : "")
                    . ($start? " AND `visitors`.`activity` > '".$start."'" : "")
                    . ($end? " AND `visitors`.`activity` < '".$end."'" : "")
                    . $user_agent_sql
                    . " ORDER BY `visitors`.`id` DESC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        $row = $this->m->_db->loadObjectList();
        return $row;
    }
    
  

}
?>