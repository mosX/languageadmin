<?php
class Account{
    private $m;
    public $numDaysRadio;
    public $roundOnPageArray;
    public function __construct(mainframe & $mainframe){
        $this->m =  $mainframe;

        $this->numDaysArray     = array(7,14,30,60);
        $this->roundOnPageArray = array(25,50,75,100);
    }

     public function validation_personalinfo() {

        $message = array (
            "password_empty" => _("You must enter your password!"),
            "password_incorrect" => _("You entered the wrong password!"),
            "answer_empty" => _("You must enter the answer to the question!"),
            "answer_incorrect" => _("You have entered an incorrect answer to the question   !"),
            "street_empty" => _("You must enter your address!"),
            "street_incorrect" => _("You entered the wrong address!"),
            "zip_empty" => _("You must enter your zip code!"),
            "zip_incorrect" => _("You entered the wrong zip code!"),
            "city_empty" => _("You must enter the name of your city!"),
            "city_incorrect" => _("You have entered an incorrect name of your city!"),
            "country_empty" => _("You did not choose the country!"),
            "phone_empty" => _("Please enter a valid phone number!"),
            "email_incorrect" => _("E-mail address is incorrect!"),
            "email_already_use" => _("E-mail address already exists!"),
            "email_validation_code_incorrect" => _("You entered the wrong security code!")
        );

        $validation = array( 'valid' => true, 'reason' => array() );

        //$passwd = getParam($_POST, "passwd");
        $answer = getParam($_POST, "answer");

        if ($answer == "") {
            $validation["valid"] = false;
            $validation["reason"]["answer"] = $message["answer_empty"];
        } else {
            if ( ! $this->m->_auth->checkUserAnswer($answer) ) {
                $validation["valid"] = false;
                $validation["reason"]["answer"] = $message["answer_incorrect"];
            } else {
                $_POST["street"] = getParam($_POST, "street");
                if (strlen($_POST["street"]) > 100) {
                    $validation["valid"] = false;
                    $validation["reason"]["street"] = $message["street_incorrect"];
                }

                $_POST["zip"] = getParam($_POST, "zip");
                if (strlen($_POST["zip"]) > 8) {
                    $validation["valid"] = false;
                    $validation["reason"]["zip"] = $message["zip_incorrect"];
                }

                $_POST["city"] = getParam($_POST, "city");
                if (strlen($_POST["city"]) > 50) {
                    $validation["valid"] = false;
                    $validation["reason"]["city"] = $message["city_incorrect"];
                }

                $_POST["country"] = getParam($_POST, "country");
                if ($_POST["country"] == "" || strlen($_POST["country"]) > 2) {
                    $validation["valid"] = false;
                    $validation["reason"]["country"] = $message["country_empty"];
                }

                $_POST["phone"] = getParam($_POST, "phone");
                if (!preg_match('/^\+?\d{6,13}$/', $_POST["phone"]) || strlen($_POST["phone"]) > 14) {
                    $validation["valid"] = false;
                    $validation["reason"]["phone"] = $message["phone_empty"];
                }

                $_POST["new_email"] = getParam($_POST, "new_email");
                if (!empty($_POST["new_email"])) {
                    if (!is_email($_POST["new_email"]) || $_POST["new_email"] == $this->m->_user->email || strlen($_POST["new_email"]) > 50) {
                        $validation["valid"] = false;
                        $validation["reason"]["new_email"] = $message["email_incorrect"];
                    } else {
                        $this->m->_db->setQuery(
                            "SELECT `id`"
                          . " FROM `users` "
                          . " WHERE (`users`.`email` = " . $this->m->_db->Quote($_POST["new_email"]) . " OR `users`.`new_email` = " . $this->m->_db->Quote($_POST["new_email"]) . ") "
                          . " AND `users`.`id` != " . (int)$this->m->_user->id
                          . " AND `users`.`club_id` >= ". $this->m->config->club_id_start ." AND `users`.`club_id` <= " . $this->m->config->club_id_end
                          . " LIMIT 1;"
                        );

                        if (intval($this->m->_db->loadResult())) {
                            $validation["valid"] = false;
                            $validation["reason"]["new_email"] = $message["email_already_use"];
                        }
                    }
                }

                if (!empty($this->m->_user->new_email_validation_code) && !empty($_POST["new_email_validation_code"])) {
                    $_POST["new_email_validation_code"] = getParam($_POST, "new_email_validation_code");
                    if ($this->m->_user->new_email_validation_code != $_POST["new_email_validation_code"] || $_POST["new_email"] == $this->m->_user->email || $_POST["new_email"] != $this->m->_user->new_email) {
                        $validation["valid"] = false;
                        $validation["reason"]["new_email_validation_code"] = $message["email_validation_code_incorrect"];
                    }
                }
            }
        }

        return $validation;
    }

    public function historygame(){
            if (isset($_POST["numDays"]) || !array_key_exists($this->m->_user->id . ".historygames.numDays", $_SESSION)) {
                $numDays = intval(getParam($_POST, "numDays", $this->numDaysArray[0]));
                if (!in_array($numDays, $this->numDaysArray)){
                    $numDays = $this->numDaysArray[0];
                }
                $_SESSION[$this->m->_user->id . ".historygames.numDays"] = $numDays;
            } elseif (array_key_exists($this->m->_user->id . ".historygames.numDays", $_SESSION)) {
                $numDays = $_SESSION[$this->m->_user->id . ".historygames.numDays"];
            }

            if (isset($_POST["onPage"]) || !array_key_exists($this->m->_user->id . ".historygames.onPage", $_SESSION)) {
                $onPage = intval(getParam($_POST, "onPage", $this->roundOnPageArray[0]));
                if (!in_array($onPage, $this->roundOnPageArray)){
                    $onPage = $this->roundOnPageArray[0];
                }
                $_SESSION[$this->m->_user->id . ".historygames.onPage"] = $onPage;
            } elseif (array_key_exists($this->m->_user->id . ".historygames.onPage", $_SESSION)) {
                $onPage = $_SESSION[$this->m->_user->id . ".historygames.onPage"];
            }

            $page = isset($this->m->_path[2]) ? (int) $this->m->_path[2] : 1;

            $start = ($page-1)*$onPage;

            foreach($this->numDaysArray as $v) {
                $this->numDaysRadio[$v] = ($v==$numDays) ? " checked=\"true\"" : " ";
            }
            foreach($this->roundOnPageArray as $v) {
                $this->roundOnPageList[$v] = ($v==$onPage) ? " selected=\"true\"" : "";
            }

            $needdate = time() - $numDays*3600*24;

            $where = " AND UNIX_TIMESTAMP(ug.`date`) > '$needdate'";

            $this->m->_db->setQuery("SELECT COUNT(ug.user_id) FROM user_games ug WHERE ug.user_id=" . (int)$this->m->_user->id . $where);
            $this->total = $this->m->_db->loadResult();

            $query = "SELECT ug.*, g.game_name, g.display_name, g.game_type"
                   . "\n FROM user_games ug"
                   . "\n LEFT JOIN games g ON ug.game_id = g.id"
                   . "\n WHERE user_id=".(int)$this->m->_user->id
                   . $where
                   . "\n ORDER BY  ug.`date` DESC"
                   ;

            $this->m->_db->setQuery($query, $start, $onPage);

            $this->rows = $this->m->_db->loadObjectList();

            $this->pager = $this->getPages($this->total, $start, $onPage, $page, "account/historygames");
    }

