<?php
$config = array(
    "host" => "localhost",
    "user" => "root",
    "pass" => "killer1906",
    //"db"   => "amocrm",
    "db"   => "languageworkshop",

    "email" => "support@binsecret.com",
    "sendername" => "BinSecret",
    "smtp_host"  => "localhost",
    
    "assets_path" =>'C:\USR\www\languageadmin\assets',
    "assets_url" =>'http://languageadmin/assets',

    "paySystems" => array(
        'MVC-SP' => 'Visa/Mastercard',
        'WM' => 'WebMoney',
        'LP' => 'LiqPay',
        'LR' => 'Libertyreserved',
        'PM' => 'PerfectMoney',
        'MM' => 'MoneyMail',
        'RBK' => 'RBKmoney',
        'ZP' => 'Zpayment',
        'W1' => 'WalletOne',
        'QIWI' => 'QIWI',
        'YM' => 'YandexMoney',
        'WC' => 'WebCreds',
        'NETELLER'=>'NETELLER',
        'MVC' => 'Visa/Mastercard'
        ),

    'phoneTypes' => array(
        '1'=>'Раб.тел',
        '2'=>'Раб.прямой',
        '3'=>'Мобильный',
        '4'=>'Факс',
        '5'=>'Домашний',
        '6'=>'Другой'
    ),
    
    'emailTypes' => array(
        '1'=>'Email раб.',
        '2'=>'Email личн.',
        '3'=>'Email др.'
    ),
    
    'messangerTypes' => array(
        '1'=>'Skype',
        '2'=>'ICQ',
        '3'=>'Jabber',
        '4'=>'Google Talk',
        '5'=>'MSN',
        '6'=>'Другое'
    ),

    'defaultlang' => 'en',
    'available_languages' => array("en","sv","fi","es","ru","de","pl","zh-chs","da"),

    'serverWS' => 'ws://ws.binsecret.com:8888'
);

//$this->addCSS("style")->addCSS("colorbox")->addCSS('jquery.cluetip')->addCSS('flags');
//$this->preAddJS('jquery-1.8.3.min')-> addJS('main');
?>