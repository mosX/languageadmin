<?php
    class lessonsController extends Model {
        public function init(){
            $this->m->addJS('jquery-ui.min');
            $this->m->addCSS('jquery-ui.min');
        }
        
        
        
        public function indexAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true);
                
                $id = (int)$_POST['id'];
                $row->name = strip_tags(trim($_POST['name']));
                $row->description = strip_tags(trim($_POST['description']));
                $row->show_answers = $_POST['show_answers'] ? 1 : 0;
                
                $row->terms = serialize($_POST['terms']);
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `lessons` SET `lessons`.`name` = '".$row->name."'"
                                . " , `lessons`.`show_answers` = '".(int)$row->show_answers."'"
                                . " , `lessons`.`description` = '".$row->description."'"
                                . ($row->terms ? " , `lessons`.`terms` = '".$row->terms."'" : '')
                                . " WHERE `lessons`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('lessons',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `lessons`.* "
                            . " FROM `lessons`"
                            . " WHERE `lessons`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function imagesAction(){
            $this->m->_db->setQuery(
                        "SELECT `images`.* "
                        . " FROM `images` "
                        . " WHERE `images`.`status` = 1"
                    );
            $this->m->data = $this->m->_db->loadObjectList();            
        }
        
        public function publishAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                
                $id = (int)$_GET['id'];
                if(!$id){
                    echo '{"status":"error"}';
                    return false;
                }
                
                //получаем єтот урок что бы узнать текущее значение паблишеда
                $this->m->_db->setQuery(
                            "SELECT `lessons`.* "
                            . " FROM `lessons` "
                            . " WHERE `lessons`.`id` = ".(int)$id 
                            . " LIMIT 1"
                        );
                $this->m->_db->loadObject($lesson);
                
                if(!$lesson){
                    echo '{"status":"error"}';
                    return false;
                }
                
                $published = $lesson->published ? 0: 1;
                
                $this->m->_db->setQuery(
                            "UPDATE `lessons` SET `lessons`.`published` = ".$published
                            . " WHERE `lessons`.`id` = ".$id
                            . " LIMIT 1"
                        );
                if($this->m->_db->query()){
                    echo '{"status":"success","result":"'.$published.'"}';
                }else{
                    echo '{"status":"error"}';
                }
            }
        }
        
        public function delete_question_collectionAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "DELETE FROM `question_collections` "
                        . " WHERE `question_collections`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{                
                echo '{"status":"error"}';
            }
        }
        
        public function delete_lessonAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            if(!$id) return false;
            
            $this->m->_db->setQuery(
                        "UPDATE `lessons` SET `lessons`.`status` = 0"
                        . " WHERE `lessons`.`id` = ".$id
                        . " LIMIT 1"
                    );
            if($this->m->_db->query()){
                echo '{"status":"success"}';
            }else{
                echo '{"status":"error"}';
            }
        }
        
        public function loadeditimageAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
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
                    $image->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('images',$image,'id');
                    $this->m->id = $image->id;
                }
            }
        }
        
        public function loadaddimageAction(){
            $this->disableTemplate();
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                xload('class.images');
                $images = new Images($this->m);                
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
                    $image->date = date("Y-m-d H:i:s");
                    $this->m->_db->insertObject('images',$image,'id');
                    $this->m->id = $image->id;
                }
            }
        }
        
        public function testing_checkAction(){
            $this->m->_db->setQuery(
                        "SELECT `testing_results`.* "
                        . " , `lessons`.`name`"
                        . " FROM `testing_results`"
                        . " LEFT JOIN `lessons` ON `lessons`.`id` = `testing_results`.`lesson_id`"
                        . " WHERE `testing_results`.`id` = ".(int)$this->m->_path[2]
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($result);
            
            $result->results = unserialize($result->results);
            
            //получаем вопросы
            $this->m->_db->setQuery(
                        "SELECT `question_collections`.* "
                        . " , `questions`.`value`"
                        . " , `questions`.`correct`"
                        . " , `questions`.`type`"
                        . " FROM `question_collections`"
                        . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                        . " WHERE `question_collections`.`lesson_id` = ".(int)$result->lesson_id
                    );
            
            $data = $this->m->_db->loadObjectList('question_id');
            //$this->m->testing = $result;
            
            foreach($data as $item)$ids[] = $item->question_id;
            
            //получаем ответы
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        . " , `images`.`filename`"
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " LEFT JOIN `images` ON `images`.`id` = `answers`.`image_id`"
                        . " WHERE `answer_collections`.`question_id` IN (".  implode(',', $ids).")"
                    );
            $answers = $this->m->_db->loadObjectList();
            
            foreach($answers as $item){
                if(is_array($result->results[$item->question_id])){
                    if($item->id == $result->results[$item->question_id]['answer']){
                        $data[$item->question_id]->time = $result->results[$item->question_id]['time'];
                        $item->selected = 'true';
                    }
                }else{
                    if($item->id == $result->results[$item->question_id]){
                         $item->selected = 'true';
                    }
                }
                
                if($data[$item->question_id]->correct == $item->id){
                    $item->correct = 'true';
                }
                
                $data[$item->question_id]->answers[] = $item;
            }
            $this->m->data = $data;
            
            //$this->m->testing = $data;
        }
        
        public function resultsAction(){
            $this->m->_db->setQuery(
                        "SELECT `testing_results`.* "
                        . " , `lessons`.`name`"
                        . " FROM `testing_results`"
                        . " LEFT JOIN `lessons` ON `lessons`.`id` = `testing_results`.`lesson_id`"
                        . " WHERE `testing_results`.`status` = 1"
                        . " ORDER BY `id` DESC"
                    );
            $this->m->data = $this->m->_db->loadObjectList();
        }
        
        public function question_image_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `questions`.* "
                        . " FROM `questions` "
                        . " WHERE `questions`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
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
        
        public function question_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `questions`.* "
                        . " FROM `questions` "
                        . " WHERE `questions`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            //получаем список ответов для селекта
            $this->m->_db->setQuery(
                        "SELECT `answer_collections`.* "
                        . " , `answers`.`text`"
                        
                        . " FROM `answer_collections` "
                        . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                        . " WHERE `answer_collections`.`question_id` = ".$id                        
                    );
            $data->answers = $this->m->_db->loadObjectList();
            
            echo json_encode($data);
        }
        
        public function answers_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `answers`.* "
                        . " FROM `answers` "
                        . " WHERE `answers`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }
        
        public function lesson_dataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `lessons`.* "
                        . " FROM `lessons` "
                        . " WHERE `lessons`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            $data->terms = unserialize($data->terms);
            
            echo json_encode($data);
        }
        
        public function question_collectionsAction(){
            $this->m->lesson_id = $this->m->_path[2];
            
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                //UPD использовалось для добавления вопроса из селекта по одному
                $row->question_id = (int)$_POST['question'];
                $row->lesson_id = (int)$this->m->_path[2];
                
                //проверяем или не было добавлено ранее
                $this->m->_db->setQuery(
                            "SELECT `question_collections`.`id` "
                            . " FROM `question_collections` "
                            . " WHERE `question_collections`.`question_id` = ".$row->question_id
                            . " AND `question_collections`.`lesson_id` = ".$row->lesson_id
                            . " LIMIT 1"                        
                        );
                $check = $this->m->_db->loadResult();
                
                if($check){
                    echo '{"status":"error"}';
                    return false;
                }
                    
                if($this->m->_db->insertObject('question_collections',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
            }else{
                //получаем все вопросы для селекта
                $this->m->_db->setQuery(
                            "SELECT `questions`.* "
                             . " FROM `questions` "
                            . " WHERE `questions`.`status` = 1"
                            . " ORDER BY `id` DESC"
                        );
                $this->m->list = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `question_collections`.* "
                            . " , `questions`.`value`"
                            . " , `questions`.`correct`"
                            . " , `questions`.`score`"
                            . " , `questions`.`type`"
                            . " , `answers`.`text` as answer"
                            . " FROM `question_collections`"
                            . " LEFT JOIN `questions` ON `questions`.`id` = `question_collections`.`question_id`"
                            . " LEFT JOIN `answer_collections` ON `answer_collections`.`id` = `questions`.`correct`"
                            . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                            . " WHERE `question_collections`.`lesson_id` = ".(int)$this->m->_path[2]
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function answer_collectionsAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $row->answer_id = (int)$_POST['answer'];
                $row->question_id = (int)$this->m->_path[2];
                
                //проверяем или не было добавлено ранее
                $this->m->_db->setQuery(
                            "SELECT `answer_collections`.`id` "
                            . " FROM `answer_collections` "
                            
                            . " WHERE `answer_collections`.`answer_id` = ".$row->answer_id
                            . " AND `answer_collections`.`question_id` = ".$row->question_id
                            . " LIMIT 1"                        
                        );
                $check = $this->m->_db->loadResult();
                
                if($check){
                    echo '{"status":"error"}';
                    return false;
                }
                    
                if($this->m->_db->insertObject('answer_collections',$row)){
                    echo '{"status":"success"}';
                }else{
                    echo '{"status":"error"}';
                }
                
            }else{
                //получаем все вопросы для селекта
                $this->m->_db->setQuery(
                            "SELECT `answers`.* "
                             . " FROM `answers` "
                            . " WHERE `answers`.`status` = 1"
                        );
                $this->m->list = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `answer_collections`.* "
                            . " , `answers`.`text`"
                             . " FROM `answer_collections`"
                             . " LEFT JOIN `answers` ON `answers`.`id` = `answer_collections`.`answer_id`"
                             . " WHERE `answer_collections`.`question_id` = ".(int)$this->m->_path[2]
                             //. " AND `answer_collections`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();                
            }
        }
        
        public function answersAction(){
             if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $id = (int)$_POST['id'];
                $row->text = strip_tags(trim($_POST['text']));
                $row->date  = date("Y-m-d H:i:s");
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `answers` SET `answers`.`text` = '".$row->text."'"
                                . " WHERE `answers`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    if($this->m->_db->query()){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('answers',$row)){
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }
            }else{
                $this->m->_db->setQuery(
                            "SELECT `answers`.* "
                            . " FROM `answers` "
                            . " WHERE `answers`.`status` = 1"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
            }
        }
        
        public function add_image_questionAction(){
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $this->disableTemplate();
                $this->disableView();
                $_POST = json_decode(file_get_contents('php://input'), true); 
                
                $id = (int)$_POST['id'];
                $row->value = strip_tags(trim($_POST['value']));
                $row->score = (int)$_POST['score'];
                $row->type = 2;
                $lesson_id = (int)$_POST['lesson_id'];
                $answers  = $_POST['answers'];
                
                if($id){        //EDIT
                    $this->m->_db->setQuery(
                                "UPDATE `questions` "
                                . " SET `questions`.`value` = ".$this->m->_db->Quote($row->value)
                                . " , `questions`.`score` = '".$row->score."'"
                                . " WHERE `questions`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    
                    if($this->m->_db->query()){
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
                            }
                            
                            echo '{"status":"success"}';
                        }
                    }
                }else{
                    if($this->m->_db->insertObject('questions',$row,'id')){
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
                        
                        //добавляем правильный ответ если он есть
                        if($correct){
                            $this->m->_db->setQuery(
                                        "UPDATE `questions` SET `questions`.`correct` = ".(int)$correct
                                        . " WHERE `questions`.`id` = ".$row->id
                                        . " LIMIT 1"
                                    );
                            $this->m->_db->query();
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
            }
        }
        
        public function questionsAction(){
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
                    //$row->correct = strip_tags(trim($_POST['correct']));
                    
                    $this->m->_db->setQuery(
                                "UPDATE `questions` "
                                . " SET `questions`.`value` = ".$this->m->_db->Quote($row->value)
                                . " , `questions`.`score` = '".$row->score."'"
                                . " WHERE `questions`.`id` = ".(int)$id
                                . " LIMIT 1"
                            );
                    
                    if($this->m->_db->query()){
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
                                case 'select':
                                        $collection->answer_id = (int)$item['value'];
                                        $collection->question_id = $id;
                                        $this->m->_db->insertObject('answer_collections',$collection,'id');

                                        if($item['correct'])$correct = $collection->id;
                                    break;
                            }
                        }
                        
                        //добавляем правильный ответ если он есть 
                        if($correct){   //$correct єто айдишник в ансвер коллектионс
                            $this->m->_db->setQuery(
                                        "UPDATE `questions` SET `questions`.`correct` = ".(int)$correct
                                        . " WHERE `questions`.`id` = ".$id
                                        . " LIMIT 1"
                                    );
                            $this->m->_db->query();                            
                        }
                        
                        echo '{"status":"success"}';
                    }else{
                        echo '{"status":"error"}';
                    }
                }else{          //ADD
                    if($this->m->_db->insertObject('questions',$row,'id')){
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
                        
                        //добавляем правильный ответ если он есть
                        if($correct){
                            $this->m->_db->setQuery(
                                        "UPDATE `questions` SET `questions`.`correct` = ".(int)$correct
                                        . " WHERE `questions`.`id` = ".$row->id
                                        . " LIMIT 1"
                                    );
                            $this->m->_db->query();
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
            }else{
                $this->m->_db->setQuery(
                            "SELECT `questions`.* "
                            . " , COUNT(`answer_collections`.`id`) as answers"
                            . " FROM `questions` "
                            . " LEFT JOIN `answer_collections` ON `answer_collections`.`question_id` = `questions`.`id`"
                            . " WHERE `questions`.`status` = 1"
                            . " GROUP by `questions`.`id`"
                            . " ORDER BY `id` DESC"
                        );
                $this->m->data = $this->m->_db->loadObjectList();
                
                $this->m->_db->setQuery(
                            "SELECT `answers`.`id` "
                             . " , `answers`.`text`"
                             . " FROM `answers` "
                             . " WHERE `answers`.`status` = 1"
                        );
                $this->m->answers = $this->m->_db->loadObjectList();
            }
        }
        
       /* public function getdataAction(){
            $this->disableTemplate();
            $this->disableView();
            
            $id = (int)$_GET['id'];
            
            $this->m->_db->setQuery(
                        "SELECT `lessons`.* "
                        . " FROM `lessons` "
                        . " WHERE `lessons`.`id` = ".$id
                        . " LIMIT 1"
                    );
            $this->m->_db->loadObject($data);
            
            echo json_encode($data);
        }*/
        
        
    }
?>