    function validation_password($task) {
        $message = array (
            "password_empty" => _("You must enter your password!"),
            "password_incorrect" => _("You entered the wrong password!"),
            "oldanswer_empty" => _("You must enter the answer to the question!"),
            "oldanswer_incorrect" => _("You have entered an incorrect answer to the question!"),
            "newpassword_empty" => _("You must enter your new password!"),
            "newpassword_incorrect" => _("Passwords do not match!"),
            "question_empty" => _("You must select a secret question!"),
            "answer_empty" => _("You must provide an answer to the question!"),
            "answer_long" => _("Length of the response should not exceed 30 characters!")
        );

        $validation = array( 'valid' => true, 'reason' => array() );
        if ($task == "password") {
            // Меняем пароль
            $answer = getParam($_POST, "answer");

            if ($answer == "") {
                $validation["valid"] = false;
                $validation["reason"]["answer"] = $message["oldanswer_empty"];
            } else {
                if (!$this->m->_auth->checkUserAnswer($answer)) {
                    $validation["valid"] = false;
                    $validation["reason"]["answer"] = $message["oldanswer_incorrect"];
                } else {
                    $_POST["NewPassword"] = getParam($_POST, "NewPassword");
                    $_POST["ConfirmNewPassword"] = getParam($_POST, "ConfirmNewPassword");

                    if ( $_POST["NewPassword"] == "" || strlen($_POST["NewPassword"]) > 50 ||
                         $_POST["ConfirmNewPassword"] == "" || strlen($_POST["ConfirmNewPassword"]) > 50 ||
                         $_POST["NewPassword"] != $_POST["ConfirmNewPassword"] ) {
                        $validation["valid"] = false;
                        $validation["reason"]["NewPassword"] = $message["newpassword_incorrect"];
                    }
                }
            }
        } elseif ($task == "question") {
            // Меняем контрольный вопрос
            $oldanswer = getParam($_POST, "oldanswer");

            if ($oldanswer == "") {
                $validation["valid"] = false;
                $validation["reason"]["oldanswer"] = $message["oldanswer_empty"];
            } else {
                if (!$this->m->_auth->checkUserAnswer($oldanswer)) {
                    $validation["valid"] = false;
                    $validation["reason"]["oldanswer"] = $message["oldanswer_incorrect"];
                } else {
                    $_POST["question"] = getParam($_POST, "question");

                    if ( $_POST["question"] == "" ) {
                        $validation["valid"] = false;
                        $validation["reason"]["question"] = $message["question_empty"];
                    }

                    $_POST["answer"] = getParam($_POST, "answer");

                    if ( $_POST["answer"] == "" ) {
                        $validation["valid"] = false;
                        $validation["reason"]["answer"] = $message["answer_empty"];
                    } elseif ( strlen($_POST["answer"]) > 30 ) {
                        $validation["valid"] = false;
                        $validation["reason"]["answer"] = $message["answer_long"];
                    }
                }
            }
        }

        return $validation;
    }


    function validation_withdrawal() {
        $message = array (
            "password_empty" => _("You must enter your password!"),
            "password_incorrect" => _("You entered the wrong password!"),
            "answer_empty" => _("You must enter the answer to the question!"),
            "answer_incorrect" => _("You have entered an incorrect answer to the question!"),
            "have_bonus" => _("You have not closed a bonus. After the closing you can request a withdrawal."),
            "have_withdrawal" => _("Do you have an application in standby mode. Wait for processing."),
            "amount_min" => _("Do you have an application in standby mode. Wait for processing."),
            "amount_min" => _("The minimum amount of withdrawal USD 20.00!"),
            "amount_min" => _("The minimum amount of withdrawal USD 20.00!"),
            "amount_max" => _("The maximum amount of withdrawals USD 15000.00!"),
            "balance_min_amount" => _("It is not enough money in the account!"),
            "cardnumber_incorrect" => _("Please enter a valid card number!"),
            "phone_incorrect" => _("Please enter a valid phone number!"),
            "wmz_incorrect" => _("Invalid number of purse. Your WebMoney purse should contain the letter Z of the 12 digits!"),
            "lru_incorrect" => _("Please enter a valid account number!"),
            "pmu_incorrect" => _("Please enter a valid account number!"),
            "withdrawal_incorrect" => _("You can not withdraw funds to the account. On account of this deposit is not implemented.")
            );

        $validation = array('valid' => true, 'reason' => array());

        $this->m->_db->setQuery(
              " SELECT `deposits`.`paysystem_account` "
            . " FROM `deposits` "
            . " WHERE `deposits`.`user_id` = " . $this->m->_user->id
            . " GROUP BY `deposits`.`paysystem_account`;"
            );
        $payment_system_accounts = count($this->m->_db->loadResultArray());

        if ($payment_system_accounts >= 2) {
            
            $answer = getParam($_POST, "answer");

            if (empty($answer)) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["answer_empty"];

                return $validation;
            }
    
            if (!$this->m->_auth->checkUserAnswer($answer)) {
                $validation["valid"] = false;
                $validation["reason"]["answer"] = $message["answer_incorrect"];

                (int)$this->m->_user->bad_withdraw_answer++;
                $row->bad_withdraw_answer = $this->m->_user->bad_withdraw_answer;
                $row->id = $this->m->_user->id;
                $this->m->_db->updateObject("users", $row, "id");

                $this->m->add_to_history($this->m->_user->id, "account", "withdrawfailedanswer");

                if ($this->m->_user->bad_withdraw_answer >= 5)
                    $this->m->_auth->logout("/signin/forgot/?blockedlogin=true");

                return $validation;
            }
        }

        $this->m->_db->setQuery(
             "SELECT COUNT(*)"
           . " FROM `user_bonus`"
           . " WHERE `user_bonus`.`user_id`=" . $this->m->_user->id
           . " AND `user_bonus`.`status`= 1 "
           . " LIMIT 1;"
        );

        if ((int)$this->m->_db->loadResult()) {
            $validation["valid"] = false;
            $validation["reason"]["_withdrawal"] = $message["have_bonus"];

            return $validation;
        }
        
        $this->m->_db->setQuery(
             "SELECT COUNT(*)"
           . " FROM `withdraws`"
           . " WHERE `withdraws`.`user_id`=" . $this->m->_user->id
           . " AND `withdraws`.`result`='add'"
           . " LIMIT 1;"
        );

