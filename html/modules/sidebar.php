<style>
    #new_sidebar{
        width:250px;
        position:fixed;
        top:0px;
        left:0px;
        bottom:0px;
        background:#273135;
        color: #89949B;
    }
    #new_sidebar .userblock{
        height: 60px;
        background: #FA7252;
        width:100%;
    }
    #new_sidebar ul{
        padding:0px;
        margin:0px;
        
    }
    #new_sidebar ul li{
        height: 45px;
    }
    #new_sidebar ul li a{
        display:block;
        height: 100%;
        width:100%;
        padding-left:25px;
        color: #89949B;
        font-weight:bold;
        padding-top:12px;
        border-left:5px solid transparent;
        /*pixelvicon*/
    }
    #new_sidebar ul li:hover a{
        text-decoration: none;
        background: rgba(0,0,0,0.3);
    }
    #new_sidebar ul li.active a{
        position:relative;
        background: rgba(0,0,0,0.8);
        border-left:5px solid #E76049;
    }
    
    #new_sidebar ul li.active a::after {
        top: 11px;
        right: 0;
        content: '';
        position: absolute;
        display: inline-block;
        border-top: 12px solid transparent;
        border-bottom: 12px solid transparent;
        border-right: 10px solid #E9F0F5;
    }
    
    #new_sidebar ul li a span{
        margin-right: 10px;
        display:inline-block;
        vertical-align: top;
        position:relative;
        width:18px;
        height: 19px;
    }
    #new_sidebar ul li a span:before{
        position:absolute;
        font-family: fontello;
    }
    #new_sidebar ul li a span.ico1:before{        
        content:"W";
    }
    #new_sidebar ul li a span.ico2:before{        
        content:"\EA70";
    }
    #new_sidebar ul li a span.ico3:before{        
        content:"\EA71";
    }
    #new_sidebar ul li a span.ico4:before{        
        content:"\EA81";
    }
    #new_sidebar ul li a span.ico5:before{        
        content:"r";
    }
    #new_sidebar ul li a span.ico6:before{        
        content:"\EAAD";
    }
    #new_sidebar ul .menutitle{
        font-weight:bolder;
        color: rgba(137,148,155,0.4);
        text-transform: uppercase;
        margin-bottom: 12px;
        font-size:12px;
        padding-top:25px;
        padding-left:25px;
    }
    
