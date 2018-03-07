<?php
class mainframe {
    public $_title = null;
    public $_head  = null;
    public $_template = null;
    public $_lang = null;
    public $maincontent = null;
    public $templatepath = null;
    public $abstemplatepath = null;
    public $config = null;
    public $_db = null;
    private $_islogin = false;
    public $_user = null;
    public $menu = array();
    public $_ip = null;
    
    public $questions;
    
    public $_controller;
    public $_action;
    public $_scripts = array();
    public $_jsparams = array();
    public $_stylesheet = array();

    function run(){
        session_start();
        $this->parsePath();
        $this->setConfig();
       
        $this->setDB();
        $this->_auth = new xAuth($this);
        $this->_auth->initSession();

        $this->_user = $this->_auth->getUser();
        
      /*  if (is_object($this->_user) && $this->_user->id > 0) {
            $this->_islogin = true;
        }*/
        
        $this->getUnreadedMail();
    
        $this->setPermissions();
        $this->page();
        $this->output();
    }
    public function getUnreadedMail(){
        $this->_db->setQuery(
                    "SELECT COUNT(`feedback`.`id`) "
                    . " FROM `feedback` "
                    . " WHERE `feedback`.`status` = 1"
                    . " AND `feedback`.`unread` = 1"                
                );
        $this->unreaded_mail = $this->_db->loadResult();
    }

    public function setPermissions(){
         //устанавливаем уровень доступа
        
        switch($this->_user->gid){
            case 10: $this->_permission = 'admin'; break;
            case 15: $this->_permission = 'super'; break;
            case 20: $this->_permission = 'support'; break;
            case 30: $this->_permission = 'operator'; break;
            default :$this->_permission = 'guest'; break;
        }

        if(file_exists(XPATH_SOURCES . DS . 'configs' . DS .  $this->_permission.'.php')){  //добавляем к конфигам
            
            include(XPATH_SOURCES . DS . 'configs' . DS .  $this->_permission.'.php');
            foreach($config as $k => $v) {
                $this->config->$k = $v;
            }
        }
    }
    
    protected function page() {
        $needlogged = array("historygames");

        if (isset($this->_path[0]) && in_array($this->_path[0], $needlogged) && !$this->_islogin) {
            redirect("/signin/");
        }
        
        if(!empty($this->_path['0'])){
            $this->_controller = str_replace('-', '_', $this->_path['0']);
            if (!empty($this->_path['1'])) {
                $this->_action = str_replace('-', '_', $this->_path['1']);
            } else {
                $this->_action = 'index';
            }
        } else {
            $this->_action = 'index';
            $this->_controller = 'index';
        }
        
        xload('class.lib.model');
        
        if(file_exists(XPATH_SOURCES . DS . 'Controllers' .DS . $this->_permission.  DS . $this->_controller . 'Controller.php')){            
            require_once XPATH_SOURCES . DS . 'Controllers'  .DS. $this->_permission. DS . $this->_controller . 'Controller.php';
        }else if (file_exists(XPATH_SOURCES . DS . 'Controllers' .  DS . $this->_controller . 'Controller.php')) {
            require_once XPATH_SOURCES . DS . 'Controllers'  . DS . $this->_controller . 'Controller.php';
        }
        
        $objName = $this->_controller . 'Controller';
        $actName = $this->_action . 'Action';
        if (method_exists($objName,$actName)){
            $this->controller = new $objName($this);
            ob_start();
                $this->controller->$actName();
                unset($this->controller);
                $this->maincontent = ob_get_contents();
            ob_end_clean();
            
            return;
        }
        
        $this->_controller = 'error';
        $this->_action = 'index';
        
        require_once XPATH_SOURCES . DS .'Controllers' . DS . 'errorController.php';
        $this->controller = new errorController($this);
        ob_start();
            $this->controller->indexAction();
            unset($this->controller);
            $this->maincontent = ob_get_contents();
        ob_end_clean();
    }
    
