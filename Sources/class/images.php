<?php
class Images{
   protected $_table = 'images';
   
    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
   
    /*static function remove($image_id){
        //получаем такой имидж что бы проверить или он существует ну и получить объект
        $image = self::where('id','=',$image_id)->get();
        
        //сначала нужно проверить или он не находится в обложке
        $adv = Advertisement::where('cover','=',$image_id)->where('user_id','=',Auth::user()->id)->get();
        if(!$adv->isEmpty()){
            $adv[0]->cover = 0;
            $adv[0]->save();
        }
        
        //нужно подчистить файл        
        //з()
        $path = public_path('assets'.DIRECTORY_SEPARATOR.Auth::user()->id.DIRECTORY_SEPARATOR.$image[0]->adv_id.DIRECTORY_SEPARATOR);
        if(file_exists($path.$image[0]->name)) unlink($path.$image[0]->name);
        if(file_exists($path.'small'.$image[0]->name)) unlink($path.'small'.$image[0]->name);
        if(file_exists($path.'thumb'.$image[0]->name)) unlink($path.'thumb'.$image[0]->name);
        
        return $image[0]->delete();
    }
    
    static function setCover($image_id, $adv_id){
        //проверяем или есть такое изображение у данного пользователя
        $image = self::where('id' ,'=', $image_id)
                    ->where('user_id' ,'=', Auth::user()->id)
                    ->where('status' ,'=', 1)
                    ->limit('1')->get();
        
        if($image->isEmpty())return false;
        
        $advertisements = Advertisement::where('id','=',$adv_id)->where('user_id','=',Auth::user()->id)->get();
        $advertisements[0]->cover = $image_id;
        
        return $advertisements[0]->save();
    }*/

    /*public function addPhoto($id, $filename){
        $row = new self();
        $row->user_id = Auth::user()->id;
        $row->adv_id = $id;
        $row->name = $filename;
        $row->moderation = Auth::user()->vip ? 1:0;
        $row->status = 1;
        $row->date = date('Y-m-d H:i:s');
        if($row->save()){
            return true;
        }else{
            return false;
        }
    }*/
    
    /*public function updateAva($filename){
        Auth::user()->ava = $filename;
        Auth::user()->save();
    }*/
    
    public function unlinkOld($filename,$shortnes, $path=null){
        //if(!$filename) return;
        
        if(!$path) $path = $this->path;
        
        foreach($shortnes as $item){
            
            if(file_exists($path.DIRECTORY_SEPARATOR.$item.$filename)){
                
                unlink($path.DIRECTORY_SEPARATOR.$item.$filename);
            }
        }
    }
    
    public function initImage($data, $path){
        $this->data = $data;
        
        $this->validation = true;        
        $this->checkPath($path);  //создаем папку                
        $this->validateFileType($this->data);   //проверяем допустимость типа файла        
        var_dump($this->validation);
        $this->validateFileSize($this->data);   //проверяем на допустимый размер файла       
        var_dump($this->validation);
        $this->generateFileName();  //формируем название файла
        var_dump($this->validation);
        
        /*if($this->validation == true){
            $this->saveThumbs($data, array(array(65,44,'')));  //65 44
        }*/
    }
    
    public function saveThumbs($thumbs){
        foreach($thumbs as $item){
            $this->thumbnail($this->data['file']['tmp_name'],$this->path . DIRECTORY_SEPARATOR . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75); //пропорционально
        }
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
        
        //$this->params[] = array('w'=>$ret->width,'h'=>$ret->height,'type'=>$type);  //массив параметров
        
        $im1 = imagecreatetruecolor($ret->width,$ret->height);
        
        switch ($type){
            case 'image/jpeg':
                imagecopyresampled($im1, $im, 0, 0, 0, 0, $ret->width, $ret->height,$w,$h);
                imagejpeg($im1,$outfile,$quality);
                break;
            case 'image/png':
                imagealphablending($im1, FALSE);
                imagesavealpha($im1, TRUE);
                imagecopyresampled($im1,$im,0,0,0,0,$ret->width,$ret->height,$w,$h);
                $r = @imagepng($im1,$outfile);
                break;
            case 'image/gif':
                imagecopyresampled($im1, $im, 0, 0, 0, 0, $ret->width, $ret->height,$w,$h);
                $background = imagecolorallocate($im1, 0, 0, 0); 
                imagecolortransparent($im1, $background);                
                $r = @imagegif($im1,$outfile);
                break;
        }
        
        imagedestroy($im);
        imagedestroy($im1);
    }
    
