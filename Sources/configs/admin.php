<?php        
$config = array(
    
    "projects"=>array(
        array('tag'=>'binsecret','name'=>"Binsecret",'code'=>'d951062b427e28d33c8a6d93fad48384'),
        array('tag'=>'platinumbin','name'=>"PlatinumBin",'code'=>'d951062b427e28d33c8a6d93fad48384'),
        array('tag'=>'libreoption','name'=>"LibreOption",'code'=>'d951062b427e28d33c8a6d93fad48384'),
    ),
);

$this->addCSS('bootstrap.min')->addCSS('main')->addCSS('contacts');
$this->preAddJS('jquery')->addJS('bootstrap.min')->addJS('angular.min')->addJS('main')->addJS('admin');

?>