    function output() {
        if ($this->_controller == 'error') {
            header('HTTP/1.0 404 Not Found');
        }
        
        if ($this->_template === ''){
            
            echo $this->maincontent;
        } elseif (!empty($this->_template)) {
            
            require(XPATH .DS . 'html' .DS. 'templates' . DS . $this->_template . '.php');
        } else {
            
            if($this->_permission == 'support' || $this->_permission == 'operator' || $this->_permission == 'admin'){                
                include(XPATH .DS . 'html' .DS . 'templates' . DS . 'template.php');
            }else{
                include(XPATH .DS . 'html' .DS . 'templates' . DS . 'template.php');    
            }
        }
    }
    
    public function setDescription($description) {
        $this->_description = strip_tags($description);
    }
    
    public function setKeywords($keywords) {
        $this->_keywords = strip_tags($keywords);
    }
    
    function showPathway() {
        if (count($this->_pathway)) {
            echo "<div class=\"pathway\">";
            $delimiter = isset($this->pathdelimiter) ? $this->pathdelimiter : " >> ";
            foreach ($this->_pathway as $pathelement) {
                $elements[] = "<a href=\"".$pathelement[1]."\">".$pathelement[0]."</a>";
            }
            echo implode($delimiter, $elements);
            echo "</div>";
        }
    }
    
    public function setTemplate($template, $flag = false){
        $this->_template = $template;
        
        if ($flag == true) {
            $this->_title = null;
            $this->_scripts = array();
            $this->_stylesheet = array();
        }
    }
    
    public function setTitle($title) {
        if (!empty($this->_title))
            $this->_title = strip_tags($title) . " - " . $this->_title;
        else
            $this->_title = strip_tags($title);
    }
    
    public function header() {
        echo "<title>" . $this->_title . "</title>\n"
             . ($this->_description != null ? "<meta name=\"description\" content=\"" . $this->_description . "\" />\n" : "")
             . ($this->_keywords != null ? "<meta name=\"keywords\" content=\"" . $this->_keywords . "\" />\n" : "")
             ;
        
        if (is_array($this->_head) && count($this->_head)) echo implode("\n", $this->_head)."\n";
    }
    
    public function setConfig($name='config'){
        
        if (!file_exists(XPATH_SOURCES . DS . 'configs' . DS .  $name.'.php'))
            return false;

        include(XPATH_SOURCES . DS . 'configs' . DS .  $name.'.php');

        if (!is_array($config))
            return false;

        foreach($config as $k => $v) {
            $this->config->$k = $v;
        }

        return true;
    }
    
/*    protected function setDB() {
        xload('class.lib.database');
        $this->_db = new database($this->config->host, $this->config->user, $this->config->pass, $this->config->db, $this->config->prefix);
    }*/
    public function setDB($data) {
        xload('class.lib.database');
        $this->_db = new database($this->config->host, $this->config->user, $this->config->pass, $this->config->db, $this->config->prefix);
    }

public function add_to_history($user_id = null, $type = null, $action = null, $value = null) {
        $history->user_id = (int)$user_id;
        if (!empty($type))
            $history->type = $type;
        
        if (!empty($action))
            $history->action = $action;
        
        if (!empty($value))
            $history->value = $value;
        
        $history->ip          = $_SERVER["REMOTE_ADDR"];
        $history->user_agent  = $_SERVER["HTTP_USER_AGENT"];
        $refcookiename = "999be3440691882c7227dfad792c7833";//md5("refcookiename-keygames");
        $history->cookie      = $_COOKIE[$refcookiename];
        $history->date        = date("Y-m-d H:i:s");
        if ($this->_db->insertObject("history", $history))
            return true;
        
        return false;
    }

/*    public function add_to_history($user_id = null, $type = null, $action = null) {
        $history->user_id = (int)$user_id;
        if (!empty($type))
            $history->type = $type;
        
        if (!empty($action))
            $history->action = $action;
        
        $history->ip          = $_SERVER["REMOTE_ADDR"];
        $history->user_agent  = $_SERVER["HTTP_USER_AGENT"];
        $refcookiename = "999be3440691882c7227dfad792c7833";//md5("refcookiename-keygames");
        $history->cookie      = $_COOKIE[$refcookiename];
        $history->date        = date("Y-m-d H:i:s");
        if ($this->_db->insertObject("history", $history))
            return true;
        
        return false;
    } */
    
