<?php
    class questionsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        public function indexAction(){
            
        }
        
        public function question_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.questions');                
            $questions = new Questions($this->m);
            
            $id = (int)$_GET['id'];
            
            $data = $questions->getGiven($id);
                        
            //получаем список ответов для селекта
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        . " , `images`.`id` as image_id"
                        . " , `images`.`filename`"                    
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                        . " WHERE `answer_collections`.`question_id` = ".$id                        
                    );
            $data->answers = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
        
        public function delete_question_collectionAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);
            
            $id = (int)$_GET['id'];
            $question_id = (int)$_GET['question_id'];
            if($questionCollections->removeGiven($id,$question_id)){
                echo '{"status":"success"}';
            }else{                
                echo '{"status":"error"}';
            }
        }
        
        
        public function editAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true); 
            
            $id = (int)$_POST['id'];
            $row->value = strip_tags(trim($_POST['value']));
            $row->score = (int)$_POST['score'];
            $row->audio_id = (int)$_POST['audio_id'];
            
            $description = strip_tags(trim($_POST['audio_description']));
            
            $type = $_POST['type'];
            $lesson_id = (int)$_POST['lesson_id'];
            $answers  = $_POST['answers'];
            
            xload('class.admin.questions');
            $questions = new Questions($this->m);
            
            if($questions->updateMain($id,$row->value,$row->score,$row->audio_id)){
                if($type == 5 || $type == 6){
                    //обновляем описание аудиофайла    
                    $this->m->_db->setQuery(
                                "UPDATE `audios` SET `audios`.`description` = '".$description."'"
                                . " WHERE `audios`.`id` = ".$row->audio_id
                                . " LIMIT 1"
                            );
                    $this->m->_db->query();
                }
                
                switch($_POST['type']){
                    case 1:$questions->editNormal($answers,$id);break;
                    case 2:$questions->editImage($answers,$id);break;
                    case 3:$questions->editNormal($answers,$id);break;
                    case 4:$questions->editNormal($answers,$id);break;
                    case 5:$questions->editNormal($answers,$id);break;
                }

                //$questions->updateCorrect($id,$correct);

                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function addAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $_POST = json_decode(file_get_contents('php://input'), true); 
                
            //$id = (int)$_POST['id'];
            $row->value = strip_tags(trim($_POST['value']));
            $row->score = (int)$_POST['score'];
            $row->type = (int)$_POST['mode'];
            $row->audio_id = (int)$_POST['audio_id'];
            
            $description = strip_tags(trim($_POST['audio_description']));
            
            $lesson_id = (int)$_POST['lesson_id'];
            $answers  = $_POST['answers'];
            
            xload('class.admin.questions');
            $questions = new Questions($this->m);
            
            if($questions->addNew($row)){
                if($row->type == 5 || $row->type == 6){
                    //обновляем описание аудиофайла    
                    $this->m->_db->setQuery(
                                "UPDATE `audios` SET `audios`.`description` = '".$description."'"
                                . " WHERE `audios`.`id` = ".$row->audio_id
                                . " LIMIT 1"
                            );
                    $this->m->_db->query();
                }
                
                switch($_POST['mode']){
                    case 1:$questions->addNormal($answers,$row->id);break;
                    case 2:$questions->addImage($answers,$row->id);break;
                    case 3:$questions->addNormal($answers,$row->id);break;
                    case 4:$questions->addNormal($answers,$row->id);break;
                    case 5:$questions->addNormal($answers,$row->id);break;
                    case 6:$questions->addNormal($answers,$row->id);break;
                    //case 5:$questions->addAudio($answers,$row->id);break;
                    //case 5:$questions->addAudio($answers,$row->id);break;
                    
                }
                
                //закрепляем за уроком если есть лессон айди
                if($lesson_id){
                    $lesson = new stdClass();
                    $lesson->question_id = $row->id;
                    $lesson->lesson_id = $lesson_id;
                    $this->m->_db->insertObject('question_collections',$lesson);
                }

                echo '{"status":"success"}';
            }else{

                echo '{"status":"error"}';
            }
        }
        
        public function imagesAction(){
            $this->m->_db->setQuery(
                        "SELECT `images`.* "
                        . " , COUNT(`answers`.`id`) as answers"
                        . " FROM `images` "
                        . " LEFT JOIN `answers` ON `answers`.`image_id` = `images`.`id`"
                        . " WHERE `images`.`status` = 1"
                        . " GROUP BY `images`.`id`"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        public function publish_questionAction(){
            $this->disableTemplate();
            $this->disableView();

            xload('class.admin.questionCollections');
            $questionCollections = new QuestionCollections($this->m);

            $id = (int)$_GET['id'];                                
            $result= new stdClass();

            if($questionCollections->updatePublishing($id, $result)){
                echo '{"status":"success","result":"'.$result->published.'"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function loadeditaudioAction(){
            $this->loadaddaudioAction();
        }
        
        public function loadaddaudioAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $hash = md5(file_get_contents($_FILES['file']['tmp_name']));
                
                $this->m->_db->setQuery(
                            "SELECT `audios`.* "
                            . " FROM `audios`"
                            . " WHERE `audios`.`hash` = '".$hash."'"
                            . " AND `audios`.`status` = 1"
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($audio);
                
                if($audio){
                    $this->m->filename = $audio->filename;                    
                    $this->m->status = 'success';                    
                    $this->m->id = $audio->id;
                    return;
                }
                
                switch($_FILES['file']['type']){
                    case 'audio/mpeg': $this->format = 'mp3';break;
                    default:
                        $this->validation = false;
                        $this->error = "Не правильный формат аудио";
                        return false;
                }
                
                $filename = $hash.'.'.$this->format;
                
                move_uploaded_file($_FILES['file']['tmp_name'], $this->m->config->assets_path.DS.'audios' . DIRECTORY_SEPARATOR . $filename);
                
                $row->filename = $filename;
                $row->hash = $hash;
                $row->date = date("Y-m-d H:i:s");
                if($this->m->_db->insertObject('audios',$row,'id')){
                    $this->m->filename = $row->filename;
                    
                    $this->m->status = 'success';                    
                    $this->m->id = $row->id;
                }else{
                    
                    echo '{"status":"error"}';
                }
            }
        }
                
        public function loadeditimageAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
                
                $hash = md5(file_get_contents($_FILES['file']['tmp_name']));
                //проверяем Хеш
                $this->m->_db->setQuery(
                            "SELECT `images`.* "
                            . " FROM `images` "
                            . " WHERE `images`.`hash` = '".$hash."'"
                            . " AND `images`.`type` = 1"
                        );
                $this->m->_db->loadObject($image);
                
                $images->initImage($_FILES, $this->m->config->assets_path.DS.'images');
                if($image){
                    $this->m->filename = $image->filename;
                    $this->m->status = 'success';                    
                    $this->m->id = $image->id;
                }else{
                    if($images->validation == true){
                        $images->saveThumbs(array(array(200,200,'')));
                    }

                    if($images->validation == false){
                        $this->m->status = 'error';
                        $this->m->error = $images->error;
                    }else{
                        $this->m->filename = $images->filename;
                        $this->m->status = 'success';

                        $image = new stdClass();
                        $image->filename = $this->m->filename;
                        $image->hash = $hash;
                        $image->type = 1;
                        $image->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('images',$image,'id');
                        $this->m->id = $image->id;
                    }
                }
            }
        }
        
        public function loadaddimageAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);
                
                $hash = md5(file_get_contents($_FILES['file']['tmp_name']));
                //проверяем Хеш
                $this->m->_db->setQuery(
                            "SELECT `images`.* "
                            . " FROM `images` "
                            . " WHERE `images`.`hash` = '".$hash."'"
                            . " AND `images`.`type` = 1"
                        );
                $this->m->_db->loadObject($image);
                
                if($image){
                    $this->m->filename = $image->filename;
                    $this->m->status = 'success';                    
                    $this->m->id = $image->id;
                }else{
                    $images->initImage($_FILES, $this->m->config->assets_path.DS.'images');

                    if($images->validation == true){
                        $images->saveThumbs(array(array(200,200,'')));
                    }

                    if($images->validation == false){
                        $this->m->status = 'error';
                        $this->m->error = $images->error;
                    }else{
                        $this->m->filename = $images->filename;
                        $this->m->status = 'success';

                        $image = new stdClass();
                        $image->filename = $this->m->filename;
                        $image->hash = $hash;
                        $image->date = date("Y-m-d H:i:s");
                        $this->m->_db->insertObject('images',$image,'id');
                        $this->m->id = $image->id;
                    }
                }
            }
        }
        
        
        /*public function question_image_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            xload('class.admin.questions');                
            $questions = new Questions($this->m);
            
            $id = (int)$_GET['id'];
            $data = $questions->getGiven($id);
            
            //получаем список ответов для селекта
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        . " , `images`.`id` as image_id"
                        . " , `images`.`filename`"                    
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                        . " WHERE `answer_collections`.`question_id` = ".$id                        
                    );
            $data->answers = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }*/
        
       
        
        /*public function add_image_questionAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                xload('class.admin.questions');                
                $questions = new Questions($this->m);
                
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $id = (int)$_POST['id'];
                $row->value = strip_tags(trim($_POST['value']));
                $row->score = (int)$_POST['score'];
                $row->type = 2;
                $lesson_id = (int)$_POST['lesson_id'];
                $answers  = $_POST['answers'];
                
                if($id){        //EDIT
                    if($questions->updateMain($id,$row->value,$row->score)){
                        foreach($answers as $item){     //добавляем вопросы
                            
                            switch($item['act']){
                                case 'update':
                                        //нужно получить ансвет айди по колекшн айди
                                        $this->m->_db->setQuery(
                                                    "SELECT `answer_collections`.`answer_id` as id"
                                                    . " FROM `answer_collections`"
                                                    . " WHERE `answer_collections`.`id` = ".(int)$item['id']
                                                    . " LIMIT 1"
                                                );
                                        $answer_id = $this->m->_db->loadResult();                                                                
                                        if($answer_id){
                                            $this->m->_db->setQuery(
                                                    "UPDATE `answers` SET `answers`.`image_id` = '".$item['value']."'"
                                                    . " WHERE `answers`.`id` = ".(int)$answer_id
                                                    . " LIMIT 1"
                                                );
                                            $this->m->_db->query();
                                        }
                                        if($item['correct'])$correct = $item['id'];                                        
                                    break;
                                case 'insert':
                                        $answer = new stdClass();
                                        $answer->image_id = (int)$item['value'];
                                        $answer->date = date("Y-m-d H:i:s");
                                        $this->m->_db->insertObject('answers',$answer,'id');
                                        
                                        $collection = new stdClass();
                                        $collection->answer_id = $answer->id;
                                        $collection->question_id = $id;
                                        
                                        $this->m->_db->insertObject('answer_collections',$collection,'id');

                                        if($item['correct'])$correct = $collection->id;
                                    break;
                                case 'delete':
                                        $this->m->_db->setQuery(
                                                    "DELETE FROM `answer_collections` WHERE `answer_collections`.`id` = ".(int)$item['id']
                                                    . " LIMIT 1"
                                                );
                                        $this->m->_db->query();
                                        
                                        if($item['correct'])$correct = '';
                                    break;    
                                    
                            }
                        }
                        
                        $questions->updateCorrect($id, $correct);
                        
                        echo '{"status":"success"}';
                    }
                }else{
                    if($questions->addNew($row)){
                        foreach($answers as $item){     //добавляем вопросы
                            if($item['act'] == 'insert'){   //добавляем новый
                                $answer = new stdClass();
                                $answer->image_id = (int)$item['value'];
                                $answer->date = date("Y-m-d H:i:s");
                                $this->m->_db->insertObject('answers',$answer,'id');
                                
                                $collection = new stdClass();
                                $collection->answer_id = $answer->id;
                                $collection->question_id = $row->id;
                                
                                $this->m->_db->insertObject('answer_collections',$collection,'id');
                                
                                if($item['correct'])$correct = $collection->id;
                            }
                        }
                        
                        $questions->updateCorrect($id, $correct);
                        
                        //закрепляем за уроком если есть лессон айди
                        if($lesson_id){
                            $lesson = new stdClass();
                            $lesson->question_id = $row->id;
                            $lesson->lesson_id = $lesson_id;
                            $this->m->_db->insertObject('question_collections',$lesson);
                        }
                        
                        echo '{"status":"success"}';
                    }else{
                        
                        echo '{"status":"error"}';
                    }
                }
            }
        }*/
        
       /* public function questionsAction(){
            xload('class.admin.questions');
            $questions = new Questions($this->m);
                
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $id = (int)$_POST['id'];
                $row->value = strip_tags(trim($_POST['value']));
                $row->score = (int)$_POST['score'];
                $lesson_id = (int)$_POST['lesson_id'];
                $answers  = $_POST['answers'];
                                
                if($id){        //EDIT
                    if($questions->updateMain($id,$row->value,$row->score)){
                        foreach($answers as $item){     //добавляем вопросы
                            switch($item['act']){
                                case 'update':
                                        //нужно получить ансвет айди по колекшн айди
                                        $this->m->_db->setQuery(
                                                    "SELECT `answer_collections`.`answer_id` as id"
                                                    . " FROM `answer_collections`"
                                                    . " WHERE `answer_collections`.`id` = ".(int)$item['id']
                                                    . " LIMIT 1"
                                                );
                                        $answer_id = $this->m->_db->loadResult();
                                                                                
                                        if($answer_id){
                                            $this->m->_db->setQuery(
                                                    "UPDATE `answers` SET `answers`.`text` = '".$item['value']."'"
                                                    . " WHERE `answers`.`id` = ".(int)$answer_id
                                                    . " LIMIT 1"
                                                );
                                            $this->m->_db->query();
                                        }
                                        if($item['correct'])$correct = $item['id'];                                        
                                    break;
                                case 'insert':
                                        $answer = new stdClass();
                                        $answer->text = $item['value'];
                                        $answer->date = date("Y-m-d H:i:s");
                                        $this->m->_db->insertObject('answers',$answer,'id');
                                        
                                        $collection = new stdClass();
                                        $collection->answer_id = $answer->id;
                                        $collection->question_id = $id;
                                        $this->m->_db->insertObject('answer_collections',$collection,'id');
                                        
                                        if($item['correct'])$correct = $collection->id;
                                    break;
                                case 'delete':
                                        if(!$item['id']) break;
                                        $this->m->_db->setQuery(
                                                    "DELETE FROM `answer_collections` WHERE `answer_collections`.`id` = ".(int)$item['id']
                                                    . " LIMIT 1"
                                                );
                                        $this->m->_db->query();
                                        
                                    break;
                            }
                        }
                        
                        $questions->updateCorrect($id,$correct);
                        
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($questions->addNew($row)){
                        foreach($answers as $item){     //добавляем вопросы
                            if($item['act'] == 'insert'){   //добавляем новый
                                $answer = new stdClass();
                                $answer->text = $item['value'];
                                $answer->date = date("Y-m-d H:i:s");
                                $this->m->_db->insertObject('answers',$answer,'id');
                                
                                $collection = new stdClass();
                                $collection->answer_id = $answer->id;
                                $collection->question_id = $row->id;
                                
                                $this->m->_db->insertObject('answer_collections',$collection,'id');
                                
                                if($item['correct'])$correct = $collection->id;
                            }else if($item['act'] == 'select'){ //закрепляем существующий
                                $collection->answer_id = (int)$item['value'];
                                $collection->question_id = $row->id;
                                $this->m->_db->insertObject('answer_collections',$collection,'id');
                                
                                if($item['correct'])$correct = $collection->id;
                            }
                        }
                        
                        $questions->updateCorrect($row->id,$correct);
                        
                        //закрепляем за уроком если есть лессон айди
                        if($lesson_id){
                            $lesson = new stdClass();
                            $lesson->question_id = $row->id;
                            $lesson->lesson_id = $lesson_id;
                            $this->m->_db->insertObject('question_collections',$lesson);
                        }
                        
                        echo '{"status":"success"}';
                    }else{
                        
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->data = $questions->getData();
                
                $this->m->_db->setQuery(
                            "SELECT `answers`.`id` "
                             . " , `answers`.`text`"
                             . " FROM `answers` "
                             . " WHERE `answers`.`status` = 1"
                        );
                $this->m->answers = $this->m->_db->loadObjectList();
            }
        }*/
    }
?>