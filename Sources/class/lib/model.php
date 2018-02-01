<?php
class Model {
	var $text = '';
	var $table = '';
	var $_db = '';
        
        private $_ajax = false;
        private $_view = null;

	function __construct(mainframe & $mainframe) {
		$this->_db =& $mainframe->_db;
		$this->m = $mainframe;
		$this->_path = $mainframe->_path;
                
                if(method_exists($this,'init')){
                    $this->init();
                }
	}
        public function __destruct(){
            if($this->_ajax){
                    xload("class.lib.phpmailer");
                    @include_once XPATH . DS .'html'.DS.'modules'.DS. $this->_view . '.php';
            }else{ 
                
                //что бы не выводить Вйу
                if(false === $this->_view) return;
                
                if(null == $this->_view){
                    if(file_exists(XPATH . DS .'html'.DS.'views'.DS.$this->m->_permission.DS.$this->m->_controller.DS.$this->m->_action.'.php')){                                                
                        @include_once XPATH . DS .'html'.DS.'views'.DS.$this->m->_permission.DS.$this->m->_controller.DS.$this->m->_action.'.php';
                    }else{
                        @include_once XPATH . DS .'html'.DS.'views'.DS.'common'.DS.'error'.DS.'index'.'.php';
                    }
                }else{
                    if(file_exists(XPATH . DS .'html'.DS.'views'.DS.$this->m->_permission.DS.$this->m->_controller.DS.$this->_view.'.php')){
                        @include_once XPATH . DS .'html'.DS.'views'.DS.$this->m->_permission.DS.$this->m->_controller.DS.$this->_view.'.php';
                    }else{
                        @include_once XPATH . DS .'html'.DS.'views'.DS.'error'.DS.'index'.'.php';
                    }
                }
            }
        }
        public function disableView(){
            $this->_view = false;
        }
        public function disableTemplate(){
            $this->m->_template = '';
        }
        public function setView($view,$ajax=false){
                $this->_view = $view;
                $this->_ajax = $ajax;
        }

	public function selectAll($where=null, $limit=null) {
		if (!empty($this->table)) {
			$query = "SELECT * FROM `".$this->table."`";
			if (isset($where)) $query .= " WHERE ".$where;
			if (isset($limit)) $query .= " LIMIT ".$limit;
		}
		if (isset($this->_db)) {
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		}
	}

	public function selectwCategory($type="", $where=null, $limit=null, $joinField = "catid") {
		if (!empty($this->table)) {
			$query = "SELECT a.*, b.title as category, FROM `".$this->table."` "
				."\n LEFT JOIN x_category b ON a.catid=b.id"
				;
			if (isset($where)) $query .= " WHERE ".$where;
			if (isset($limit)) $query .= " LIMIT ".$limit;
		}
		if (isset($this->_db)) {
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		}
	}

	public function getCategories($type="c", $parent=0, $where=null, $limit=null) {
		if (!empty($this->table)) {
			if ($parent==-1) $parent = "";
			else  $parent = " AND parent='".$parent."'";
			$query = "SELECT * FROM `x_categories` WHERE `type`='".$type."'" . $parent;
			if (isset($where)) $query .= "AND ".$where;
			if (isset($limit)) $query .= " LIMIT ".$limit;
		}
		if (isset($this->_db)) {
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		}
	}

	public function output() {
		return $this->text;
	}
	
	function setTitle($title) {
		global $mainframe;
		$mainframe->setTitle($title);
		return;
	}

}
