<?php
$xname = array();
$xname['24playcasino'] = '24playcasino';
$xname['front2'] = 'front2';
$xname['betwinn'] = 'betwinn';
//$xname['maxino'] = 'maxino';


define( 'XNAME',        'binary');

define('XPATH_SOURCES',    XPATH.DS.'Sources');
//define('XPATH_TEMPLATE',   XPATH.DS.'html'.DS.'common');
define('XPATH_TEMPLATE_FRONT', XPATH . DS . 'html');
define('XPATH_CACHE',      XPATH.DS.'cache');

function xload($path, $defaultdir = "", $file_ext = ".php") {
    $default_dir = (empty($defaultdir)) ? XPATH_SOURCES : $defaultdir;
    $path = str_replace(".",DS,$path);
    if (file_exists($default_dir.DS.$path.$file_ext)) {
        include($default_dir.DS.$path.$file_ext);
    }
}
?>