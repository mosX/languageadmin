<?php
class Users extends DBTable{
    protected $_table = 'users';

    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;        
    }
    
  
   
    /*public function getUnActive(){
        $email_filter = trim($_GET['email']);
        $id_filter = (int)$_GET['id'];
        $partner_filter = trim($_GET['partner']);
        $lastname_filter = trim($_GET['lastname']);
        $firstname_filter = trim($_GET['firstname']);
        //$sort = (int)($_GET['sort']);

        $phone = trim(strip_tags($_GET['phone']));
        $date_from = $_GET['date_from'] ? date('Y-m-d 00:00:00',strtotime($_GET['date_from'])) : null;
        $date_to = $_GET['date_to'] ? date('Y-m-d 23:59:59',strtotime($_GET['date_to'])) : null;

        $sub_page_filter = strip_tags($this->m->_path[2]);  //фильтр подраздела

        $sql =  ($email_filter ? " AND `users`.`email` LIKE '%".$email_filter."%' " : "")
                . ($id_filter? " AND `accounts`.`user_id` = ".$id_filter : '')
                . ((int)$partner_filter ? "  AND  `users`.`partner` = '".$partner_filter."'" : "")
                //. ($partner_filter && (int)$partner_filter == 0 ? "  AND  `partners`.`username` LIKE '".$partner_filter."'" : "")

                . ($phone ? " AND `users`.`phone` LIKE '%".$phone."%'" : '')

                . ($lastname_filter ? " AND `users`.`lastname` LIKE '%".$lastname_filter."%'" : '')
                . ($firstname_filter ? " AND `users`.`firstname` LIKE '%".$firstname_filter."%'" : '')

                . ($date_from ? " AND `users`.`date` >= '".$date_from."'" : "")
                . ($date_to ? " AND `users`.`date` < '".$date_to."'" : "")

                . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).") "
        
                . " AND `users`.`last_login` = '0000-00-00 00:00:00'";

        //Count Elements
        $this->m->_db->setQuery(
            "SELECT COUNT(*) as cnt "
            . " FROM `accounts`"
            . " LEFT JOIN `users` ON `users`.`id` = `accounts`.`user_id` "
            . ( $partner_filter && (int)$partner_filter == 0 ? " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`" : "" )           
            . " WHERE 1 "
            . $sql
            );
        $total = $this->m->_db->loadResult();

        $xNav = new xNav("/stats/gamers/".($sub_page_filter? $sub_page_filter.'/':'' ), $total, "GET");
        $xNav->limit = 20;
        $this->m->pagesNav = $xNav->showPages();

        //SELECT ELEMENTS
        $this->m->_db->setQuery(
                "SELECT `accounts`.`id` as account_id,`accounts`.`balance`,`accounts`.`deposit_sum`,`accounts`.`withdraw_sum`,`accounts`.`gain` "
                . " , `users`.* "
                . " , (SELECT (100/SUM(`user_bonus`.`total_bet`)*SUM(`user_bonus`.`bet`)) FROM `user_bonus` WHERE `user_bonus`.`status` = 1 AND `user_bonus`.`account_id` = `accounts`.`id`) as bonus"
                . " , `country`.`code`"
                . " , `country`.`name_ru`"
                . " , `partners`.`username` as partner_name"
                . " , `afftracks`.`afftracker`"
                . " , `x_session`.`session_id`"
                . " FROM `accounts`"
                . " LEFT JOIN `users` ON `users`.`id` = `accounts`.`user_id` "
                . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
                . " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`"
                . " LEFT JOIN `afftracks` ON `users`.`afftrack` = `afftracks`.`id`"
                //. ( $sub_page_filter == 'balanceonline' || $sub_page_filter == 'demobalanceonline' ? " LEFT JOIN `x_session` ON `x_session`.`userid` = `users`.`id`" : "" )
                . " LEFT JOIN `x_session` ON `x_session`.`userid` = `users`.`id` AND `x_session`.`gid` <= 2 "
                . " WHERE 1"
                . $sql
                . " GROUP BY `accounts`.`id` "
                . (!$sort ? " ORDER BY `accounts`.`id` DESC":'')
                . ($sort == 1 ? " ORDER BY `accounts`.`balance` DESC":'')
                //. ($sort == 2 ? " ORDER BY `".$this->_table."`.`funbalance` DESC":'')
                . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
            );

        $this->data = $this->m->_db->loadObjectList('account_id');
        
        return $this->data;
    }*/

    public function addNewAdvCost(){
        if(!$_POST['amount']){
            $this->m->error->amount = "Не введена сумма";
            //echo '{"status":"error","message":"Не введена сумма"}';
            return;
        }
        
        $amount = (int)((float)(str_replace(array(',',' '),array('.',''),$_POST['amount']))*100);

        $row->user_id = $this->m->_user->id;
        $row->costs = $amount;
        $row->partner_id = (int)$_POST['partner'];
        $row->create_date = date("Y-m-d H:i:s",strtotime($_POST['date']));
        $row->modified = date('Y-m-d H:i:s');
        
        if($this->m->_db->insertObject("adv_costs",$row,"id")){
            return true;
        }else{
            return false;
        }
    }
    
    public function editAdvCosts($data){
        if(!$data){
            echo '{"status":"error","message":"Такие данныне не были найдены"}';
            return;
        }
        
        if(!$_POST['amount']){
            echo '{"status":"error","message":"Не введена сумма"}';
            return;
        }
        
        $amount = (int)((float)(str_replace(array(',',' '),array('.',''),$_POST['amount']))*100);

        $row->costs = $amount;
        $row->partner_id = $_POST['partner'];
        $row->create_date = $_POST['date'];
        $row->id = $_POST['id'];
        $row->modified = date('Y-m-d H:i:s');
        
        if($this->m->_db->updateObject("adv_costs",$row,"id")){
            echo '{"status":"success"}';
        }
    }
    
    public function getAdvCostEditData($id){
         $this->m->_db->setQuery(
            "SELECT `adv_costs`.`id`"
                 . " ,`adv_costs`.`partner_id`"
                 . " ,`adv_costs`.`costs`"
                 . " ,`adv_costs`.`create_date`"
                 . " ,`adv_costs`.`modified` "
                . " FROM `adv_costs` "
                . " WHERE `adv_costs`.`status` = 1 "
                . " AND `adv_costs`.`user_id` = '".(int)$this->m->_user->id."'"
                . " AND `adv_costs`.`id` = '".(int)$id."'"
                . " AND `adv_costs`.`partner_id` IN (" . implode(',', $this->m->partner_ids) . ")"
        );
        $this->m->_db->loadObject($data);
        
        return $data;
    }
    
    public function advCosts(){
//        (int)$_GET["filter_partner"] ? $this->m->config->partners = array((int)$_GET["filter_partner"]):null;
        if((int)$_GET["partner"]){
               $this->m->partner = (int)$_GET["partner"];
        }

        $this->m->from = $_GET['filter_from'] ?  $_GET["filter_from"] : date('Y-m-01 00:00:00');
        $this->m->to = $_GET['filter_to'] ? $_GET["filter_to"] : date('Y-m-d H:i:s');

        $amount = (int)((float)(str_replace(array(',',' '),array('.',''),$_POST['amount']))*100);
        
        $this->m->_db->setQuery(
                "SELECT `adv_costs`.`id`"
                    . " ,`adv_costs`.`partner_id`"
                    . " ,`adv_costs`.`costs`"
                    . " ,`adv_costs`.`create_date`"
                    . " ,`adv_costs`.`modified` "
                    . " , `partners`.`username`"
                    . " FROM `adv_costs` "
                    . " LEFT JOIN `partners` ON `adv_costs`.`partner_id` = `partners`.`id`"
                    . " WHERE `adv_costs`.`status` = 1 "
                   . ($this->m->partner ? " AND `adv_costs`.`partner_id`  = ".$this->m->partner :'')
                   . ($this->m->_permission == 'super' ? "AND `adv_costs`.`user_id` = ".$this->m->_user->id : '')
                    . " AND `adv_costs`.`create_date` >= '".date('Y-m-d 00:00:00',strtotime($this->m->from))."'"
                    . " AND `adv_costs`.`create_date` <= '".date('Y-m-d 23:59:59',strtotime($this->m->to))."'"
//                    . " AND `adv_costs`.`partner_id` IN (" . implode(',', $this->m->partner_ids) . ")"
                    . " ORDER by `id` DESC"
            );

            $data = $this->m->_db->loadObjectList();

            return $data;
    }
        

    public function report(){
        $partner = $_GET['partner'];
/*        $this->start_year = (int)getParam($_GET, "start_year", date("Y"));
        $this->start_month = (int)getParam($_GET, "start_month", date("m"));
        $this->start_day = (int)getParam($_GET, "start_day", 1);

        $this->end_year = (int)getParam($_GET, "end_year", date("Y"));
        $this->end_month = (int)getParam($_GET, "end_month", date("m"));
        $this->end_day = (int)getParam($_GET, "end_day", date("d"));*/

        $this->m->from = $_GET['from'] ? date("Y-m-d 00:00:00",strtotime($_GET['from'])): date("Y-m-01 00:00:00");
        $this->m->to = $_GET['to'] ? date("Y-m-d 23:59:59",strtotime($_GET['to'])): date("Y-m-d 23:59:59");

        $this->report  = array('nrc'=>array(),'ndc'=>array(),'tdc'=>array(),'nrw'=>array(),'nww'=>array());
        $this->m->_db->setQuery(
            " SELECT `users`.`id`,`users`.`date` "
            //. " , (SELECT SUM(`deposits`.`amount`) as sum FROM `deposits` WHERE `deposits`.`status` = 1 AND `deposits`.`user_id` = `users`.`id` AND `deposits`.`fake` = 0) as deposit_sum"
            . " FROM `users` "
            //. " WHERE `partner` = " . (int)$this->m->_user->id
            //. " LEFT JOIN `deposits` ON `deposits`.`user_id` = `users`.`id`"
            . " WHERE 1"
            . " AND `users`.`gid` = 1"
            . ($partner ? " AND `users`.`partner` = ".$partner : " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")")
            . " AND `users`.`date` >= '".$this->m->from."'"
            . " AND `users`.`date` <= '".$this->m->to."'"
            );
        $this->nrc = $this->m->_db->loadObjectList('date');

        $this->m->_db->setQuery(
              " SELECT `deposits`.`amount`, `users`.`date` "
            . " FROM `deposits` "
            . " LEFT JOIN `users` ON `users`.`id` = `deposits`.`user_id` "
            . " WHERE 1"
            . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")"
            . " AND `deposits`.`status` = 1"
            . " AND `deposits`.`fake` = 0"
            . " AND `users`.`date` >= '".$this->m->from."'"
            . " AND `users`.`date` <= '".$this->m->to."'"
            );
        $this->tdc = $this->m->_db->loadObjectList();

        $this->m->_db->setQuery(
//              " SELECT DISTINCT `deposits`.`user_id`,`deposits`.`date` "
              " SELECT `deposits`.`user_id`, `deposits`.`date`, `deposits`.`amount`, `deposits`.`fee` "
            . " FROM `deposits` "
            . " LEFT JOIN `users` ON `users`.`id` = `deposits`.`user_id` "
//            . " WHERE `deposits`.`partner` = " . (int)$this->m->_user->id
            . " WHERE 1"
            . ($partner ? " AND `deposits`.`partner` = ".$partner : '')
            . " AND `deposits`.`date` >= '".$this->m->from."'"
            . " AND `deposits`.`date` <= '".$this->m->to."'" 
            . " AND `deposits`.`status` = 1"
            . " AND `deposits`.`fake` = 0"
            . " AND `users`.`gid` = 1"
//            . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")"
            . ($partner ? " AND `users`.`partner` = ".$partner : " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")")
            );
        $this->ndc = $this->m->_db->loadObjectList('date');

            
        //получаем работников зареганных за данный период
        $this->m->_db->setQuery(
              " SELECT `users`.`id`, `users`.`date` "
            . " FROM `users` "
            . " WHERE 1"
            . " AND `users`.`date` >= '".$this->m->from."'" 
            . " AND `users`.`date` <= '".$this->m->to."'" 
            //. " AND `users`.`status` = 1"
            . " AND `users`.`gid` = 2"
//            . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")"
            . ($partner ? " AND `users`.`partner` = ".$partner : " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")")
            );
        $this->nrw = $this->m->_db->loadObjectList('date');

        //получаем работников у которых есть выводы
        $this->m->_db->setQuery(
              " SELECT `users`.`id`, `withdraws`.`adddate` "
            . " FROM `users` "
            . " LEFT JOIN `withdraws` ON `users`.`id` = `withdraws`.`user_id` "
            . " WHERE 1"
            //. " AND `users`.`date` >= " . $this->m->_db->Quote($this->start_year . "-" . $this->start_month . "-01 00:00:00")
            //. " AND `users`.`date` <= " . $this->m->_db->Quote($this->end_year . "-" . $this->end_month . "-".$this->end_day." 23:59:59")
            . " AND `withdraws`.`adddate` >= '".$this->m->from."'" 
            . " AND `withdraws`.`adddate` <= '".$this->m->to."'"
            //. " AND `users`.`status` = 1"
            . " AND `users`.`gid` = 2"
//            . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")"
            . ($partner ? " AND `users`.`partner` = ".$partner : " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).")")
            . " AND `withdraws`.`result` != 'cancel'"
            . " AND `withdraws`.`fake` = 1"
            );
        $this->nww = $this->m->_db->loadObjectList('adddate');

        if (date("Y",strtotime($this->m->from)) == date("Y",strtotime($this->m->to)) && date("m",strtotime($this->m->from)) == date("m",strtotime($this->m->from))) {
            $period = 'd';
        } elseif (date("Y",strtotime($this->m->from)) == date("Y",strtotime($this->m->to)) && date("m",strtotime($this->m->from)) < date("Y",strtotime($this->m->to))) {
            $period = 'm';
        }

        $this->total_nrc = 0;
        $this->total_ndc = 0;
        $this->total_tdc = 0;
        $this->total_d = 0;
        $this->total_nrw = 0;
        $this->total_revenue = 0;

        foreach($this->nrc as $date => $nrc){
            $this->report['nrc'][(int)date($period,strtotime($date))][] = $nrc->id;
            $this->total_nrc++;
        }

        if ($this->tdc) {
            foreach($this->tdc as $tdc){
                $this->report['tdc'][(int)date($period,strtotime($tdc->date))] += $tdc->amount;
                $this->total_tdc += $tdc->amount;
            }
        }

        if($this->ndc){
            foreach($this->ndc as $date => $ndc){
                $this->report['ndc'][(int)date($period,strtotime($date))][] = $ndc->user_id;
                $this->report['d'][(int)date($period,strtotime($date))] += $ndc->amount;
                $this->total_d += $ndc->amount;
                $this->total_ndc++;
            }
        }

        if($this->nrw){
            foreach($this->nrw as $date => $nrw){
                $this->report['nrw'][(int)date($period,strtotime($date))][] = $nrw->id;
                $this->total_nrw++;
            }
        }

        if($this->nww){
            foreach($this->nww as $date => $nww){
                $this->report['nww'][(int)date($period,strtotime($date))][] = $nww->id;
                $this->total_nww++;
            }
        }

        if($this->revenue){
            foreach($this->revenue as $date=>$revenue){
                $revenue = self::revenueFormula($revenue->bet_amount,$revenue->win_amount,$revenue->deposits_fee,$revenue->active_bonus);
                $this->report['revenue'][(int)date($period,strtotime($date))][] = $revenue;
                $this->total_revenue += $revenue;
            }
        }
    }

