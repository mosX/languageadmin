<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <?= $this->header() ?>
        <?= $this->css() ?>
        <?= $this->js() ?>        
    </head>

    <body>
        <style>
            .content{
                display:flex;
                overflow: hidden;
                padding-left:65px;  
                position:fixed;
                left:0px;
                top:0px;
                width:100%;
                height:100%;
                background: green;
            }
            .content .left{
                width:300px;
                background:white;
                float:left;                
            }
            .content .right{
                background: #c6c6c6;
                flex:1;
            }
            .content .popup{
                position:absolute;
                left:2200px;
                top:0px;
                width:100%;
                height:100%;
                background: red;
                z-index: 100;
            }
        </style>
        <style>
            .page_left{       
                width:256px;
                height:100%;        
                padding:10px 20px ;        
                background:white;
            }
            .page_left h2{
                font-size:24px;
                margin-bottom:20px;
            }
            .page_left ul{
                margin:0px;
                padding:0px;
            }
            .page_left ul li{

                border-bottom: 1px solid #d6d8dc;
            }
            .page_left ul li a{
                cursor:pointer;
                text-decoration: none;
                display:block;
                color: #313942;
                font-size: 15px;
                width:100%;        
                height:100%;
                padding: 15px 5px;
            }
            .page_left ul li.active a{
                color: #1b94d7;
                font-weight: bold;
            }

            .page_right{                    
                flex:1;
                background: #c6c6c6;
                
                height:100%;
                padding:10px 20px;
            }
            .page_right h2{
                font-size:24px;
                margin-bottom:20px;
            }
        </style>
        <?=$this->module('svg')?>
        <?=$this->module('sidebar') ?>
        <div class="content">            
            <div class="page_left">
                <h2>Почта</h2>

                <ul>
                    <li class='active'>
                        <a hreh="">Входящие</a>
                    </li>
                    <li>
                        <a hreh="">Исходящие</a>
                    </li>
                    <li>
                        <a hreh="">Удаленные</a>
                    </li>
                </ul>

            </div>
            <div class="page_right">
                <h2>Входящие</h2>

                <div class="table table-hover">
                    <div class="tr">
                        <div class="th" style="width:37px;">

                        </div>
                        <div class="th">
                            от
                        </div>
                        <div class="th">
                            ТЕМА
                        </div>
                        <div class="th">
                            КОНТАКТ
                        </div>
                        <div class="th">
                            ДАТА
                        </div>
                    </div>
                    <?php  foreach($this->m->data as $item){ ?>
                        <div class="tr" data-id='<?=$item->id?>'>
                            <div class="td">
                                <label class='checkbox'>
                                    <input type="checkbox" class="action_panel_triger">
                                    <div class='box'></div>
                                </label>
                            </div>
                            <div class="td">
                                <div class="actions_panel">
                                    <a class="reply" href=""><span></span>ответить</a>
                                    <a class="del_user" href=""><span></span>удалить</a>
                                </div>
                                <?=$item->from_sendername?>
                            </div>
                            <div class="td">
                                <a href><?=$item->subject?></a>
                            </div>
                            <div class="td">

                            </div>
                            <div class="td">
                                <?=date("Y-m-d H:i",strtotime($item->date_receive))?>                    
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
    <div class="popup">
                23423423423
                <div class='readletter_page'>
                    <div style='margin-bottom:20px;'>
                        <div class='back_btn'></div>
                        <h2>второе письмо</h2>
                    </div>


                </div>

            </div>
        </div>
            
        
    </body>
</html>