        if ((int)$this->m->_db->loadResult()) {
            $validation["valid"] = false;
            $validation["reason"]["_withdrawal"] = $message["have_withdrawal"];

            return $validation;
        }

        $amount = floatval(str_replace(",", ".", getParam($_POST, "amount", 0))) * 100;

        if ($amount < 2000) {
            $validation["valid"] = false;
            $validation["reason"]["amount"] = $message["amount_min"];

            return $validation;
        } elseif ($amount > 1500000) {
            $validation["valid"] = false;
            $validation["reason"]["amount"] = $message["amount_max"];

            return $validation;
        }
        
        if ($this->m->_user->balance < $amount) {
            $validation["valid"] = false;
            $validation["reason"]["_withdrawal"] = $message["balance_min_amount"];
            $validation["reason"]["amount"] = $message["balance_min_amount"];

            return $validation;
        }

        if ("cc" == $this->payment_system) {
            $payment_system_account = getParam($_POST, "cardnumber");

            if (!preg_match("/^[4]{1}\d{3}((\ ){1})?\d{4}((\ ){1})?\d{4}((\ ){1})?\d{4}$/", $payment_system_account)) {
                $validation["valid"] = false;
                $validation["reason"]["cardnumber"] = $message["cardnumber_incorrect"];

                return $validation;
            }
        } elseif ("liqpay" == $this->payment_system) {
            $payment_system_account = getParam($_POST, "phone");
            if (!preg_match('/^(\+)?\d{11,12}$/', $payment_system_account)) {
                $validation["valid"] = false;
                $validation["reason"]["phone"] = $message["phone_incorrect"];

                return $validation;
            }
        } elseif ("webmoney" == $this->payment_system) {
            $payment_system_account = strtoupper(getParam($_POST, "wmz"));

            if (!preg_match("/^[Z]{1}\d{12}$/", $payment_system_account)) {
                $payment_system_account;            
                $validation["valid"] = false;
                $validation["reason"]["wmz"] = $message["wmz_incorrect"];

                return $validation;
            }
            
        } elseif ("libertyreserve" == $this->payment_system) {
            $payment_system_account = strtoupper(getParam($_POST, "lru"));

            if (!preg_match("/^[U]{1}\d{6,7}$/", $payment_system_account)) {
                $validation["valid"] = false;
                $validation["reason"]["lru"] = $message["lru_incorrect"];

                return $validation;
            }
        } elseif ("perfectmoney" == $this->payment_system) {
            $payment_system_account = strtoupper(getParam($_POST, "pmu"));

            if (!preg_match("/^[U]{1}\d{6,7}$/", $payment_system_account)) {
                $validation["valid"] = false;
                $validation["reason"]["pmu"] = $message["pmu_incorrect"];

                return $validation;
            }
        }

        $this->m->_db->setQuery(
             "SELECT COUNT(*)"
           . " FROM `deposits`"
           . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
           . " AND `deposits`.`paysystem_id` != 'MVC' "
           . " AND `deposits`.`paysystem_id` != 'MVCLPWL' "
           . " AND `deposits`.`paysystem_id` != 'MVCMBK' "
           . " AND `deposits`.`paysystem_id` != 'MVCFD' "
           . " AND `deposits`.`paysystem_id` != 'LP' "
           . " AND `deposits`.`paysystem_id` != 'WM' "
           . " AND `deposits`.`paysystem_id` != 'LR' "
           . " AND `deposits`.`paysystem_id` != 'PM' "
           . " LIMIT 1;"
        );

        if ((int)$this->m->_db->loadResult())
            return $validation;

