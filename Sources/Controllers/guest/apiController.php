<?php
    class apiController extends Model {
        public function init(){
            $this->disableTemplate();
            $this->disableView();
        }
        
        public function indexAction(){
            
        }
        
        public function nimbleAction(){
            // Log entire incoming request to see what we have in it.
            /*$fp = fopen('/var/tmp/request.log', 'w');
            fwrite($fp, $HTTP_RAW_POST_DATA);
            fclose($fp);*/
            
            //file_put_contents(XPATH.DS.'log.txt', $HTTP_RAW_POST_DATA);
            file_put_contents(XPATH.DS.'log.txt', $HTTP_RAW_POST_DATA,FILE_APPEND);
                        
            // Use this object for accessing each viewer's ID, IP and stream name.
            $sync_data = json_decode($HTTP_RAW_POST_DATA);
            // Return IDs of clients which needs to be denied. Their IDs are 1 and 2 here.
            // Those viewers will be disconnected immediatelly and will not be allowed to connect anymore.
            
            
            /*header('Content-Type: application/json');
            $arr = array('DenyList' => array('ID' => array(1, 2)));
            echo json_encode($arr);*/
        }
    }
?>