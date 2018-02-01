<?php
class Images extends Model_App{
   protected $_table = 'images';
   public function __construct() {
        $this->validation = true;
        parent::__construct();
   } 
   
   protected function unlinkOld($filename, $shortnes, $path=null){
       
        if(!$filename) return;
        
        if(!$path) $path = $this->path;
        
        
        foreach($shortnes as $item){
            unlink($path.DS.$item.$filename);
        }
    }
   
    protected function initImage($data, $path){
        $this->checkPath($path);  //создаем папку
        $this->validateFileType($data);   //проверяем допустимость типа файла
        $this->validateFileSize($data);   //проверяем на допустимый размер файла
        $this->generateFileName();  //формируем название файла
        
        if($this->validation == true){
            
            $this->saveThumbs($data, array(array(225,225,'thumb'),array(70,70,'small')));
        }
    }
    
    protected function checkPath($path){
        if(!@scandir($path)){
            if(!mkdir($path)){
                $this->validation = false;
                $this->error = 'Данный путь не доступен';
                return false;
            }
        }
        $this->path = $path;
    }
    
    protected function validateFileType($data){
        switch($data["type"]){
            case 'image/jpeg': $this->format = 'jpg';break;
            case 'image/gif': $this->format = 'gif';break;
            case 'image/png': $this->format = 'png';break;
            default:
                $this->validation = false;
                $this->error = "Не правильный формат картинки";
                return false;
        }
        
        return $this->format;
    }
    
    protected function validateFileSize($data){
        
        if($_FILES["file"]["size"] > $this->app->config->filesize){
            
            $this->validation = false;
            $this->error = 'Файл недопустимого размера';
            
            return false;
        }
    }
    
    protected function generateFileName(){
        $this->filename = md5(makePassword(8).time()).'.'.$this->format;
    }
    
    protected function saveThumbs($data,$thumbs){
        foreach($thumbs as $item){
            //$this->cropsize($data["tmp_name"],$this->path . DS . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75);    //обрезанный 
            $this->thumbnail($data["tmp_name"],$this->path . DS . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75); //пропорционально
        }
    }
    
    protected function saveOriginal($data){
        if(move_uploaded_file($data["tmp_name"], $this->path . DS . $this->filename)){
            return true;
        }
        return false;
    }
    
    public function thumbnail($infile,$outfile,$width = 80,$height = 106,$quality = 75){
        $infile = $infile;     //путь к загруженному файлу
        $outfile = $outfile;   //путь к файлу который в итоге получим
        
        $size = getimagesize($infile);
        $w  = $size[0];
        $h = $size[1];
        $type = $size['mime']; //'image/jpeg'
        
        
        switch($type){ 
            case 'image/jpeg': $im = imagecreatefromjpeg($infile); break;
            case 'image/gif': $im = imagecreatefromgif($infile); break;
            case 'image/png': $im = imagecreatefrompng($infile); break;
        }
        
        $ret = $this->proportionallyChange($w,$h,$width,$height);   //пропорционально меняем размер ширины и высоты
        
        $this->params[] = array('w'=>$ret->width,'h'=>$ret->height,'type'=>$type);  //массив параметров
        
        $im1 = imagecreatetruecolor($ret->width,$ret->height);
        imagecopyresampled($im1,$im,0,0,0,0,$ret->width,$ret->height,$w,$h);

        imagejpeg($im1,$outfile,$quality);
        imagedestroy($im);
        imagedestroy($im1);
    }
    
    private function proportionallyChange($width,$height,$width2,$height2){
            $ret = new stdClass();
            $coef = $height/$height2;
            
            $ret->height = ceil($height/$coef);
            $ret->width = ceil($width/$coef);
                
            if($ret->width > $width2){
                $coef = $ret->width/$width2;
            
                $ret->height = ceil($ret->height/$coef);
                $ret->width = ceil($ret->width/$coef);
            }
        
        return $ret;
    }
   
}
?>