    protected function parsePath() {
        $REQUEST_URI = $_SERVER["REQUEST_URI"];
        if (!empty($_SERVER['QUERY_STRING']))
            $REQUEST_URI = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER["REQUEST_URI"]);
        
        if (substr($REQUEST_URI, -1) != '/' && 'GET' == $_SERVER['REQUEST_METHOD']) {
            @header('HTTP/1.1 301 Moved Permanently');
            @header('Location: ' . $REQUEST_URI . '/' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
            die();
        }
        
        $path = explode('/', strtolower($REQUEST_URI));
        array_shift($path);

        if (empty($path[count($path)-1]))
            array_pop($path);

        if (in_array($path[0], $this->config->available_languages)) {
            $this->_lang = $path[0];
            array_shift($path);
        } elseif (!isset($_COOKIE['lang'])) {
            //$this->_lang = self::getLangByIP();
            $this->_lang = $this->config->defaultlang;
        } else {
            $this->_lang = $_COOKIE['lang'];
        }
        setcookie('lang', $this->_lang, 0, '/');
        
        $this->_path = $path;
    }
    
    //если язык не был указан то узнаем язык по АйПи пользователя
    protected function getLangByIP(){
        $this->_ip = $_SERVER["REMOTE_ADDR"];
        $int = self::ip2int($this->_ip);
        
        $country_id = 0;
        
        $query = "SELECT * FROM (SELECT * FROM net_euro WHERE begin_ip <= $int ORDER BY begin_ip DESC LIMIT 1) AS t WHERE end_ip >= $int";
        $this->_db->setQuery($query);
        $country_id = $this->_db->loadResult();
        
        if (empty($country_id)) {
            $query = "SELECT country_id FROM (SELECT * FROM net_country_ip WHERE begin_ip <= $int ORDER BY begin_ip DESC LIMIT 1) AS t WHERE end_ip >= $int";
            $this->_db->setQuery($query);
            $country_id  = $this->_db->loadResult();
        }
        
        $query = "SELECT lang FROM net_country WHERE id = '" . $country_id . "' LIMIT 1";
        $this->_db->setQuery($query);
        $lang = $this->_db->loadResult();
        
        if (empty($lang)) {
            $lang = $this->config->defaultlang;
        }
        
        return $lang;
    }
    
    //переводим АйПи в числовое представление для поиска по базе
    private function ip2int($ip){
        $part = explode(".", $ip);
        $int = 0;
        if (count($part) == 4) {
            $int = $part[3] + 256 * ($part[2] + 256 * ($part[1] + 256 * $part[0]));
        }
        return $int;
    }
    
    protected function setLang() {        
        //setlocale(LC_MESSAGES, $this->_lang . '_' . strtoupper($this->_lang) . '.UTF-8');
        putenv("LC_MESSAGES=".$this->_lang . '_' . strtoupper($this->_lang) . '.UTF-8');
        bindtextdomain('messages', XPATH .DS . 'html' .  DS . 'locale' . DS);
        bind_textdomain_codeset('messages', 'UTF-8');
        textdomain('messages');
    }
    
    function islogin() {
        return $this->_islogin;
    }

    function module($name='') {
        if (!empty($name) && file_exists(XPATH .DS . 'html' . DS . 'modules'.DS . $this->_permission .  DS . $name . '.php')) {
            require_once(XPATH .DS . 'html' . DS. 'modules'. DS. $this->_permission .  DS . $name . '.php');
        } elseif(!empty($name) && file_exists(XPATH .DS . 'html'  .DS. 'modules' .  DS . $name . '.php')){
            require_once(XPATH .DS . 'html'  . DS . 'modules' .  DS . $name . '.php');
        }
    }

