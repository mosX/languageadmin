<?php
class Notes{
    protected $_table = 'notes';
    public $note;

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
    public function addNew(){
        $row->user_id = $_POST['user_id'];
        $row->parent_id = $this->m->_user->id;
        $row->message = $_POST['message'];
        $row->date = date("Y-m-d H:i:s");
        
        if($this->m->_db->insertObject('notes',$row,'id')){
            $this->note = $row;
            //echo '{"status":"success"}';
            return true;
        }else{
            //echo '{"status":"error"}';
            
            return false;
        }                
    }
}
?>