/*    public function statistic(){
        $this->date_from = $_GET['date_from'] ? date('Y-m-d H:i:s',strtotime($_GET['date_from'])) : date('Y-m-d 00:00:00',time()-3600*24*30);
        $this->date_to = $_GET['date_to'] ? date('Y-m-d H:i:s',strtotime($_GET['date_to'])) : date('Y-m-d H:i:s');

        //Регистрации
        $this->m->_db->setQuery(
                    "SELECT `users`.* FROM `users` "
                    . " WHERE 1 "
                    . " AND `users`.`date` >= '".$this->date_from."'"
                    . " AND `users`.`date` <= '".$this->date_to."'"
                );
        $users = $this->m->_db->loadObjectList();


        if($users){
            $this->total_registrations = count($users);
            foreach($users as $item){
                $registrations[strtotime(date('Y-m-d',strtotime($item->date)))]++;
            }
            ksort($registrations);
            $i=0;
            foreach($registrations as $date=>$amount){
                $day = date('N',$date);
                if($day == 6){
                    $this->registration .= '"'.$i.'":{"date":"'.$date.'","amount":"'.$amount.'","dot":"#ff0000","color":"#ff0000"},';
                }else if ($day == 7){
                    $this->registration .= '"'.$i.'":{"date":"'.$date.'","amount":"'.$amount.'","dot":"#ff0000"},';
                }else{
                    $this->registration .= '"'.$i.'":{"date":"'.$date.'","amount":"'.$amount.'"},';
                }

                $i++;
            }
            $this->registration = '{'.substr($this->registration, 0,-1).'}';
        }
        //Депозиты
        $this->m->_db->setQuery(
                    "SELECT `deposits`.* "
                    . " FROM `deposits` "
                    . " LEFT JOIN `users` ON `users`.`id` = `deposits`.`user_id`"
                    . " WHERE 1 "
                    . " AND `users`.`date` >= '".$this->date_from."'"
                    . " AND `users`.`date` <= '".$this->date_to."'"
                );
        $result = $this->m->_db->loadObjectList();
        if($result){
            foreach($result as $item){
                $this->total_deposits += $item->amount;
                $deposits[strtotime(date('Y-m-d',strtotime($item->date)))]+=$item->amount;
            }

            ksort($deposits);
            $i=0;
            foreach($deposits as $date=>$amount){
                $day = date('N',$date);
                if($day == 6){
                    $this->deposits .= '"'.$i.'":{"date":"'.$date.'","amount":"'.($amount/100).'","dot":"#ff0000","color":"#ff0000"},';
                }else if($day == 7){
                    $this->deposits .= '"'.$i.'":{"date":"'.$date.'","amount":"'.($amount/100).'","dot":"#ff0000"},';
                }else{
                    $this->deposits .= '"'.$i.'":{"date":"'.$date.'","amount":"'.($amount/100).'"},';
                }

                $i++;
            }

            $this->deposits = '{'.substr($this->deposits, 0,-1).'}';
        }
        //Года
        $this->agesStatistic($this->date_from,$this->date_to);
    }

    public function agesStatistic($from,$to,$mode=null){
        $this->m->_db->setQuery(
                    "SELECT `users`.`id` , `users`.`birthday` FROM `users`"
                    . " WHERE 1"
                    . " AND `users`.`date` >= '".$from."'"
                    . " AND `users`.`date` <= '".$to."'"
                );
        $result = $this->m->_db->loadObjectList();

        $this->agesJson = '';

        $this->total = count($result);
        foreach($result as $item){
            $birthday = strtotime($item->birthday);
            $time = time();
            $year = 356*24*60*60;
            if($time - $birthday < (18*$year)){
                $this->above18++;
            }else if($time - $birthday< 24*$year){
                $this->above24++;
            }else if($time - $birthday< 36*$year){
                $this->above36++;
            }else if($time - $birthday< 45*$year){
                $this->above45++;
            }else if($time - $birthday< 52*$year){
                $this->above52++;
            }else{
                $this->other++;
            }
        }
        $coef = $this->total / 100;

        $this->agesJson = '{"0":{"value":"'.$this->above18.'","percents":"'.($this->above18/$coef).'","name":"До 18ти"}'
                            . ',"1":{"value":"'.$this->above24.'","percents":"'.($this->above24/$coef).'","name":"От 18ти До 24х","color":{"in":"#f16060","out":"#ee4949"}}'
                            . ',"2":{"value":"'.$this->above36.'","percents":"'.($this->above36/$coef).'","name":"От 24х До 36ти","color":{"in":"#d3f5e0","out":"#d9fce6"}}'
                            . ',"3":{"value":"'.$this->above45.'","percents":"'.($this->above45/$coef).'","name":"От 36ти До 45ти","color":{"in":"#b1cff8","out":"#9ac0f4"}}'
                            . ',"4":{"value":"'.$this->above52.'","percents":"'.($this->above52/$coef).'","name":"От 45ти До 52х"}'
                            . ',"5":{"value":"'.$this->other.'","percents":"'.($this->other/$coef).'","name":"Больше 52х","color":{"in":"#f2d477","out":"#f1cd60"}}}';

    }     */


    public function getUsersCSV($download = false){
        $date_from = $_GET['date_from'] ? date('Y-m-d', strtotime($_GET['date_from'])) : date('Y-m-d', time() - (30*24*60*60));
        $date_to = $_GET['date_to'] ? date('Y-m-d', strtotime($_GET['date_to'])) : date('Y-m-d');

        if ($download == false)
            return "";

        $this->m->_db->setQuery(
                    "SELECT `users`.*, `country`.`name_ru` "
                    . " FROM `users` "
                    . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
                    . " WHERE `users`.`gid` = 1"
                    . " AND `users`.`status` = 1"
                    . " AND `users`.`date` >= '".$date_from." 00:00:00'"
                    . " AND `users`.`date` <= '".$date_to." 23:59:59'"
                    . " ORDER by `users`.`id` ASC"
                );
        $result = $this->m->_db->loadObjectList();

        $str_end = $download ? "\r\n" : "<br>";

        $list = '"Обращение","Имя","ID","Фамилия","Рабочий тел.","Контрагент","Мобильный тел.","Источник","Домашний тел.","Адрес E-mail","День рождения","Тел.ассистента","Не отпралять Email","Не звонить","Рекомендации","Ответственный","Создано","Уведомлять ответственного","Дата отправки SMS","Баланс","Демо-Баланс","Пользователь портала","Дата начала обслуживания","Дата окончания обслуживания","Дата последнего входа","Улица","Доп.адрес: улица","Абонентский Ящик","Другой а/я","Город","Доп.адрес: город","Область","Доп.адрес: область","Индекс","Доп.адрес: индекс","Страна","Доп.адрес: страна","Описание"';
        $list .= $str_end;

        foreach($result as $item){
            if ($item->country == '20') {
                if ($item->phone[0] != '7' && strlen($item->phone) == 11) {
                    $item->phone[0] = '7';
                }

                if ($item->phone[0] != '7' && strlen($item->phone) == 10) {
                    $item->phone = '7' . $item->phone;
                }
            }

            $list .= '"--None--",'; //"Обращение"
            $list .= '"' . trim($item->firstname) . '",'; //"Имя"
            $list .= '"' . trim($item->id) . '",'; //"ID"
            $list .= '"' . trim($item->lastname) . '",'; //"Фамилия"
            $list .= '"' . trim($item->phone) . '",'; //"Рабочий тел."
            $list .= '"",'; //"Контрагент"
            $list .= '"",'; //"Мобильный тел."
            $list .= '"--None--",'; //"Источник"
            $list .= '"",'; //"Домашний тел."
            $list .= '"' . trim($item->email) . '",'; //"Адрес E-mail"
            $list .= '"' . date('Y-m-d', strtotime($item->birthday)) . '",'; //"День рождения"
            $list .= '"",'; //"Тел.ассистента"
            $list .= '"1",'; //"Не отпралять Email"
            $list .= '"0",'; //"Не звонить"
            $list .= '"0",'; //"Рекомендации"
            $list .= '"admin",'; //"Ответственный"
            $list .= '"' . $item->date . '",'; //"Создано"
            $list .= '"0",'; //"Уведомлять ответственного"
            //$list .= '"' . $item->date . '",'; //"Изменено"
            $list .= '"",'; //"Дата отправки SMS"
            $list .= '"",'; //"Баланс"
            $list .= '"",'; //"Демо-Баланс"
            $list .= '"0",'; //"Пользователь портала"
            $list .= '"' . date('Y-m-d', strtotime($item->date)) . '",'; //"Дата начала обслуживания"
            $list .= '"",'; //"Дата окончания обслуживания"
            $list .= '"' . date('Y-m-d', strtotime($item->last_login)) . '",'; //"Дата последнего входа"
            $list .= '"",'; //"Улица"
            $list .= '"",'; //"Доп.адрес: улица"
            $list .= '"",'; //"Абонентский Ящик"
            $list .= '"",'; //"Другой а/я"
            $list .= '"",'; //"Город"
            $list .= '"",'; //"Доп.адрес: город"
            $list .= '"",'; //"Область"
            $list .= '"",'; //"Доп.адрес: область"
            $list .= '"",'; //"Индекс"
            $list .= '"",'; //"Доп.адрес: индекс"
            $list .= '"' . trim($item->name_ru) . '",'; //"Страна"
            $list .= '"",'; //"Доп.адрес: страна"
            $list .= '""'; //"Описание"
            //$list .= '""'; //"Изображение"

            $list .= $str_end;
        }

        return $list;
    }

    public function depositFunBalance($account_id) {
        $this->m->_db->setQuery(
            "SELECT `balance` "
            . " FROM `accounts` "
            . " WHERE `id` = " . $this->m->_db->Quote($account_id)
            . " AND `mode` = 0 "
            . " LIMIT 1"
            );
        $this->m->_db->loadObject($account);

        if(!$account){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        $account->balance = 50000;

        xload("class.lib.ws.Client");

        try {
            $packet = new stdClass;
            $packet->name = "changeBalance";
            $packet->balance = $account->balance;
            $packet->account_id = $account_id;
            $packet->author_id = $this->m->_user->id;

            $ws = new WebSocket\Client($this->m->config->serverWS, array('timeout' => 10));

            $ws->send(json_encode($packet));

            $response = json_decode($ws->receive());

            if ($response[0]->status != "ok") {
                echo '{"status":"error", "message":"Ошибка при отправке пакета"}';
                return false;
            }
        } catch(Exception $e) {
            $this->m->_db->setQuery(
                " UPDATE `accounts` "
                . " SET "
                . " `balance` = " . $account->balance
                . " WHERE `id` = " . $this->m->_db->Quote($account_id)
                . " AND `mode` = 0 "
                . " LIMIT 1;"
                );

            if (!$this->m->_db->query()) {
                echo '{"status":"error", "message":"Ошибка при обновлении базы"}';
                return false;
            }
        }

        echo json_encode(array("status"=>"success", "info" => var_export($response, true)));
    }
    public function getUser($id){
        if(!$id) return ;
        $user_id = (int)$id;

        $this->m->_db->setQuery(
                    "SELECT * FROM `users` WHERE `users`.`id` = ".(int)$user_id
                    . " LIMIT 1"
                );
        $user = $this->m->_db->loadObjectList();
        return $user[0];
    }

    public function changePassword($id){
        if(!$id){
            echo '{"status":"error","message":"Не передан Айдишник"}';
            return;
        }

        $user_id = (int)$id;

        //получаем старый пароль что бы сохранить для истории , и заодно проверяем или такой пользователь есть
        $this->m->_db->setQuery(
                    "SELECT `users`.`password`, `users`.`email`, `users`.`gid`, `users`.`country` "
                    . " FROM `users`"
                    . " WHERE `users`.`id` = ".(int)$user_id
                    . " LIMIT 1"
                );
        $this->m->_db->loadObject($user);

        if(!$user->password){
            echo '{"status":"error","message":"Данный пользователь не был найден"}';
            return;
        }

        $salt = makePassword(16);
        $newpassword = makePassword(16);
        $crypt = md5(md5($newpassword).$salt);

        $this->m->_db->setQuery(
                    "UPDATE `users` SET `users`.`password` = '" . $crypt . ":" . $salt . "' , `users`.`bad_auth` = 0"
                    . " WHERE `users`.`id` = ".(int)$user_id
                    . " LIMIT 1"
                );

        if ($this->m->_db->query()) {
            if ($user->gid == 1) {
                if ($user->country == 20) {
                    $mailsubject = "Смена пароля";
                    $mailbody_html = "Уважаемый трейдер,<br><br>Ваш пароль от аккаунта был изменён.<br>Новый пароль: ". $newpassword . "<br><br>С уважением,<br>служба поддержки BinSecret.";
                    $mailbody_txt = "Уважаемый трейдер,\n\nВаш пароль от аккаунта был изменён.\nНовый пароль: ". $newpassword . "\n\nС уважением,\nслужба поддержки BinSecret.";
                } else {
                    $mailsubject = "New password";
                    $mailbody_html = "Dear trader,<br><br>your password has been changed.<br>New password: ". $newpassword . "<br><br>Best regards,<br>support team BinSecret.";
                    $mailbody_txt = "Dear trader,\n\nyour password has been changed.\nNew password: ". $newpassword . "\n\nBest regards,\nsupport team BinSecret.";
                }
                $mailsubject = "=?UTF-8?B?" . base64_encode($mailsubject) ."?=";
                $status = (int)sendemail($user->email, $mailsubject, $mailbody_html, $mailbody_txt);
            }

            echo '{"status":"success","message":"Пароль был успешно изменен на: '.$newpassword.'"}';
            $this->m->add_to_history($row->id, "admin", "changepassword", $user->password);
        }
    }

/*    public function changePassword($id){
        if(!$id){
            echo '{"status":"error","message":"Не передан Айдишник"}';
            return;
        }

        $user_id = (int)$id;

        //получаем старый пароль что бы сохранить для истории , и заодно проверяем или такой пользователь есть
        $this->m->_db->setQuery(
                    "SELECT `users`.`password` FROM `users`"
                    . " WHERE `users`.`id` = ".(int)$user_id
                    . " LIMIT 1"
                );
        $old_password = $this->m->_db->loadResult();

        if(!$old_password){
            echo '{"status":"error","message":"Данный пользователь не был найден"}';
            return;
        }

        $salt = makePassword(16);
        $password = makePassword(16);
        $crypt = md5(md5($password).$salt);

        $row->password = $crypt . ':' . $salt;
        $row->id = (int)$user_id;
        $row->bad_auth = 0;

        $this->m->_db->setQuery(
                    "UPDATE `users` SET `users`.`password` = '".$row->password."' , `users`.`bad_auth` = 0"
                    . " WHERE `users`.`id` = ".(int)$user_id
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            echo '{"status":"success","message":"Пароль был успешно изменен на: '.$password.'"}';
            $this->m->add_to_history($row->id, "admin", "changepassword",$old_password);
        }

    }  */

    public function getBonuses($id){
        $this->m->_db->setQuery(
                "SELECT `user_bonus`.* "
                . " , `users`.`email`"
                . " , `country`.`code`"
                . " , `country`.`name_ru`"
                . " , `deposits`.`amount` as deposit"
                . " FROM `user_bonus` "
                . " LEFT JOIN `users` ON `user_bonus`.`user_id` = `users`.`id`"
                . " LEFT JOIN `deposits` ON `user_bonus`.`deposit_id` = `deposits`.`id`"
                . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
                . " WHERE `user_bonus`.`user_id` = ".(int)$id
                );
        $data = $this->m->_db->loadObjectList();

        return $data;
    }

    public function changeMaxBalance($account_id){
        $this->m->_db->setQuery(
            "SELECT * "
            . " FROM `accounts` "
            . " WHERE `id` = " . $this->m->_db->Quote($account_id)
            . " LIMIT 1"
            );
        $this->m->_db->loadObject($account);

        if(!$account){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        $maxbalance = (int)$_POST['amount'] * 100;

        xload("class.lib.ws.Client");

        try {
            $packet = new stdClass;
            $packet->name = "changeMaxBalance";
            $packet->maxbalance = $maxbalance;
            $packet->account_id = $account_id;
            $packet->author_id = $this->m->_user->id;

            $ws = new WebSocket\Client($this->m->config->serverWS, array('timeout' => 10));

            $ws->send(json_encode($packet));

            $response = json_decode($ws->receive());

            if ($response[0]->status != "ok") {
                echo '{"status":"error", "message":"Ошибка при отправке пакета"}';
                return false;
            }
        } catch(Exception $e) {
            $this->m->_db->setQuery(
                " UPDATE `accounts` "
                . " SET "
                . " `maxbalance` = " . $maxbalance
                . " WHERE `id` = " . $this->m->_db->Quote($account_id)
                . " LIMIT 1;"
                );

            if (!$this->m->_db->query()) {
                echo '{"status":"error", "message":"Ошибка при обновлении базы"}';
                return false;
            }
        }

        echo json_encode(array("status"=>"success", "result" => $result, "info" => var_export($response, true)));
    }

    public function block($id){
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` "
                    . " WHERE `users`.`id` = ".(int)$id
                );
        $user = $this->m->_db->loadObjectList();
        if(!$user){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        if($user[0]->status < 0){
            $this->m->_db->setQuery(
                        "UPDATE `users` SET `users`.`status` = 1 "
                        . " WHERE `users`.`id` = ".(int)$user[0]->id
                        . " LIMIT 1"
                    );
            $result = 'off';
        }else{
            $this->m->_db->setQuery(
                    "UPDATE `users` SET `users`.`status` = -1 "
                    . " WHERE `users`.`id` = ".(int)$user[0]->id
                    . " LIMIT 1"
                );
            $result = 'on';
        }

        if($this->m->_db->query()){
            echo '{"status":"success","result":"'.$result.'"}';
        }
    }
    public function changeAutogain($account_id){
        $this->m->_db->setQuery(
            "SELECT `autogain`, `id` "
            . " FROM `accounts` "
            . " WHERE `id` = " . $this->m->_db->Quote($account_id)
            . " LIMIT 1"
            );
        $this->m->_db->loadObject($account);

        if(!$account){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        if ($account->autogain == 0) {
            $account->autogain = 1;
            $result = 'off';
        } else {
            $account->autogain = 0;
            $result = 'on';
        }

        xload("class.lib.ws.Client");

        try {
            $packet = new stdClass;
            $packet->name = "changeAutoGain";
            $packet->autogain = $account->autogain;
            $packet->account_id = $account_id;
            $packet->author_id = $this->m->_user->id;

            $ws = new WebSocket\Client($this->m->config->serverWS, array('timeout' => 10));

            $ws->send(json_encode($packet));

            $response = json_decode($ws->receive());

            if ($response[0]->status != "ok") {
                echo '{"status":"error", "message":"Ошибка при отправке пакета"}';
                return false;
            }
        } catch(Exception $e) {
            $this->m->_db->setQuery(
                " UPDATE `accounts` "
                . " SET "
                . " `autogain` = " . $account->autogain
                . " WHERE `id` = " . $this->m->_db->Quote($account_id)
                . " LIMIT 1;"
                );

            if (!$this->m->_db->query()) {
                echo '{"status":"error", "message":"Ошибка при обновлении базы"}';
                return false;
            }
        }

        echo json_encode(array("status"=>"success", "result" => $result, "info" => var_export($response, true)));
    }

    public function changeStatus($id){
        $this->m->_db->setQuery(
                    "SELECT `users`.* "
                    . " FROM `users` "
                    . " WHERE `users`.`id` = ".(int)$id
                );
        $user = $this->m->_db->loadObjectList();
        if(!$user){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        if($user[0]->bad_auth >=5){
            $this->m->_db->setQuery(
                        "UPDATE `users` SET `users`.`bad_auth` = 0 "
                        . " WHERE `users`.`id` = ".(int)$user[0]->id
                        . " LIMIT 1"
                    );
            $result = 'off';
        }else{
            $this->m->_db->setQuery(
                    "UPDATE `users` SET `users`.`bad_auth` = 5 "
                    . " WHERE `users`.`id` = ".(int)$user[0]->id
                    . " LIMIT 1"
                );
            $result = 'on';
        }

        if($this->m->_db->query()){
            echo '{"status":"success","result":"'.$result.'"}';
        }
    }

    public function setGain($account_id, $val, $type){
        $this->m->_db->setQuery(
            "SELECT `gain` "
            . " FROM `accounts` "
            . " WHERE `id` = " . $this->m->_db->Quote($account_id)
            );
        $this->m->_db->loadObject($account);

        if(!$account){
            echo '{"status":"error","message":"Не найден такой пользователь"}';
            return;
        }

        $account->gain = (int)$val;
        if($account->gain < -50) $account->gain = -50;

        xload("class.lib.ws.Client");

        try {
            $packet = new stdClass;
            $packet->name = "changeGain";
            $packet->gain = $account->gain;
            $packet->account_id = $account_id;
            $packet->author_id = $this->m->_user->id;

            $ws = new WebSocket\Client($this->m->config->serverWS, array('timeout' => 10));

            $ws->send(json_encode($packet));

            $response = json_decode($ws->receive());

            if ($response[0]->status != "ok") {
                echo '{"status":"error", "message":"Ошибка при отправке пакета"}';
                return false;
            }
        } catch(Exception $e) {
            $this->m->_db->setQuery(
                " UPDATE `accounts` "
                . " SET "
                . " `gain` = " . $account->gain
                . " WHERE `id` = " . $this->m->_db->Quote($account_id)
                . " LIMIT 1;"
                );

            if (!$this->m->_db->query()) {
                echo '{"status":"error", "message":"Ошибка при обновлении базы"}';
                return false;
            }
        }

        echo json_encode(array("status"=>"success","result"=>$account->gain, "info" => var_export($response, true)));
    }

    public function setBadAuth($id,$val){
        $id = (int)$id;
        $val = (int)$val;
        if(!$id) echo '{"status":"error","message":"Нету АйДи пользователя"}';

        $this->m->_db->setQuery(
                    "UPDATE `users` "
                    . " SET `users`.`bad_auth` = ".(int)$val
                    . " WHERE `users`.`id` = ".(int)$id
                    . " LIMIT 1"
                );
        if($this->m->_db->query()){
            echo '{"status":"success"}';
        }

    }

    public function phoneAnalize($user){
        if(!$user->phone) return false;

        $this->m->_db->setQuery(
            "SELECT `users`.* "
            . " , `partners`.`username` as `partner_name`"
            . " , `country`.`code` as country"
            . " , (SELECT SUM(amount) FROM `deposits` WHERE `deposits`.`user_id` = `users`.`id` AND `deposits`.`status` = 1) as deposits"
            . " , (SELECT SUM(amount) FROM `withdraws` WHERE `withdraws`.`user_id` = `users`.`id`) as withdraws"
            . " FROM `users` "
            . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
            . " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`"
            . " WHERE `users`.`phone` LIKE '%".$user->phone."'"
            //. " AND `users`.`id` NOT IN ('" . implode("', '", $checked_id) . "')" //TODO minus checek ids
            . " AND `users`.`id` != ". (int)$user->id
            );
        $phones = $this->m->_db->loadObjectList();
        return $phones;
    }

    public function ipAnalize($user){
        //$table = 'history';
        $this->m->_db->setQuery(    //получаем все айПишники с которых заходили с данного аккаунта
                " SELECT `history`.`ip` "
              . " FROM `history` "
              . " WHERE `history`.`user_id` = " . $user->id
              . " AND `history`.`ip` NOT IN ('87.118.126.64','46.183.149.53','144.76.222.3') "
              . " GROUP BY `history`.`ip` "
              . " ORDER BY `history`.`id` ASC"
            );
        $ip = $this->m->_db->loadObjectList();

        foreach($ip as $item)$ips[] = $item->ip;
        if(!$ips) return;

        //АНАЛИЗИРУЕМ ПОСЕЩЕНИЯ
        $table = 'visitors_activity';
        $this->m->_db->setQuery(
                    "SELECT `".$table."`.`visitor_id` "
                    . " FROM `".$table."`"
                    . " WHERE `".$table."`.`ip` IN ('". implode('\',\'',$ips)."') "
                    . " GROUP BY `".$table."`.`visitor_id` "
                    . " ORDER BY `".$table."`.`id` DESC"
                );
        $visitors = $this->m->_db->loadObjectList();
        foreach($visitors as $item)$visitor_ids[] = $item->visitor_id;

        $this->m->_db->setQuery(
                    "SELECT `".$table."`.`ip` "
                    . " FROM `".$table."`"
                    . " WHERE `".$table."`.`visitor_id` IN (".implode(',',$visitor_ids).")"
                    . " GROUP BY `".$table."`.`ip` "
                    . " ORDER BY `".$table."`.`id` DESC"
                );
        $visitors_ip = $this->m->_db->loadObjectList();

        foreach($visitors_ip as $item) $visit_ips[] = $item->ip;

        $ips = array_unique(array_merge($visit_ips,$ips));

        $this->m->_db->setQuery(    //получаем пользователей соотвутствующих полученным АйПишникам
                  " SELECT `history`.`user_id`"
                    . " , `users`.`last_ip` as ip"
                    . " , `users`.`email`"
                    //. " , `users`.`demo`"
                    //. " , `users`.`balance`"
            . " , `accounts`.`balance`"
                    . " , `users`.`partner`"
                    . " , `partners`.`username` as `partner_name`"
                    . " , `country`.`code` as country"
                    . " , (SELECT SUM(amount) FROM `deposits` WHERE `deposits`.`user_id` = `users`.`id` AND `deposits`.`status` = 1) as deposits"
                    . " , (SELECT SUM(amount) FROM `withdraws` WHERE `withdraws`.`user_id` = `users`.`id`) as withdraws"
                    . " FROM `history` "
                    . " LEFT JOIN `users` ON `history`.`user_id` = `users`.`id`"
                    . " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`"
                    . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
            . " LEFT JOIN `accounts` ON `accounts`.`mode` = 1 AND `accounts`.`user_id` = `users`.`id`"
                    . " WHERE `history`.`user_id` != " . $user->id
                    . " AND `users`.`gid` IN (1,2) "
                    . " AND `history`.`ip` IN ('". implode('\',\'',$ips)."') "
                    . " GROUP BY `history`.`user_id` "
                    . " ORDER BY `history`.`id` DESC"
                );
        $result = $this->m->_db->loadObjectList("user_id");
        
        return $result;
    }

    public function analize($id){
        $id = (int)$id;

        $this->m->_db->setQuery(    //получаем пользователя что бы проверить что такой вообще есть.. что бы не делать лишних действий
            "SELECT `users`.`id` "
            . " , `users`.`firstname`"
            . " , `users`.`lastname`"
            . " , `users`.`email`"
            . " , `accounts`.`balance`"
            . " , `users`.`phone_prefix`"
            . " , `users`.`phone_area`"
            . " , `users`.`phone`"
            . " , `users`.`birthday`"
            . " , `users`.`status`"
            //. " , `users`.`demo`"
            . " , `users`.`last_login`"
            . " , `users`.`last_modified`"
            . " , `users`.`last_ip`"
            . " , `users`.`partner`"
            . " , `users`.`date`"
            . " , `country`.`name_ru` as country"
            . " FROM `users` "
            . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
        . " LEFT JOIN `accounts` ON `accounts`.`mode` = 1 AND `accounts`.`user_id` = `users`.`id`"
            . " WHERE `users`.`id` = ".(int)$id
            . " LIMIT 1"
            );
        $this->m->_db->loadObject($user);
        if(!$user) return;
//        $user = reset($user);


        $this->ips = $this->ipAnalize($user);
        $this->phones = $this->phoneAnalize($user);

    }

    public function userinfo(){
        $this->m->_db->setQuery(
            "SELECT `users`.`id` "
            . " , `users`.`firstname`"
            . " , `users`.`lastname`"
            . " , `users`.`fathersname`"
            . " , `users`.`upass`"
            . " , `users`.`passport`"
            . " , `users`.`pin_code`"
            . " , `users`.`inn`"
            . " , `users`.`email`"
            . " , `users`.`phone_prefix`"
            . " , `users`.`phone_area`"
            . " , `users`.`phone`"
            . " , `users`.`birthday`"
            . " , `users`.`status`"
            . " , `users`.`metka`"
            . " , `users`.`bad_auth`"
            . " , `users`.`last_login`"
            . " , `users`.`last_modified`"
            . " , `users`.`last_ip`"
            . " , `users`.`partner`"
            . " , `users`.`crm_id`"
            . " , `users`.`date`"
            . " , `users`.`robot`"
            . " , `users`.`gid`"
            . " , `partners`.`username` as partner"
            . " , `afftracks`.`afftracker`"
            . " , `country`.`name_ru` as country"
            . " , `users`.`timezone`"
//            . " , (SELECT (100/SUM(`user_bonus`.`total_bet`)*SUM(`user_bonus`.`bet`)) FROM `user_bonus` WHERE `user_bonus`.`status` = 1 AND  `user_bonus`.`user_id` = `users`.`id`) as bonus"
            . " FROM `users` "
            . " LEFT JOIN `partners` ON `users`.`partner` = `partners`.`id`"
            . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
            . " LEFT JOIN `afftracks` ON `users`.`afftrack` = `afftracks`.`id`"
            . " WHERE `users`.`id` = " . (int)$_GET['id']
            . " LIMIT 1"
            );
        $this->m->_db->loadObject($user);

        $this->m->_db->setQuery(
            "SELECT `accounts`.* "
            . " , (SELECT (100/SUM(`user_bonus`.`total_bet`)*SUM(`user_bonus`.`bet`)) FROM `user_bonus` WHERE `user_bonus`.`status` = 1 AND `user_bonus`.`account_id` = `accounts`.`id`) as bonus"
            . " FROM `accounts` "
            . " WHERE `accounts`.`user_id` = " . (int)$_GET['id']
            . " ORDER BY `accounts`.`mode` DESC, `accounts`.`id` ASC"
            );
        $user->accounts = $this->m->_db->loadObjectList();

        return $user;
    }

    public function history(){
        $this->m->_db->setQuery(
                    "SELECT COUNT(`history`.`id`) as cnt"
                    . " FROM `history` "
                    . " WHERE 1"
                );
        $total = $this->m->_db->loadResult();

        $xNav = new xNav("/stats/history/", $total, "GET");
        $xNav->limit = 20;
        $this->m->pagesNav = $xNav->showPages();

        $this->m->_db->setQuery(
                    "SELECT `history`.* "
                    . " , `users`.`email`"
                    . " , `users`.`demo`"
                    . " , `country`.`code`"
                    . " FROM `history` "
                    . " LEFT JOIN `users` ON `users`.`id` = `history`.`user_id`"
                    . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
                    . " WHERE 1"
                    . " ORDER BY `history`.`id` DESC"
                    . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
                );
        $this->data = $this->m->_db->loadObjectList();
    }

    public function gamers_stats(){
        $email_filter = trim($_GET['email']);
        $id_filter = (int)$_GET['id'];
        $partner_filter = trim($_GET['partner']);
        $lastname_filter = trim($_GET['lastname']);
        $firstname_filter = trim($_GET['firstname']);
        $sort = (int)($_GET['sort']);

        $phone = trim(strip_tags($_GET['phone']));
        $date_from = $_GET['date_from'] ? date('Y-m-d 00:00:00',strtotime($_GET['date_from'])) : null;
        $date_to = $_GET['date_to'] ? date('Y-m-d 23:59:59',strtotime($_GET['date_to'])) : null;

        $sub_page_filter = strip_tags($this->m->_path[2]);  //фильтр подраздела

        $sql =  ($email_filter ? " AND `users`.`email` LIKE '%".$email_filter."%' " : "")
                . ($id_filter? " AND `accounts`.`user_id` = ".$id_filter : '')
                . ((int)$partner_filter ? "  AND  `users`.`partner` = '".$partner_filter."'" : "")
                //. ($partner_filter && (int)$partner_filter == 0 ? "  AND  `partners`.`username` LIKE '".$partner_filter."'" : "")

                . ($phone ? " AND `users`.`phone` LIKE '%".$phone."%'" : '')

                . ($lastname_filter ? " AND `users`.`lastname` LIKE '%".$lastname_filter."%'" : '')
                . ($firstname_filter ? " AND `users`.`firstname` LIKE '%".$firstname_filter."%'" : '')

                . ($date_from ? " AND `users`.`date` >= '".$date_from."'" : "")
                . ($date_to ? " AND `users`.`date` < '".$date_to."'" : "")

                . ( $sub_page_filter == 'blocked' ? " AND `users`.`bad_auth` >= 5" : "")

                . ( $sub_page_filter == 'balance' || $sub_page_filter == 'balanceonline'? " AND `accounts`.`balance` >= 1000" : "")
                . ( $sub_page_filter == 'balanceonline' ? " AND `x_session`.`userid` = `users`.`id`" : "" )
                . ( $sub_page_filter == 'demobalance' ? " AND `accounts`.`balance` > 0 " : "")
                . ( $sub_page_filter == 'demobalanceonline' ? " AND `accounts`.`balance` > 0 AND `x_session`.`userid` = `accounts`.`user_id`" : "")
                . ( $sub_page_filter == 'withoutbalance' ? " AND `accounts`.`balance` = 0" : "")
                //. ( $sub_page_filter == 'real' ? " AND `".$this->_table."`.`demo` = 0" : "" )
                . ( $sub_page_filter == 'realgain' ? " AND `accounts`.`gain` != 0" : "" )
                . ( $sub_page_filter == 'unactive' ? " AND `users`.`last_login` = '0000-00-00 00:00:00'" : "" )
                . ( $sub_page_filter == 'unconnected' ? " AND `users`.`gid` = 2 AND `users`.`robot_settings` = ''" : "" )
                
                . " AND `users`.`partner` IN (".implode(',',$this->m->partner_ids).") ";

                $mode = ($sub_page_filter == 'demobalance' || $sub_page_filter == 'demobalanceonline') ? "0" : "1"; // 0 - demo, 1 - real

        //Count Elements
        $this->m->_db->setQuery(
            "SELECT COUNT(*) as cnt "
            . " FROM `accounts`"
            . " LEFT JOIN `users` ON `users`.`id` = `accounts`.`user_id` "
            . ( $partner_filter && (int)$partner_filter == 0 ? " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`" : "" )
            . ( $sub_page_filter == 'balanceonline' ? " LEFT JOIN `x_session` ON `x_session`.`userid` = `users`.`id`" : "" )
            . " WHERE `accounts`.`mode` = " . $mode
            . $sql
            );
        $total = $this->m->_db->loadResult();

        $xNav = new xNav("/stats/gamers/".($sub_page_filter? $sub_page_filter.'/':'' ), $total, "GET");
        $xNav->limit = 20;
        $this->m->pagesNav = $xNav->showPages();

        //SELECT ELEMENTS
        $this->m->_db->setQuery(
                "SELECT `accounts`.`id` as account_id,`accounts`.`balance`,`accounts`.`deposit_sum`,`accounts`.`withdraw_sum`,`accounts`.`gain` "
                . " , `users`.* "
                . " , (SELECT (100/SUM(`user_bonus`.`total_bet`)*SUM(`user_bonus`.`bet`)) FROM `user_bonus` WHERE `user_bonus`.`status` = 1 AND `user_bonus`.`account_id` = `accounts`.`id`) as bonus"
                . " , (SELECT COUNT(`withdraws`.`id`) FROM `withdraws` WHERE `withdraws`.`user_id` = `users`.`id` AND `withdraws`.`result` != 'ok') as withdrawals"
                . " , `country`.`code`"
                . " , `country`.`name_ru`"
                . " , `partners`.`username` as partner_name"
                . " , `afftracks`.`afftracker`"
                . " , `x_session`.`session_id`"
                . " FROM `accounts`"
                . " LEFT JOIN `users` ON `users`.`id` = `accounts`.`user_id` "
                . " LEFT JOIN `country` ON `country`.`id` = `users`.`country`"
                . " LEFT JOIN `partners` ON `partners`.`id` = `users`.`partner`"
                . " LEFT JOIN `afftracks` ON `users`.`afftrack` = `afftracks`.`id`"
                //. ( $sub_page_filter == 'balanceonline' || $sub_page_filter == 'demobalanceonline' ? " LEFT JOIN `x_session` ON `x_session`.`userid` = `users`.`id`" : "" )
                . " LEFT JOIN `x_session` ON `x_session`.`userid` = `users`.`id` AND `x_session`.`gid` <= 2 "
                . " WHERE `accounts`.`mode` = " . $mode
                . $sql
                . " GROUP BY `accounts`.`id` "
                . (!$sort ? " ORDER BY `accounts`.`id` DESC":'')
                . ($sort == 1 ? " ORDER BY `accounts`.`balance` DESC":'')
                //. ($sort == 2 ? " ORDER BY `".$this->_table."`.`funbalance` DESC":'')
                . " LIMIT ".$xNav->limit." OFFSET ".$xNav->start.""
            );

        $this->data = $this->m->_db->loadObjectList('account_id');
        
        if(!$this->data) return;

        foreach($this->data as $key => $users){
            $this->data[$key]->is_pwd_changed = false;

            if ($users->gid == 2 && !empty($users->upass)) {
                $password = RC4_decrypt($users->upass, md5(md5($users->email . 'SecKeyword').'-keygames'));
                list($hash, $salt) = explode(':', $users->password);
                $cryptpass = md5(md5($password) . $salt);
                if ($hash != $cryptpass) {
                    $this->data[$key]->is_pwd_changed = true;
                }
            }

            $users->robot_status = "";

            if ($users->robot_settings != "") {
                $users->robot_settings = json_decode($users->robot_settings);
                if ($users->robot_settings->real == 1) {
                    $users->robot_status = "R-R";
                } else {
                    $users->robot_status = "R-D";
                }
            }

            if ($users->robot == 1 && $users->robot_status != "") {
                $users->robot_status .= "+";
            }

            $ids[] = $key;
        }

        if ($mode == 1) {
            $this->m->_db->setQuery(    //получаем все ставки совершенные пользователями выше
                "SELECT `deals`.`id` "
                . " , `deals`.`user_id`"
                . " , `deals`.`account_id`"
                . " , `deals`.`status`"
                . " , `deals`.`bet`"
                . " , `deals`.`payout`"
                . " , `deals`.`payback`"
                . " , `deals`.`end`"
                . " FROM `deals` "
                . " WHERE `deals`.`account_id` IN (" . implode(',',$ids) . ")"
                );
            $result = $this->m->_db->loadObjectList();

            foreach($result as $item){
                if($item->status == 1){
                    if(strtotime($item->end) <= time()){
                        $bets[$item->account_id][$item->status]['ended'] += $item->bet;
                    } else {
                        $bets[$item->account_id][$item->status]['active'] += $item->bet;
                    }
                }else if($item->status == 2){
                    $bets[$item->account_id][$item->status] += $item->bet*($item->payout/100);
                }else if($item->status == 3){
                    $bets[$item->account_id][$item->status] += $item->bet - ($item->bet*($item->payback/100));
                }else if($item->status == 4){
                    $bets[$item->account_id][$item->status] += $item->bet;
                }

                $bets[$item->account_id]['all'] += $item->bet;
            }

            if($bets){//заносим результаты в общзий массив
                foreach($bets as $id=>$item){
                    $this->data[$id]->ended = $item[1]['ended'];
                    $this->data[$id]->active = $item[1]['active'];
                    $this->data[$id]->win = $item[2];
                    $this->data[$id]->lose = $item[3];
                    $this->data[$id]->equel = $item[4];
                    $this->data[$id]->all = $item['all'];
                }
            }
        }
    }

}
?>