    function addHeadTag($tag) {
        $this->_head[] = $tag;
    }
    
    public function js() {
        $links = null;
        foreach($this->_scripts as $key => $item) {
            if (file_exists(XPATH .DS . 'html' . DS . 'js' . DS . $item . '.js')) {
                if ($this->_jsparams[$key] == null) {
                    $links .= '<script src="/html/js/' . $item . '.js" type="text/javascript" /></script>';
                } else {
                    $links .= '<script src="/html/js/' . $item . '.js?' . $this->_jsparams[$key] . '" type="text/javascript" /></script>';
                }
            }
        }
        return $links;
    }
    
    public function addJS($jsfile, $jsparam = null) {
        if (file_exists(XPATH .DS . 'html' . DS . 'js' . DS . $jsfile . '.js')) {
            array_push($this->_scripts, $jsfile);
            array_push($this->_jsparams, $jsparam);
        }
        return $this;
    }
    
    public function preAddJS($jsfile, $jsparam = null) {
        if (file_exists(XPATH .DS . 'html' .  DS . 'js' . DS . $jsfile . '.js')) {
            array_unshift($this->_scripts, $jsfile);
            array_unshift($this->_jsparams, $jsparam);
        }
        return $this;
    }
    
    public function addCSS($name) {
        if (file_exists(XPATH .DS . 'html' . DS . 'css' . DS . $name . '.css')) {
            array_push($this->_stylesheet, $name);
        }
        return $this;
    }
    
    public function preAddCSS($name) {
        if (file_exists(XPATH .DS . 'html' . DS . 'css' . DS . $name . '.css')) {
            array_unshift($this->_stylesheet, $name);
        }
        return $this;
    }
    
    //�?зменить что бы само определяло с какой папки тянуть ЦССК�?
    public function css($flag = false){
        $links = null;
        $data = null;
        //если нужно просто вывести добавленные файлы
        if ($flag == false) {
            foreach($this->_stylesheet as $file) {
                if (file_exists(XPATH .DS . 'html' . DS . 'css' . DS . $file . '.css')) {
                    $links .= '<link href="/html/css/' . $file . '.css' . '" rel="stylesheet" type="text/css" />';
                }
            }
            return $links;
        //если нужно обьеденить и ужать в один файл выбранные файлы
        } else {
            if(!file_exists(XPATH .DS . 'html' . DS . 'css' . DS .$this->_controller.DS.$this->_action.'.css')){
                foreach($this->_stylesheet as $file){
                    if (file_exists(XPATH .DS . 'html' . DS .'css'. DS . $file . '.css')) {
                        $content = file_get_contents(XPATH .DS . 'html' . DS . 'css'. DS . $file . '.css');
                    }
                    //начинаем парсить контент и уберать лишнее
                    $start = strlen($content);
                    
                    $data .= $content;
                    
                    $end = strlen($content);
                }
                
                if(!is_dir(XPATH .DS . 'html' .  DS . 'css' . DS . $this->_controller)) {
                    mkdir(XPATH .DS . 'html' . DS . 'css' . DS . $this->_controller, 0700);
                }
                file_put_contents(XPATH .DS . 'html' . DS . 'css' . DS . $this->_controller . DS . $this->_action . '.css', $data);
            }
            
            $links = '<link href="/html/css/' . $this->_controller . '/' . $this->_action . '.css" rel="stylesheet" type="text/css" />';
            return $links;
            
        }
    }