    private function proportionallyChange($width,$height,$width2,$height2){
            $ret = new \stdClass();
            //$ret = new stdClass();
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
    
    public function makePassword($length=8){
        $salt            = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $makepass        = '';
        mt_srand(10000000*(double)microtime());
        for ($i = 0; $i < $length; $i++)
            $makepass .= $salt[mt_rand(0,61)];
        return $makepass;
    }
    
    public function move($from,$to){
        if(rename($from, $to)){
            return true;
        }
        return false;
    }

    public function saveOriginal($data){
        if(!$data) $data = $this->data;
            
        if(move_uploaded_file($data['file']['tmp_name'], $this->path . DIRECTORY_SEPARATOR . $this->filename)){
            return true;
        }
        return false;
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
    
    protected function validateFileSize($data){
        $filesize = 2 * 1024 * 1024;
        if($data['file']['size'] > $filesize){
            $this->validation = false;
            $this->error = 'Файл недопустимого размера';
            return false;
        }
    }
    
    protected function validateFileType($data){        
        
        switch($data['file']['type']){
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
    
    protected function generateFileName(){
        $this->filename = md5($this->makePassword(8).time()).'.'.$this->format;
    }
        
    /*protected function unlinkOld($filename, $shortnes, $path=null){
        if(!$filename) return;
        if(!$path) $path = $this->path;
        
        foreach($shortnes as $item){
            unlink($path.DS.$item.$filename);
        }
    }
    
    public function cropsize($infile,$outfile,$width = 80,$height = 106,$quality = 75){
        //$infile = $infile;     //путь к загруженному файлу
        //$outfile = $outfile;   //путь к файлу который в итоге получим
        
        $size = getimagesize($infile);
        
        $w = $size[0];
        $h = $size[1];
        $type = $size['mime']; //'image/jpeg'
        
        if($w < $h){
            $width_big = $w;
            $height_big = $w;
            $middle_x = 0;
            $middle_y = ($h/2) - ($width_big/2);
        }else{
            $width_big = $h;
            $height_big = $h;
            $middle_x = ($w/2) - ($width_big/2);
            $middle_y = 0;
        }
        
        switch($type){
            case 'image/jpeg': $im = imagecreatefromjpeg($infile); break;
        }
        
        $this->params[] = array('w'=>$width,'h'=>$height,'type'=>$type);  //массив параметров
        
        $im1 = imagecreatetruecolor($width,$height);
        imagecopyresampled($im1,$im,0,0,$middle_x,$middle_y,$width,$height,$width_big,$height_big);
        
        imagejpeg($im1,$outfile,$quality);
        imagedestroy($im);
        imagedestroy($im1);
    }
    
    
    public function getFileName($path){
        if(!@scandir($path)) mkdir($path);  //check if there exist directory of our user, and create if not    

        //generate name of file and move to our directory
        switch($_FILES[$this->field]["type"]){    // get file format
            case 'image/jpeg': $format = 'jpg';break;
            case 'image/gif': $format = 'gif';break;
            
            default: 
                $this->validation = false;
                $this->error = "Не правильный формат картинки";
                return false;
        }
            
        $this->filename = md5(makePassword(8).time()).'.'.$format;
        
        return $this->filename;
    }
    
    public function uploadeCropedSizeFile($path,$thumbs,$original = false){
        $this->filename = $this->getFileName($path);
        
        if($original == true){  move_uploaded_file($_FILES[$this->field]["tmp_name"], $path . DS . $this->filename); } //грузим орегинал
        
        foreach($thumbs as $item){
            $this->cropsize($_FILES[$this->field]["tmp_name"],$path . DS . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75);
        }
        
        return $this->filename;
    }
    
    public function uploadeFitSizeFile($path,$thumbs,$original = false){
        $this->filename = $this->getFileName($path);
        
        if($original == true){  //грузим орегинал
            move_uploaded_file($_FILES[$this->field]["tmp_name"], $path . DS . $this->filename);
        }
        
        foreach($thumbs as $item){
            $this->fitsize($_FILES[$this->field]["tmp_name"],$path . DS . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75);
        }
        
        return $this->filename;
    }
    
    public function uploadeFile($path,$thumbs,$original = false){
        if(!@scandir($path)) mkdir($path);  //check if there exist directory of our user, and create if not    

        //generate name of file and move to our directory                    
            switch($_FILES[$this->field]["type"]){    // get file format
                case 'image/jpeg': $format = 'jpg';break;
                case 'image/gif': $format = 'gif';break;
                default: 
                    $this->validation = false;
                    $this->error = "Не правильный формат картинки";
                    return false;
            }
            
        $this->filename = md5(makePassword(8).time()).'.'.$format;
        
        if($original == true){  //грузим орегинал
            move_uploaded_file($_FILES[$this->field]["tmp_name"], $path . DS . $this->filename);
        }
        
        foreach($thumbs as $item){
            $this->thumbnail($_FILES[$this->field]["tmp_name"],$path . DS . $item[2].$this->filename,$width = $item[0],$height = $item[1],$quality = 75);
        }
                    
        return $this->filename;
    }
    
   
    public function fitsize($infile,$outfile,$width = 80,$height = 106,$quality = 75){
        $infile = $infile;     //путь к загруженному файлу
        $outfile = $outfile;   //путь к файлу который в итоге получим
        
        $size = getimagesize($infile);
        $w  = $size[0];
        $h = $size[1];
        $type = $size['mime']; //'image/jpeg'
        
        switch($type){ 
            case 'image/jpeg': $im = imagecreatefromjpeg($infile); break;
        }
        
        //$ret = $this->proportionallyChange($w,$h,$width,$height);   //пропорционально меняем размер ширины и высоты
        
        $this->params[] = array('w'=>$width,'h'=>$height,'type'=>$type);  //массив параметров
        
        $im1 = imagecreatetruecolor($width,$height);
        imagecopyresampled($im1,$im,0,0,0,0,$width,$height,$w,$h);

        imagejpeg($im1,$outfile,$quality);
        imagedestroy($im);
        imagedestroy($im1);
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
    
    
    
    public function imagesValidation(){
        if(empty($_FILES[$this->field]["name"])){                        //if empty name
            $this->error = "Добавьте файл картинки";
            return false;
        }else if($_FILES[$this->field]["size"] > $this->m->config->filesize){           //if size of file is to larg                    

            $this->error = "Размер файла превышает три мегабайта";
            return false;
        }else if(!is_uploaded_file($_FILES[$this->field]["tmp_name"])){  //if not loaded for some reasone

            $this->error = "Ошибка загрузки файла";
            return false;
        }
        
        
        switch($_FILES[$this->field]["type"]){    // get file format
            case 'image/jpeg': break;
            //case 'image/gif': $format = 'gif';break;
            default: 
                $this->validation = false;
                $this->error = "Не правильный формат картинки";
                return false;
        }
        
        return true;
    }
    
    
      public function crop($path,$filename){
        list($width, $height, $type) = getimagesize($path.$filename);
      
        switch($type){
            case 1: $img = imagecreatefromgif($path.$filename); break;
            case 2: $img = imagecreatefromjpeg($path.$filename); break;
            case 3: $img = imagecreatefrompng($path.$filename); break;
        }
        
        $this->createCropedImage($img,100,100,'crop',$path,$filename);
    }
    
    public function createCropedImage($img,$w,$h,$prefix,$path,$filename){
        $img_o = imagecreatetruecolor($w, $h);
        imagecopyresampled($img_o, $img, 0, 0, $_POST['x'], $_POST['y'], $w, $h, $_POST['w'],$_POST['h']);
        imagejpeg($img_o,$path.$prefix.$filename,75);
        imagedestroy($img_o);
    }*/
}
?>