</style>
<div id="new_sidebar">
    <div class="userblock">
        <style>
            #new_sidebar .ico{
                position:relative;
                display:inline-block;
                vertical-align: middle;
                width:60px;
                height: 60px;
            }
            #new_sidebar .username{
                display: inline-block;
                vertical-align: middle;
                font-size: 18px;
                color: white;
                font-weight:bold;
                width:150px;
            }
              
            #new_sidebar .ico::before{
                left:50%;
                margin-left: -15px;
                top:50%;
                margin-top:-16px;
                position:absolute;
                content:'';
                display:block;
                width:30px;
                height: 32px;                
                background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIyOXB4IiBoZWlnaHQ9IjMzcHgiIHZpZXdCb3g9IjAgMCAyOSAzMyIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjkgMzMiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxnIGlkPSJJY29uc194NUZfQWxsIj48ZyBpZD0iQ29udGFjdHNfeDVGX24iPjxwYXRoIGZpbGw9Im5vbmUiIHN0cm9rZT0iI0ZGRkZGRiIgc3Ryb2tlLW1pdGVybGltaXQ9IjEwIiBkPSJNMjEuMDIzLDcuMjUxYzMuODczLDIuMjUxLDYuNDc5LDYuNDQ0LDYuNDc5LDExLjI0N2MwLDcuMTgtNS44MTYsMTMtMTMsMTNjLTcuMTc5LDAtMTMtNS44MTctMTMtMTNjMC00LjY1NiwyLjQ1MS04Ljc0Miw2LjEzNS0xMS4wMzgiLz48Zz48Zz48Zz48Zz48Zz48Zz48Zz48Zz48ZGVmcz48cGF0aCBpZD0iU1ZHSURfMV8iIGQ9Ik0yNy41LDE4LjQ5NmMwLDcuMTgyLTUuODE4LDEzLTEzLjAwMSwxM2MtNy4xNzgsMC0xMy01LjgxNi0xMy0xM2MwLTcuMTc4LDMuMDEyLTIwLjQ5LDEzLTIwLjQ5QzI0LjM4Ny0xLjk5MywyNy41LDExLjMyMSwyNy41LDE4LjQ5NnoiLz48L2RlZnM+PGNsaXBQYXRoIGlkPSJTVkdJRF8yXyI+PHVzZSB4bGluazpocmVmPSIjU1ZHSURfMV8iICBvdmVyZmxvdz0idmlzaWJsZSIvPjwvY2xpcFBhdGg+PHBhdGggY2xpcC1wYXRoPSJ1cmwoI1NWR0lEXzJfKSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRkZGRkZGIiBzdHJva2UtbWl0ZXJsaW1pdD0iMTAiIGQ9Ik0yNy44OTMsMjYuOTUxYy0wLjg5NS0xLjc3OS0zLjgzLTIuODU1LTcuODk1LTQuMzQ4Yy0wLjU3NC0wLjIxMS0xLjE3Mi0wLjQzMi0xLjc5My0wLjY2VjE3LjQyYzAuNDA4LTAuNDQ0LDEuNDM4LTEuNzY3LDEuNTM5LTMuODE1YzAuMjc5LTAuMjE4LDAuNTA4LTAuNjIzLDAuNjM5LTEuMTc4YzAuMjA3LTAuODc4LDAuMS0xLjkyMi0wLjU0OS0yLjUwM2MwLjA0My0wLjExNCwwLjEtMC4yNDksMC4xNDUtMC4zNzZjMC40OC0xLjIwNiwxLjM2OS0zLjQ1MSwxLjAyMS01LjE5MmMtMC4zOTUtMS45NzEtMy4yNi0yLjg1My01LjkyOC0yLjg1M2MtMS45NDksMC00LjMyMSwwLjQ4Ni01LDEuODM1QzkuMzI0LDMuNDA1LDguNzQ4LDMuNyw4LjM2NCw0LjIyMUM3LjMwNyw1LjY0Niw4LjA5Miw4LjIyOSw4LjUxLDkuNjE3YzAuMDI3LDAuMDk4LDAuMDYxLDAuMjAxLDAuMDg2LDAuMjljLTAuNjYyLDAuNTc1LTAuNzc1LDEuNjM1LTAuNTY2LDIuNTIxYzAuMTMxLDAuNTU0LDAuMzU1LDAuOTYxLDAuNjQxLDEuMTc5YzAuMDk5LDIuMDE2LDEuMTA0LDMuMTk3LDEuNTM0LDMuNjE4djQuNzI5Yy0wLjYxOCwwLjIyOS0xLjIxOCwwLjQ0NS0xLjc5MywwLjY1OWMtNC4wNjQsMS40ODQtNy4wMDMsMi41NjItNy44OTQsNC4zNDljLTEuMjk5LDIuNi0xLjMxMyw1LjIyOS0xLjMxMyw1LjMzOGMwLDAuMjc0LDAuMjI1LDAuNSwwLjUsMC41aDI5YzAuMjc1LDAsMC41LTAuMjI2LDAuNS0wLjVDMjkuMjA1LDMyLjE4MSwyOS4xOTEsMjkuNTQ5LDI3Ljg5MywyNi45NTF6Ii8+PC9nPjwvZz48L2c+PC9nPjwvZz48L2c+PC9nPjwvZz48L2c+PC9nPjwvc3ZnPg==);
            }
        </style>
        <div class='ico'></div>
        <div class='username'><?=$this->_user->email?></div>
    </div>      
    <style>
        .list_content{
            top:60px;
            bottom:0px;
            left:0px;
            width:100%;
            overflow:hidden;
            position:absolute;
        }
        .list_content .inner{
            position:relative;
            top:0px;
            width:100%;
        }
    </style>
    <div class="list_content">
        <div class="scroller" scroller data-parent="#new_sidebar .list_content" data-inner="#new_sidebar .list_content .inner"></div>
        <div class="inner">
            <ul>
                <?php if(in_array($this->_controller,['lessons'])){ ?>
                    <div class="menutitle">Раздел1</div>
                        <li class='<?=$this->_controller == 'lessons' && $this->_action == 'index' ? 'active':''?>'>
                            <a href="/lessons/"><span class="ico2"></span>Уроки</a>
                        </li>
                        <li class='<?=$this->_controller == 'lessons' && $this->_action == 'questions' ? 'active':''?>'>
                            <a href="/lessons/questions/"><span class="ico2"></span>Вопросы</a>
                        </li>            
                        <li class='<?=$this->_controller == 'lessons' && $this->_action == 'answers' ? 'active':''?>'>
                            <a href="/lessons/answers/"><span class="ico3"></span>Ответы</a>
                        </li>                        
                <?php } ?>


                <?php if(in_array($this->_controller,['tasktable'])){ ?>
                    <div class="menutitle">Каналы</div>
                        
                    <hr style='display:block; margin:auto;border:1px solid #384648;margin-top:8px;margin-bottom:0px; width:180px;'/>
                <?php } ?>


                <?php if(in_array($this->_controller,['system'])){ ?>
                    <div class="menutitle">Управление</div>                
                        
                        <li class='<?=$this->_controller == 'system' && $this->_action == 'admins' ? 'active':''?>'>
                            <a href="/system/admins/"><span class="ico5"></span>Учителя</a>
                        </li>
                        
                    <hr style='display:block; margin:auto;border:2px solid #384648;margin-top:15px;margin-bottom:0px; width:200px;'/>
                <?php } ?>
                    
                <?php if(in_array($this->_controller,['feedback'])){ ?>
                    <div class="menutitle">Обратная связь</div>
                        
                        <li class='<?=$this->_controller == 'feedback' && $this->_action == 'index' ? 'active':''?>'>
                            <a href="/feedback/"><span class="ico5"></span>Учителя</a>
                        </li>
                        
                    <hr style='display:block; margin:auto;border:2px solid #384648;margin-top:15px;margin-bottom:0px; width:200px;'/>
                <?php } ?>
            </ul>
        </div>
    </div>
</div> 