    public function getCountryByIP($ip = "") {
        if (empty($ip))
            return;

        $int = sprintf("%u", ip2long($ip));

        $country_id = 0;

        //$query = "select `country_id` from (select * from x_net_euro where begin_ip <= " . $int . " order by begin_ip desc limit 1) as t where end_ip >= " . $int;
        //$this->_db->setQuery($query);
        //$country_id = (int)$this->_db->loadResult();

        //if (empty($country_id)) {
            $query = "select `country_id` from (select * from x_net_country_ip where begin_ip <= " . $int . " order by begin_ip desc limit 1) as t where end_ip >= " . $int;
            $this->_db->setQuery($query);
            $country_id  = (int)$this->_db->loadResult();
        //}

        $query = "select `code` from x_net_country where id = " . $this->_db->Quote($country_id);
        $this->_db->setQuery($query);
        $country = strtolower($this->_db->loadResult());

        if (empty($country))
            $country = "xx";

        return $country;
    }
}
class xNav{
    public $total = 0;
    public $start = 0;
    public $limit = 10;
    public $page  = 1;
    public $url   = "";
    public $_prev = "< Назад";
    public $_next = "Вперед >";


    public function __construct($url, $total=0, $method="") {
        $this->total = $total;

        $this->parseUrl($url, $method);

        $this->page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;

        $this->start = ($this->page-1)*$this->limit;

        if ($this->start > $total) { 
            $this->page = 1;
            $this->start = ($this->page-1)*$this->limit;
        }
    }

    public function showPages() {
            $total_pages = ceil($this->total/$this->limit);

            if ($total_pages>10) {
                $pages_del = array("...");
                if ($this->page<9) {
                    $pages = array_merge(range(1,10), $pages_del, range($total_pages-1, $total_pages));
                }elseif ($this->page+8>$total_pages) {
                    $pages = array_merge(range(1,2), $pages_del, range($total_pages-9, $total_pages));
                }else{
                    $pages = array_merge(range(1,2), $pages_del, range($this->page-5, $this->page+5), $pages_del, range($total_pages-1, $total_pages));
                }
            }elseif ($total_pages>0){ 
                $pages = range(1,$total_pages);
            }else {
                $pages = array(1);
                $total_pages = 1;
            }

            $str = "<ul class='pagination'>";

            if ($this->page==1) {
		$str .= '<li><a onClick="return false;" class="navpages prev" href="">'.$this->_prev.'</a></li>';
            }else{
		$str .= '<li><a class="navpages prev active" href="'.$this->url.'limit='.$this->limit.'&page='.($this->page-1).'">'.$this->_prev.'</a></li>';
            }

            for($i=0; $i < count($pages);  $i++) {
                if ($pages[$i]=="...") {
                    //$str .= " <span> " . $pages[$i]  . " </span> ";
                    $str .= " <li><a href='' class='navpages'> " . $pages[$i]  . " </a></li> ";
                }
                elseif ($pages[$i]==$this->page) {
			$str .= '<li class="active"><a onClick="return false" class="navpages pages active" href="">'.$pages[$i].'</a></li>';
                }
                else {
			$str .= '<li><a class="navpages pages" href="'.$this->url.'limit='.$this->limit.'&page='.$pages[$i].'">'.$pages[$i].'</a></li>';			
                }
            }

            if ($this->page==$total_pages) {
		$str .= '<li><a onClick="return false" class="navpages next" href="">'.$this->_next.'</a></li>';
            }
            else {
		$str .= '<li><a class="navpages next active" href="'.$this->url.'limit='.$this->limit.'&page='.($this->page+1).'">'.$this->_next.'</a></li>';
            }
            return $str.'</ul>';
    }


    function parseUrl($url,$method) {
            $str = array();
            if ($method == "GET") {
                foreach($_GET as $k=>$v) {
                    if ($k=="limit" || $k=="page") continue;
                    if (is_array($v)) {
                        foreach($v as $ky => $val) {
                                $str[] = $k."[".$ky."]=".$val;
                        }
                    }
                    else
                    $str[] = $k."=".$v;
                }
                if (count($str)) {
                    $this->url = $url."?".implode("&",$str);
                }else{
                    $this->url = $url;
                }
            }else $this->url = $url;
            
            strpos($this->url,'?') ? $this->url .= '&' : $this->url .= '?';
            
            
            if (isset($_GET["limit"])) $this->limit = getParam($_GET, "limit",10);
    }
}
?>