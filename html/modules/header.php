<style>
    #header{
        display:block;
        width:100%;
        background:#273135;
        height: 60px;
    }
    #header ul.menu{        
        font-size: 0px;
        height: 100%;        
        display:inline-block;
        vertical-align: top;
    }
    #header .menu li{
        position:relative;
        font-size: 16px;
        display:inline-block;
        font-weight:600;
        vertical-align: middle;
        height:100%;        
    }
    #header .menu li a{        
        display:block;
        color: white;
        height: 100%;
        width:100%;
        padding: 20px 20px;
        
        border-bottom:5px solid transparent;
    }
    #header .menu li:hover a{
        text-decoration: none;
        background: rgba(0,0,0,0.3);
    }
    #header .menu li.active a{
        background: rgba(0,0,0,0.8);
        border-bottom:5px solid #E76049;
    }
    
    #header .right_panel{
        width:auto;
        float:right;
        height: 100%;
    }
    #header .right_panel ul{
        padding:0px;
        margin:0px;
        height: 100%;
        width:100%;
    }
    #header .right_panel ul li{
        position:relative;
        display:inline-block;
        vertical-align: top;
    }
    #header .right_panel ul li .logout{
        position:relative;
        
        display:block;        
        width:60px;
        height: 60px;;
        background: #E76049;
        font-family: fontello;
    }
    
    #header .right_panel ul li .logout:before{
        text-align: center;
        font-size: 36px;
        width:100%;
        height: 100%;
        line-height: 60px;
        color: white;
        display:block;
        position:absolute;
        content:'\EF30';
    }
    
    #header .menu .dropdown-toggle{
        position:relative;
    }
    #header .menu .dropdown-toggle:hover .dropdown-menu{
        display:block;        
    }
    
    #header .menu .dropdown-menu{        
        height:auto;
        width:220px;
        background: #273135;        
        position:absolute;
        left:0px;        
        top:58px;
        padding:20px 0px;
        
    }
    #header .menu .dropdown-menu ul{
        height:auto;
        padding:0px;
        margin:0px;
    }
    #header .menu .dropdown-menu li{
        display:block;
        background:none;
        height: 25px;
        
    }
    #header .menu .dropdown-menu li a{
        padding:0px;
        font-size: 12px;
        height: 100%;
        color: #89949B;
        background:none;
        padding-left:20px;
        padding-right:20px;
        padding-top:4px;
        border:none;
    }
    #header .menu .dropdown-menu li:hover a{
        background: rgba(0,0,0,0.3);
    }
</style>
<div id="header">
    <ul class='menu'>
         <li class='dropdown-toggle <?=in_array($this->_controller, ['collections','genres','radio','logos','cms','channels','banners','pages','epg']) ? 'active':''?>'>
            <a class='' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="">Обучение</a>
            <div class="dropdown-menu">
                <ul>
                    <li><a href="/lessons/">Уроки</a></li>
                    <li><a href="/lessons/questions/">Вопросы</a></li>
                    <li><a href="/lessons/answers/">Ответы</a></li>
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                </ul>
            </div>
        </li>
        
        <li class='dropdown-toggle <?=in_array($this->_controller, ['contacts','tasks','mail','finance']) ? 'active':''?>'>
            <a class='' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="/channels/">Расписание</a>
            
            <div class="dropdown-menu">
                <!--<ul>
                    <li><a href="/finance/payments/">Payments</a></li>
                    <li><a href="/contacts/">Подписчики</a></li>
                    <li><a href="/operators/">Operators/Dealers(pending)</a></li>
                    <li><a href="/rightholders/">RightHolders(pending)</a></li>
                                        
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                                        
                    <li><a href="/tasks/">Задачи</a></li>
                    <li><a href="/mail/">Почта</a></li>
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                </ul>-->
            </div>
        </li>
                
        <li class='dropdown-toggle <?=in_array($this->_controller, ['system']) ? 'active':''?>'>
            <a class='' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="">Управление</a>
            <div class="dropdown-menu">
                <ul>
                    <li><a href="/system/admins/">Пользователи и роли</a></li>
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                </ul>
            </div>
        </li>
        
        <li class='dropdown-toggle <?=in_array($this->_controller, ['system']) ? 'active':''?>'>
            <a class='' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="">Обратная Связь</a>
            <div class="dropdown-menu">
                <ul>
                    <li><a href="/feedback/">Просмотреть</a></li>
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                </ul>
            </div>
        </li>
    </ul>
    <style>
        .right_panel .divider{
            border: 1px solid rgba(59,70,72,0.45);
            height: 100%;
            width:1px;
        }
        
        .right_panel .language{
            padding:14px 20px;
            position:relative;
        }
        .right_panel .language .dropdown-menu{
            left:-100%;            
        }
        #header .right_panel .language .dropdown-menu ul li{
            display:block;
        }
        
        .right_panel .language:hover .dropdown-menu{
            display:block;
            
        }
    </style>
    <div class='right_panel'>
        <ul>
            <li class='divider'></li>
            <li class='language dropdown-toggle' data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <a href=''><img src='/html/images/United-States.png'></a>
                
                <div class="dropdown-menu">
                    <ul>
                        <li><a href='/ru/'>Русский</a></li>
                        <li><a href='/en/'>Английский</a></li>
                    </ul>
                </div>
            </li>
            
            <li><a href='/logout/' class='logout'></a></li>
        </ul>
    </div>
</div>