        if ("cc" == $this->payment_system) {
            $this->m->_db->setQuery(
                 "SELECT COUNT(*)"
               . " FROM `deposits`"
               . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
               . " AND (`deposits`.`paysystem_id` = 'MVC' OR `deposits`.`paysystem_id` = 'MVCLPWL' OR `deposits`.`paysystem_id` = 'MVCMBK') "
               . " LIMIT 1;"
            );

            if (0 == (int)$this->m->_db->loadResult()) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["withdrawal_incorrect"];
            }
        } elseif ("liqpay" == $this->payment_system) {
            $this->m->_db->setQuery(
                 "SELECT COUNT(*)"
               . " FROM `deposits`"
               . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
               . " AND `deposits`.`paysystem_id`='LP' "
               . " AND `deposits`.`paysystem_account`=" . $this->m->_db->Quote($payment_system_account)
               . " LIMIT 1;"
            );

            if (0 == (int)$this->m->_db->loadResult()) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["withdrawal_incorrect"];
            }
        } elseif ("webmoney" == $this->payment_system) {
            $this->m->_db->setQuery(
                 "SELECT COUNT(*)"
               . " FROM `deposits`"
               . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
               . " AND `deposits`.`paysystem_id`='WM' "
               . " AND `deposits`.`paysystem_account`=" . $this->m->_db->Quote($payment_system_account)
               . " LIMIT 1;"
            );

            if (0 == (int)$this->m->_db->loadResult()) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["withdrawal_incorrect"];
            }
        } elseif ("libertyreserve" == $this->payment_system) {
            $this->m->_db->setQuery(
                 "SELECT COUNT(*)"
               . " FROM `deposits`"
               . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
               . " AND `deposits`.`paysystem_id`='LR' "
               . " AND `deposits`.`paysystem_account`=" . $this->m->_db->Quote($payment_system_account)
               . " LIMIT 1;"
            );

            if (0 == (int)$this->m->_db->loadResult()) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["withdrawal_incorrect"];
            }
        } elseif ("perfectmoney" == $this->payment_system) {
            $this->m->_db->setQuery(
                 "SELECT COUNT(*)"
               . " FROM `deposits`"
               . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
               . " AND `deposits`.`paysystem_id`='PM' "
               . " AND `deposits`.`paysystem_account`=" . $this->m->_db->Quote($payment_system_account)
               . " LIMIT 1;"
            );

            if (0 == (int)$this->m->_db->loadResult()) {
                $validation["valid"] = false;
                $validation["reason"]["_withdrawal"] = $message["withdrawal_incorrect"];
            }
        }

        if (false == $validation["valid"]) {
            $this->payment_system_array = array(
                "cc" => "MVC",
                "webmoney" => "WM",
                "liqpay" => "LP",
                "libertyreserve" => "LR",
                "perfectmoney" => "PM"
            );

            $withdraw = new StdClass;
            $withdraw->user_id = $this->m->_user->id;
            $withdraw->start_balance = $this->m->_user->balance;
            $withdraw->end_balance = $this->m->_user->balance - $amount;
            $withdraw->cancel_start_balance = $withdraw->end_balance;
            $withdraw->cancel_end_balance = $withdraw->start_balance;
            $withdraw->amount = $amount;
            $withdraw->cancel_reason = iconv("UTF-8","WINDOWS-1251",$message["withdrawal_incorrect"]);
            $withdraw->result = "cancel";
            $withdraw->paysystem_id = strtoupper($this->payment_system_array[$this->payment_system]);
            $withdraw->paysystem_account = $payment_system_account;
            $withdraw->adddate = date("Y-m-d H:i:s");
            $withdraw->resultdate = $withdraw->adddate;

            $this->m->_db->insertObject("withdraws", $withdraw);

            if ((int)$this->m->_user->bad_withdraw_answer) {
                $this->m->_user->bad_withdraw_answer = 0;
                $row->bad_withdraw_answer = 0;
                $row->id = $this->m->_user->id;
                $this->m->_db->updateObject("users", $row, "id");
            }
        }
        return $validation;
    }



    function validation_iprestriction() {
        $message = array (
            "password_empty" => _("You must enter your password!"),
            "password_incorrect" => _("You entered the wrong password!"),
            "answer_empty" => _("You must enter the answer to the question!"),
            "answer_incorrect" => _("You have entered an incorrect answer to the question!"),
            "ip_incorrect" => _("IP-address is not valid!")
        );

        $validation = array( 'valid' => true, 'reason' => array() );

                //$passwd = getParam($_POST, "passwd");
        $answer = getParam($_POST, "answer");

        if ($answer == "") {
            $validation["valid"] = false;
            $validation["reason"]["answer"] = $message["answer_empty"];
        } else {
            if (!$this->m->_auth->checkUserAnswer($answer)) {
                $validation["valid"] = false;
                $validation["reason"]["answer"] = $message["answer_incorrect"];
            } else {
                for ($i = 1; $i <= 3; $i++) {
                    $_POST["ip" . $i] = getParam($_POST, "ip" . $i);
                    if ( strlen($_POST["ip" . $i]) > 0 ) {
                        if (filter_var($_POST["ip" . $i] , FILTER_VALIDATE_IP) == false) {
                            $validation["valid"] = false;
                            $validation["reason"]["ip" . $i] = $message["ip_incorrect"];
                        }
                    }
                }
            }
        }

        return $validation;
    }
    
    public function withdrawal(){
        $this->payment_system = $this->m->_path[2];

            $this->payment_system_array = array(
                "cc" => "MVC",//MVC,MVCLPWL,MVCMBK
                "webmoney" => "WM",
                "liqpay" => "LP",
                "libertyreserve" => "LR",
                "perfectmoney" => "PM"
                );

            $this->withdrawal_startpage = 1;

            if (isset($this->m->_path[2])) {
                
                $this->withdrawal_startpage = 0;
                if (isset($this->payment_system_array[$this->payment_system])) {
                    $needdate = time() - 30 * 3600 * 24;
                    
                    $this->m->_db->setQuery(
                        " SELECT `deposits`.`paysystem_account` "
                        . " FROM `deposits` "
                        . " WHERE `deposits`.`user_id` = " . $this->m->_user->id
                        . " GROUP BY `deposits`.`paysystem_account`;"
                        );
                    $this->payment_system_accounts = count($this->m->_db->loadResultArray());

                    $this->m->_db->setQuery(
                         "SELECT COUNT(*)"
                       . " FROM `withdraws`"
                       . " WHERE `withdraws`.`user_id`=" . $this->m->_user->id
                       . " AND `withdraws`.`result`='ok'"
                       . " AND UNIX_TIMESTAMP(`withdraws`.`adddate`) > '" . $needdate . "'"
                       . " LIMIT 1;"
                    );

                    list($second_withdrawal) = $this->m->_db->loadRow();
                    $second_withdrawal = intval($second_withdrawal);

                    $this->comiss_out = 0.03;
                    $this->comiss_out_static = 0;

                    if ($second_withdrawal) $this->comiss_out_static += 10;
                    if ("cc" == $this->payment_system) $this->comiss_out_static += 2;

                    $this->m->_db->setQuery(
                          " SELECT `deposits`.`paysystem_account` "
                        . " FROM `deposits` "
                        . " WHERE `deposits`.`user_id` = " . $this->m->_user->id
                        . " GROUP BY `deposits`.`paysystem_account`;"
                        );
                    $this->payment_system_accounts = count($this->m->_db->loadResultArray());

                    if ("save" == getParam($_POST, "task", "")) {
                        $validation = $this->validation_withdrawal();
                        
                        if (false == $validation["valid"]) {
                            $_POST["_withdrawal_validation_errors"] = array("messages" => $validation["reason"]);
                            if (isset($validation["reason"]["_withdrawal"])) {
                                $_POST['_withdrawal'] = array('ok' => false, 'message' => $validation["reason"]["_withdrawal"]);
                                $this->withdrawal_startpage = 1;
                                $this->payment_system = "";
                            } else {
                                $_POST['_withdrawal'] = array('ok' => false, 'message' => _("Incorrectly filled fields!"));
                            }

                            $this->amount_with_fee = (int)getParam($_POST, "amount") * (1 - $this->comiss_out) - $this->comiss_out_static;
                        } else {
                            $this->m->_db->setQuery(
                                 " SELECT `deposits`.`amount`, `deposits`.`date` "
                               . " FROM `deposits` "
                               . " WHERE `deposits`.`user_id` = " . $this->m->_user->id
                               . " ORDER BY  `deposits`.`id` DESC "
                               . " LIMIT 1;"
                            );
                            $this->m->_db->loadObject($lastdeposit);
                            $lastdeposit->amount = (int)$lastdeposit->amount;

                            $this->m->_db->setQuery(
                                 " SELECT SUM(`user_games`.`bet`) "
                               . " FROM `user_games` "
                               . " WHERE `user_games`.`user_id` = " . $this->m->_user->id
                               . " AND `user_games`.`date` > " . $this->m->_db->Quote($lastdeposit->date)
                               . " LIMIT 1;"
                            );
                            $lastbets = (int)$this->m->_db->loadResult();

                            $addwithdraw = false;
                            if ($lastbets >= $lastdeposit->amount) {
                                $this->m->_db->setQuery(
                                     " SELECT SUM(`deposits`.`amount`) "
                                   . " FROM `deposits` "
                                   . " WHERE `deposits`.`user_id` = " . $this->m->_user->id
                                   . " LIMIT 1;"
                                );
                                $alldeposits = (int)$this->m->_db->loadResult();

                                $this->m->_db->setQuery(
                                     " SELECT SUM(`user_games`.`bet`) "
                                   . " FROM `user_games` "
                                   . " WHERE `user_games`.`user_id` = " . $this->m->_user->id
                                   . " LIMIT 1;"
                                );
                                $allbets = (int)$this->m->_db->loadResult();

                                if ($allbets >= $alldeposits) {
                                    $addwithdraw = true;
                                }
                            }

                            if ($addwithdraw) {
                                $this->amount = floatval(str_replace(",", ".", getParam($_POST, "amount", 0))) * 100;

                                if ("cc" == $this->payment_system)
                                    $this->payment_system_account = getParam($_POST, "cardnumber");
                                elseif ("liqpay" == $this->payment_system)
                                    $this->payment_system_account = getParam($_POST, "phone");
                                elseif ("webmoney" == $this->payment_system)
                                    $this->payment_system_account = strtoupper(getParam($_POST, "wmz"));
                                elseif ("libertyreserve" == $this->payment_system)
                                    $this->payment_system_account = strtoupper(getParam($_POST, "lru"));
                                elseif ("perfectmoney" == $this->payment_system)
                                    $this->payment_system_account = strtoupper(getParam($_POST, "pmu"));

                                $withdraw = new StdClass;
                                $withdraw->user_id = $this->m->_user->id;
                                $withdraw->start_balance = $this->m->_user->balance;
                                $withdraw->end_balance = $this->m->_user->balance - $this->amount;
                                $withdraw->amount = $this->amount;
                                $withdraw->fee = $this->amount * $this->comiss_out + $this->comiss_out_static * 100;
                                $withdraw->result = "add";
                                $withdraw->paysystem_id = strtoupper($this->payment_system_array[$this->payment_system]);
                                $withdraw->paysystem_account = $this->payment_system_account;
                                $withdraw->adddate = date("Y-m-d H:i:s");

                                if ($this->m->_db->insertObject("withdraws", $withdraw)) {
                                    $this->m->_db->setQuery(
                                        " UPDATE `users` SET "
                                      . " `users`.`balance` = " . $withdraw->end_balance
                                      . " WHERE `users`.`id` = " . $this->m->_user->id
                                      . " LIMIT 1;"
                                    );

                                    if ($this->m->_db->query()) {
                                        $_POST['_withdrawal'] = array('ok' => true, 'message' => _("Your request for withdrawal received!"));
                                        $this->m->_user->balance = $withdraw->end_balance;
                                        $this->withdrawal_startpage = 1;
                                        $this->payment_system = "";

                                        if ((int)$this->m->_user->bad_withdraw_answer) {
                                            $this->m->_user->bad_withdraw_answer = 0;
                                            $row->bad_withdraw_answer = 0;
                                            $row->id = $this->m->_user->id;
                                            $this->m->_db->updateObject("users", $row, "id");
                                        }
                                    } else
                                        $_POST['_withdrawal'] = array('ok' => false, 'message' => _("An unexpected error occurred. Please try again later."));
                                } else
                                    $_POST['_withdrawal'] = array('ok' => false, 'message' => _("An unexpected error occurred. Please try again later."));
                            } else {
                                $_POST['_withdrawal'] = array('ok' => false, 'message' => _("The amount of your bets is less than the amount of your deposit."));
                            }
                        }
                    }
                } else $_POST['_withdrawal'] = array( 'ok' => false, 'message' => _("Does not selected a payment system!") );
            }

            if ($this->withdrawal_startpage) {
                $this->m->_db->setQuery(
                    "SELECT `adddate`,`amount`,`paysystem_id`,`result`,`cancel_reason`"
                  . " FROM `withdraws`"
                  . " WHERE"
                  . " `user_id` = '" . $this->m->_user->id . "'"
                  . " ORDER BY `adddate` DESC"
                  . " LIMIT 5;"
                );

                $this->rows = $this->m->_db->loadObjectList();
            }

    }
    
    public function password(){
            $save = getParam($_POST, "save", "");

            if ($save == "1") {
                // Меняем пароль
                $validation = $this->validation_password("password");

                if (!$validation["valid"]) {
                    $_POST["_password_validation_errors"] = array( "messages" => $validation["reason"] );
                    $_POST['_password'] = array( 'ok' => false, 'message' => _("Incorrectly filled fields!") );
                } else {
                    $row = new User($this->m);
                    $NewPassword = getParam($_POST, "NewPassword");

                    $salt = makePassword(16);
                    $crypt = md5(md5($NewPassword).$salt);

                    $row->password = $crypt . ':' . $salt;
                    $row->id = $this->m->_user->id;

                    if ($row->store()) {
                        $this->m->add_to_history($this->m->_user->id, "account", "changedpassword");
                        unset($_POST);
                        $_POST['_password'] = array( 'ok' => true, 'message' => _("Password changed!"));
                    } else {
                        $_POST['_password'] = array( 'ok' => false, 'message' => _("An unexpected error occurred. Please try again later.") );
                    }
                }
            } elseif ($save == "2") {
                // Меняем контрольный вопрос
                $validation = $this->validation_password("question");

                if (!$validation["valid"]) {
                    $_POST["_password_validation_errors"] = array( "messages" => $validation["reason"] );
                    $_POST['_password'] = array( 'ok' => false, 'message' => _("Incorrectly filled fields!") );
                } else {
                    $row = new User($this->m);
                    $row->id = $this->m->_user->id;

                    $row->question = getParam($_POST, "question");
                    $row->answer = iconv("UTF-8","WINDOWS-1251",getParam($_POST, "answer"));

                    if ($row->store()) {
                        $this->m->add_to_history($this->m->_user->id, "account", "changedsecurityquestion");
                        unset($_POST);
                        $_POST['_password'] = array( 'ok' => true, 'message' => _("Your secret question successfully updated.") );
                        $this->m->_user->question = $row->question;
                    } else {
                        $_POST['_password'] = array( 'ok' => false, 'message' => _("An unexpected error occurred. Please try again later.") );
                    }
                }
            }
    }
    
    /*public function personal_info(){
        $this->m->_db->setQuery("SELECT `alpha2`,`title_ru` FROM country WHERE status = 1 ORDER BY title_ru");
        $this->countries = $this->m->_db->loadObjectList();

        $task = getParam($_POST, "task", "");
        if ($task != "save") {
            return;
        }

        $validation = $this->validation_personalinfo();

        if ( ! $validation["valid"] ) {
            $_POST["_personalinfo_validation_errors"] = array( "messages" => $validation["reason"] );
            $_POST['_personalinfo'] = array( 'ok' => false, 'message' => _("Incorrectly filled fields!") );
        } else {
            $row = new User($this->m);
            $_POST["adress"] = $_POST["street"];
            unset($_POST["street"]);
            unset($_POST["answer"]);
            if ( (! empty($_POST["new_email"]) && empty($this->m->_user->new_email_validation_code)) || ( ! empty($_POST["new_email"]) && empty($_POST["new_email_validation_code"]) && $_POST["new_email"] != $user->new_email && $_POST["new_email"] != $this->m->_user->email) ) {
                // Генерируем проверочный код для смены емейла
                $_POST["new_email_validation_code"] = strtolower(makePassword(6));
                $new_email = $_POST["new_email"];
            } elseif ( ! empty($_POST["new_email"]) && $_POST["new_email"] == $this->m->_user->new_email &&
                       ! empty($_POST["new_email_validation_code"]) && $_POST["new_email_validation_code"] == $this->m->_user->new_email_validation_code &&
                       $_POST["new_email"] != $this->m->_user->email ) {
                $_POST["email"] = $_POST["new_email"];
                $_POST["new_email"] = "";
                $_POST["new_email_validation_code"] = "";
            } else {
                unset($_POST["new_email_validation_code"]);
            }

            $row->bind($_POST);
            $row->city = iconv("UTF-8", "WINDOWS-1251", $row->city);
            $row->adress = iconv("UTF-8", "WINDOWS-1251", $row->adress);

            $row->id = (int)$this->m->_user->id;
            if (empty($row->new_email) && !empty($this->m->_user->new_email_validation_code))
                $row->new_email_validation_code = "";

            if ($row->store()) {
                if (isset($new_email)) {
                    include(XPATH_TEMPLATE_FRONT . DS . "modules" . DS . "newemailvalidationcode.php");
                    sendemail($this->m->_user->email, $mailsubject, $mailbody_html, $mailbody_txt);
                }
                unset($_POST);

                if (!empty($row->email)) {
                    $this->m->_user->email = $row->email;
                    $this->m->_user->new_email = "";
                    $this->m->_user->new_email_validation_code = "";
                }

                if (!empty($row->new_email)) {
                    $this->m->_user->new_email = $row->new_email;
                    $this->m->_user->new_email_validation_code = $row->new_email_validation_code;
                } elseif (!empty($this->m->_user->new_email_validation_code)) {
                    $this->m->_user->new_email = "";
                    $this->m->_user->new_email_validation_code = "";
                }

                $this->m->_user->adress    = iconv("WINDOWS-1251","UTF-8",$row->adress);
                $this->m->_user->zip       = $row->zip;
                $this->m->_user->city      = iconv("WINDOWS-1251","UTF-8",$row->city);
                $this->m->_user->country   = $row->country;
                $this->m->_user->phone     = $row->phone;

                $this->m->add_to_history($this->m->_user->id, "account", "changedpersonalinfo");

                $_POST['_personalinfo'] = array( 'ok' => true, 'message' => _("Personal data is updated!"));
            } else {
                $_POST['_personalinfo'] = array( 'ok' => false, 'message' => _("An unexpected error occurred. Please try again later."));
            }
        }
    }*/
    
    public function transactions(){
        if (isset($_POST["numDays"]) || !array_key_exists($this->m->_user->id . ".transactions.numDays", $_SESSION)) {
            $numDays = intval(getParam($_POST, "numDays", $this->numDaysArray[0]));
            if (!in_array($numDays, $this->numDaysArray))
                $numDays = $this->numDaysArray[0];
            $_SESSION[$this->m->_user->id . ".transactions.numDays"] = $numDays;
        } elseif (array_key_exists($this->m->_user->id . ".transactions.numDays", $_SESSION)) {
            $numDays = $_SESSION[$this->m->_user->id . ".transactions.numDays"];
        }

        if (isset($_POST["onPage"]) || !array_key_exists($this->m->_user->id . ".transactions.onPage", $_SESSION)) {
            $onPage = intval(getParam($_POST, "onPage", $this->roundOnPageArray[0]));
            if (!in_array($onPage, $this->roundOnPageArray))
                $onPage = $this->roundOnPageArray[0];
            $_SESSION[$this->m->_user->id . ".transactions.onPage"] = $onPage;
        } elseif (array_key_exists($this->m->_user->id . ".transactions.onPage", $_SESSION)) {
            $onPage = $_SESSION[$this->m->_user->id . ".transactions.onPage"];
        }

        $page = isset($this->m->_path[2]) ? (int) $this->m->_path[2] : 1;

        $start = ($page-1)*$onPage;

        foreach($this->numDaysArray as $v) {
            $this->numDaysRadio[$v] = ($v==$numDays) ? " checked=\"true\"" : " ";
        }
        foreach($this->roundOnPageArray as $v) {
            $this->roundOnPageList[$v] = ($v==$onPage) ? " selected=\"true\"" : "";
        }

        $needdate = time() - $numDays*3600*24;

        $this->m->_db->setQuery(
                "SELECT COUNT(*)"
              . " FROM `deposits`"
              . " WHERE `user_id`=" . $this->m->_user->id
              . " AND UNIX_TIMESTAMP(`date`) > '$needdate'"
            );

        $total["deposit"] = $this->m->_db->loadResult();

        $this->m->_db->setQuery(
                "SELECT COUNT(*)"
              . " FROM `withdraws`"
              . " WHERE `user_id`=" . $this->m->_user->id
              . " AND UNIX_TIMESTAMP(`adddate`) > '$needdate'"
            );
        $total["withdraw"] = $this->m->_db->loadResult();

        $totalall = $total["deposit"] + $total["withdraw"];

        $query = "(SELECT `id`, `user_id`, `start_balance`, `end_balance`, `amount`, `transaction_id`, `result`, `paysystem_id`, `date` FROM `deposits` WHERE `user_id`=" . $this->m->_user->id . " AND UNIX_TIMESTAMP(`date`) > '$needdate' )"
               . "\n UNION "
               . "\n(SELECT `id`, `user_id`, `start_balance`, `end_balance`, (-1)*`amount` as amount, `transaction_id`, `result`, `paysystem_id`, `adddate` as `date` FROM `withdraws` WHERE `user_id`=" . $this->m->_user->id . " AND `result` = 'ok' AND UNIX_TIMESTAMP(`adddate`) > '$needdate' )"
               . "\n ORDER BY `date` DESC"
               ;

        $this->m->_db->setQuery($query, $start, $onPage);

        $rows = $this->m->_db->loadObjectList();

        if (! empty($rows) ) {
            foreach($rows as $r) {
                $this->transactions[$r->date] = $r;
            }
            if (isset($this->transactions))
                krsort($this->transactions);
        }

        $this->pager = $this->getPages($totalall, $start, $onPage, $page, "account/transactions");

    }
    public function deposit() {
        $this->payment_system = $this->m->_path[2];

        $this->payment_system_array = array(
            "cc" => array("MVC", "min" => 20, "max" => 250),
            "webmoney" => array("WM", "min" => 20, "max" => 1000),
            "liqpay" => array("LP", "min" => 20, "max" => 1000),
            "libertyreserve" => array("LR", "min" => 20, "max" => 1000),
            "perfectmoney" => array("PM", "min" => 20, "max" => 1000),
            "moneymail" => array("MM", "min" => 20, "max" => 1000),
            "rbkmoney" => array("RBK", "min" => 20, "max" => 1000),
            "zpayment" => array("ZP", "min" => 20, "max" => 1000),
            "w1" => array("W1", "min" => 20, "max" => 1000),
            "qiwi" => array("QIWI", "min" => 20, "max" => 250),
            "yandexmoney" => array("YM", "min" => 20, "max" => 1000),
            "webcreds" => array("WC", "min" => 20, "max" => 1000)
            );

        if ("POST" == $_SERVER['REQUEST_METHOD'] && null == getParam($_POST, "task")) {
            $_POST["STATUS"] = getParam($_POST, "STATUS");
            if ($_POST["STATUS"] <= 0)
                $_POST['_deposit'] = array( 'ok' => false, 'message' => _("You have refused to pay, or an error!") );
            return;
        }

        if ("cancel" == getParam($_GET, "s")) {
            $_POST['_deposit'] = array( 'ok' => false, 'message' => _("You have refused to pay, or an error!") );
            return;
        }

        if (!empty($this->payment_system) && !isset($this->payment_system_array[$this->payment_system])) {
            $_POST['_deposit'] = array( 'ok' => false, 'message' => _("Payment system does not choose !") );
            return;
        }
        
        $this->m->_db->setQuery(
            "SELECT COUNT(*)"
            . " FROM `deposits`"
            . " WHERE `user_id`=" . $this->m->_user->id
            );
        $this->first_deposit = intval($this->m->_db->loadResult());
        
        if ("save" == getParam($_POST, "task", "")) {
            $this->amount = (int)getParam($_POST, "amount", 0);
            $this->give_bonus = ($this->amount < 20) ? 0 : getParam($_POST, "give_bonus", 0);

            if ($this->amount >= $this->payment_system_array[$this->payment_system]["min"] && $this->amount <= $this->payment_system_array[$this->payment_system]["max"]) {
                $order_nr =  $this->m->_user->username . "-" . time();
                $description = _("Deposit accounts for") . $this->m->_user->username;
                $html = '<div>' . _("Now you will be redirected to the payment gateway chosen method of payment") . '.</div><br />'
                      . '<div>' . _("Follow all instructions given during the operation of payment. Do not close your browser window until payment is fully made.") . '</div><br />'
                      . '<div>' . _("The amount of recharge USD") . ' <b>' . sprintf('%0.2f', $this->amount) . '</b></div><br />'
                      . '<div class="right">'
                      . '<input class="buttonDefault" type="submit" value="' . _("Continue") . '">'
                      . '</div>'
                      ;

                $xml = "<request>"
                     . "<merchant_id>" . $this->m->config->merchantId . "</merchant_id>"
                     . "<order_id>" . $order_nr . "</order_id>"
                     . "<amount>" . sprintf('%0.2f', $this->amount) . "</amount>"
                     . "<currency>USD</currency>"
                     . "<description></description>"
                     . "<language>ru</language>"
                     . "<system>" . ("cc" == $this->payment_system ? "card" : $this->payment_system) . "</system>"
                     . "<bonus>" . $this->give_bonus . "</bonus>"
                     . "</request>"
                     ;
                $xml_encoded = base64_encode($xml);
                $signature = base64_encode(sha1($this->m->config->merchantSign . $xml . $this->m->config->merchantSign, 1));

                $this->payment_form  = '<form method="POST" action="http://24change.net/change.php" target="_parent" name="ExpressMerchants" id="ExpressMerchants">'
                                     . '<input type="hidden" name="operation_xml" value="' . $xml_encoded . '" />'
                                     . '<input type="hidden" name="signature" value="' . $signature .'" />'
                                     . $html
                                     . '</form>'
                                     ;
                
            } else {
                if ($this->amount < $this->payment_system_array[$this->payment_system]["min"]) {
                    $_POST["_deposit_validation_errors"] = array("messages" => array("amount" => _("The amount is less than the minimum!")));
                } elseif ($this->amount > $this->payment_system_array[$this->payment_system]["max"]) {
                    $_POST["_deposit_validation_errors"] = array("messages" => array("amount" => _("Sum greater than the maximum!")));
                }
                $_POST["_deposit"] = array("ok" => false, "message" => _("Incorrectly filled fields!"));
            }
        }
    }
    public function iprestriction(){
        $this->user_allow_ip = explode("\n", $this->m->_user->allow_ip);

        $save = getParam($_POST, "save", 0);

        if ($save) {
            $validation = $this->validation_iprestriction();

            if ( ! $validation["valid"] ) {
                $_POST["_iprestriction_validation_errors"] = array( "messages" => $validation["reason"] );
                $_POST['_iprestriction'] = array( 'ok' => false, 'message' => _("Incorrectly filled fields!")  );
            } else {
                $allow_ip = array();
                for ($i = 1; $i <= 3; $i++) {
                    $allow_ip[] = $_POST["ip" . $i];
                }

                $row = new User($this->m);

                $row->allow_ip = implode("\n", $allow_ip);
                $row->id = $this->m->_user->id;

                if ($row->store()) {
                    unset($_POST);
                    $_POST['_iprestriction'] = array( 'ok' => true, 'message' => _("IP-limit saved!") );
                    $this->user_allow_ip = $allow_ip;
                } else {
                    $_POST['_iprestriction'] = array( 'ok' => false, 'message' => _("An unexpected error occurred. Please try again later.") );
                }
            }
        }
    }
    public function summary() {
        if ("POST" == $_SERVER['REQUEST_METHOD']) {
            if (getParam($_POST, "getbonus") == "true") {
                // Получаем последний депозит
                $this->m->_db->setQuery(
                     "SELECT `id`,`amount`"
                   . " FROM `deposits`"
                   . " WHERE `deposits`.`user_id`=" . $this->m->_user->id
                   . " ORDER BY `id` DESC"
                   . " LIMIT 1;"
                );

                list($deposits_id, $amount) = $this->m->_db->loadRow();

                if ($amount < 2000) {
                    $this->m->_db->setQuery(
                        "UPDATE `users` SET"
                      . " `users`.`show_popup_bonus` = '0'"
                      . " WHERE `users`.`id` = " . $this->m->_db->Quote($this->m->_user->id)
                      . " AND `users`.`club_id` >= ".$this->m->config->club_id_start." AND `users`.`club_id` <=" . $this->m->config->club_id_end
                      . " LIMIT 1;"
                    );
                    $this->m->_db->query();

                    $this->m->_user->show_popup_bonus = 0;
                    $deposits_id = 0;
                }

                if ($deposits_id) {
                    $this->m->_db->setQuery(
                         "SELECT `id`"
                       . " FROM `user_bonus`"
                       . " WHERE `user_bonus`.`user_id`=" . $this->m->_user->id
                       . " AND `user_bonus`.`deposit_id`='" . $deposits_id . "'"
                       . " LIMIT 1;"
                    );

                    list($user_bonus_id) = $this->m->_db->loadRow();

                    if (empty($user_bonus_id)) {
                        // Проверяем какой бонус давать

                        $this->m->_db->setQuery(
                            "SELECT COUNT(*)"
                          . " FROM `deposits`"
                          . " WHERE `user_id`=" . $this->m->_user->id
                        );

                        $bonus_id = ($this->m->_db->loadResult() >= 2 ? 2 : 1); // 1 = на первый депозит; 2 = на повторный депозит

                        $this->m->_db->setQuery(
                            "SELECT * "
                          . " FROM `bonuses`"
                          . " WHERE `id`=" . $bonus_id
                          . " AND `status` = 1"
                          . " LIMIT 1;"
                        );

                        $this->m->_db->loadObject($bonus);

                        if (is_object($bonus)) {
                            $bonus_sum = floor($amount * $bonus->bonus_percent / 100);
                            if ($bonus_sum > $bonus->max_sum)
                                $bonus_sum = $bonus->max_sum;

                            $query = "INSERT INTO `user_bonus` (`user_id`, `bonus_id`, `deposit_id`, `wager`, `bonus_sum`, `blocked_sum`, `start_balance`, `end_balance`, `rest_bet`, `total_bet`, `status`, `start_date`)"
                                   . " VALUES ("
                                   . " '" . $this->m->_user->id . "',"
                                   . " '" . $bonus->id . "',"
                                   . " '" . $deposits_id . "',"
                                   . " '" . $bonus->wager . "',"
                                   . " '" . $bonus_sum . "',"
                                   . " '" . $amount . "',"
                                   . " '" . $this->m->_user->balance . "',"
                                   . " '" . ($this->m->_user->balance + $bonus_sum). "',"
                                   . " '" . ($bonus_sum * $bonus->wager) . "',"
                                   . " '" . ($bonus_sum * $bonus->wager) . "',"
                                   . " '1',"
                                   . " NOW()"
                                   . " );"
                                   ;

                            $this->m->_db->setQuery($query);
                            if ($this->m->_db->query()) {
                                $this->m->_db->setQuery(
                                    "UPDATE `users` SET"
                                  . " `users`.`balance` = '" . intval($this->m->_user->balance + $bonus_sum) . "'"
                                  . ",`users`.`show_popup_bonus` = '0'"
                                  . " WHERE `users`.`id` = '" . $this->m->_user->id . "'"
                                  . " LIMIT 1;"
                                );
                                $this->m->_db->query();

                                $this->m->_user->balance += $bonus_sum;
                                $this->m->_user->show_popup_bonus = 0;
                            }
                        }
                    }
                }
            } elseif (getParam($_POST, "getbonus") == "false") {
                $this->m->_db->setQuery(
                    "UPDATE `users` SET"
                  . " `users`.`show_popup_bonus` = '0'"
                  . " WHERE `users`.`id` = '" . $this->m->_user->id . "'"
                  . " AND `users`.`club_id` >= ".$this->m->config->club_id_start." AND `users`.`club_id` <=" . $this->m->config->club_id_end
                  . " LIMIT 1;"
                );
                $this->m->_db->query();

                $this->m->_user->show_popup_bonus = 0;
            } else {
                $_POST["STATUS"] = getParam($_POST, "STATUS");
                $_POST["AMOUNT"] = getParam($_POST, "AMOUNT");
                if (1 == $_POST["STATUS"]) {
                    $_POST['_summary'] = array( 'ok' => true, 'message' => _("Balance credited to USD ") . number_format($_POST["AMOUNT"], 2, ".", " ") );
                }
            }
        }

        if ("success" == (int)getParam($_GET, "s") && (float)getParam($_GET, "a") > 0) {
            $_POST['_summary'] = array( 'ok' => true, 'message' => _("Balance credited to USD ") . number_format(getParam($_GET, "a"), 2, ".", " ") );
        }
        ///Начинает работу Пагинатор
        if (isset($_POST["numDays"]) || !array_key_exists($this->m->_user->id . ".bonus.numDays", $_SESSION)) {
            $numDays = intval(getParam($_POST, "numDays", $this->numDaysArray[0]));
            if (!in_array($numDays, $this->numDaysArray))
                $numDays = $this->numDaysArray[0];
            $_SESSION[$this->m->_user->id . ".bonus.numDays"] = $numDays;
        } elseif (array_key_exists($this->m->_user->id . ".bonus.numDays", $_SESSION)) {
            $numDays = '60';
            //$numDays = $_SESSION[$this->m->_user->id . ".bonus.numDays"];
        }

        if (isset($_POST["onPage"]) || !array_key_exists($this->m->_user->id . ".bonus.onPage", $_SESSION)) {
            $onPage = intval(getParam($_POST, "onPage", $this->roundOnPageArray[0]));
            if (!in_array($onPage, $this->roundOnPageArray))
                $onPage = $this->roundOnPageArray[0];
            $_SESSION[$this->m->_user->id . ".bonus.onPage"] = $onPage;
        } elseif (array_key_exists($this->m->_user->id . ".bonus.onPage", $_SESSION)) {
            $onPage = $_SESSION[$this->m->_user->id . ".bonus.onPage"];
        }

        $page = isset($this->m->_path[2]) ? (int) $this->m->_path[2] : 1;

        $start = ($page-1)*$onPage;
        foreach($this->numDaysArray as $v) {
            $this->numDaysRadio[$v] = ($v==$numDays) ? " checked=\"true\"" : " ";
        }

        foreach($this->roundOnPageArray as $v) {
            $this->roundOnPageList[$v] = ($v==$onPage) ? " selected=\"true\"" : "";
        }

        $needdate = time() - $numDays*3600*24;

        $where = " AND UNIX_TIMESTAMP(`user_bonus`.`start_date`) > '$needdate'";

        $this->m->_db->setQuery("SELECT COUNT(`user_bonus`.`user_id`) FROM `user_bonus` WHERE `user_bonus`.`user_id`=" . $this->m->_user->id . $where);
        $total = $this->m->_db->loadResult();

        $query = "SELECT `user_bonus`.*, `bonuses`.`name`, `bonuses`.`description`"
               . "\n FROM `user_bonus`"
               . "\n LEFT JOIN `bonuses` ON `user_bonus`.`bonus_id` = `bonuses`.`id`"
               . "\n WHERE `user_bonus`.`user_id` = " . $this->m->_user->id
               . $where
               . "\n ORDER BY `user_bonus`.`start_date` DESC"
               ;

        $this->m->_db->setQuery($query, $start, $onPage);
        $this->rows = $this->m->_db->loadObjectList();

        $this->pager = $this->getPages($total, $start, $onPage, $page, "account/bonus");
        // END Вывод выданных бонусов
    }

    function getPages($total, $start, $limit, $page, $link) {
        $total_pages = $limit ? ceil( $total / $limit ) : 0;
        $this_page = $limit ? ceil( ($start+1) / $limit ) : 1;

        $txt = '<span class="pages">'. _("Page ") . $page . ' из ' . $total_pages . ' &nbsp; / &nbsp; </span>';

        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                $txt .= '<span class="pagenav">['. $i .']</span> ';
            } else {
                if ($i == 1)
                    $txt .= '<a href="/'. $link .'" class="pagenav"><strong>'. $i .'</strong></a> ';
                else
                    $txt .= '<a href="/'. $link .'/'. $i.'" class="pagenav"><strong>'. $i .'</strong></a> ';
            }
        }

        return $txt;
    }
}
?>