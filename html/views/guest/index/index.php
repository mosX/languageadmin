<style>
    body{
        margin:0px;
        padding:0px;
        position:relative;
        height:100%;
        width:100%;
        overflow: hidden;
    }
    ul{
        list-style: none;
    }
    
    .blur_in{
        filter: blur(10px);
        pointer-events: none;
    }
    
    .container{
        width:1170px;
        position:relative;
    }
    
    .page_wrapper{        
        
        width:1170px;
        position:relative;        
        height:100%;
        margin:auto;
    }
    
    #fixed_content{
        position:fixed;
        height:100%;
        width:100%;
        left:0px;
        top:0px;
        background: #339DC7;
    }
    
    #header{
        
        
        position:absolute;
        width: 100vw;
        top:0px;
        padding-top: 27px;
        z-index:99;
        
    }
    
    #header_inner{
        padding:30px 0px;
        max-width:1170px;
        width:100%;
        display:flex;
        
        margin:auto;
    }
    #header_inner .logo{
        width: 174px;
        height: 35px;
        background-position: 0 -756px;
        background-image:url('/html/images/sprite.png');
        display:block;
        flex-shrink: 0;
    }
    #header_inner .menu{
        display:block;
        flex-shrink: 0;
    }
    #header_inner .right_panel{
        display:flex;
        margin-left:auto;
        align-items: center;
    }
    #header_inner .login{
        position:relative;
        padding-left:20px;
        padding-bottom: 4px;
        font-size:18px;
        color: white;
        font-weight:bolder;                
        border-bottom: 2px solid transparent;        
    }
    #header_inner .login:hover{
        text-decoration: none;
        border-bottom: 2px solid white;        
    }
    #header_inner .login span{
        position:absolute;
        left:-2px;
        bottom:8px;
        display:block;
        width: 14px;
        height: 19px;
        background-position: 0 -312px;
        background-image: url(/html/images/sprite.png);
        background-repeat: no-repeat;
    }
    
    .page_wrapper .square{
        position:absolute;
        z-index:1;
        right:0px;
        top:200px;
        width:62%;
        height: 540px;
        border: 14px solid #62b4db;
        opacity:.99
    }
    .page_wrapper .girl_bg{
        position:absolute;
        z-index:1;
        right:0px;
        top:-35px;
        bottom:0;
        right:-50%;
        width:160%;                
        display:block;
        background: url('/html/images/need_for_sales.png') no-repeat;
        background-position: 50% 50%;
        background-size: cover;
        min-height:768px;
    }
</style>

<script>
    $('document').ready(function(){
        $('#header .login').click(function(){            
            $('#authModal').css({"display":"block"});
            
            $('.page_wrapper').addClass('blur_in');
            $('#header').addClass('blur_in');
            
            return false;
        });
    });
</script>

<div id="fixed_content">
    <div id="header">
        <div id="header_inner">
            <figure>
                <a class="logo" href="/"></a>
            </figure>
            <div class='menu'>
                <ul>
                    <li></li>
                </ul>
            </div>
            
            <div class='right_panel'>
                <a href='/login' class='login'>                    
                    <span></span>
                    Войти
                </a>
            </div>
        </div>
    </div>
    
    <div class='page_wrapper'>
        <div class='square'></div>
        <div class='girl_bg'></div>
    </div>
    <style>
        .modal_page{
            display:none;
            position:fixed;
            left:0px;
            top:0px;
            width:100%;
            height:100%;
            background: rgba(28,148,196,0.5);            
            z-index:1000;
        }
        .modal_page .overlay{
            position: fixed;
            left:0px;
            top:0px;
            width:100%;
            height:100%;
        }
        .modal_page .modal_inner{            
            position:relative;
            width:1170px;            
            height:100%;
            margin:auto;
            padding-top:100px;
            z-index:10;
        }
        
        .modal_page .close_btn{
            cursor:pointer;
            right:0px;
            top:70px;
            transform: translate(calc(100% + 7px));
            font-size: 14px;
            font-style:italic;
            text-align: right;
            color: #fff;                    
            position:absolute;
        }

        .modal_page .close_btn .close_ico{
            position:absolute;
            top:50%;
            transform:translateY(-50%);
            left:-26px;
            display:block;
            width:21px;
            height:21px;
            background:url('/html/images/sprite.png');
            background-position: 0 -396px;
        }
        
        .form-control{
            height:50px;
            padding:0px 32px;
            color: #339dc8;
            border: 1px solid #fff;
            border-radius: 0px;
        }
        
        .btn.btn-primary{
            padding:17px 25px;
            font-size: 15px;
            border: 1px solid #62b4db;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.23);
            background:#006f9f;
            border-radius: 0px;
        }        
    </style>
    <script>
        $('document').ready(function(){
            $('.close_btn,.overlay','.modal_page').click(function(){

                $('.blur_in').removeClass('blur_in');                
                $(this).closest('.modal_page').css({'display':'none'});
                
                return false;                
            });
        });
    </script>
    
    <script>
        $('document').ready(function(){
            $('#authModal form').submit(function(){
                var email = $('input[name=email]',this).val();
                var password = $('input[name=password]',this).val();

                $.ajax({
                    url:'/login/',
                    type:'POST',
                    data:{email:email,password:password},
                    success:function(msg){
                        //console.log(msg);
                        var json = JSON.parse(msg);
                        if(json.status == 'success'){
                            location.reload();
                        }
                    }
                });
                return false;
            });
        });
    </script>
    <div class='modal_page' id='authModal'>
        <div class='overlay'></div>
        <div class='modal_inner'>
            <style>
                .auth_form{
                    text-align: center;
                    width:312px;
                    height: 400px;
                    position:absolute;
                    right:41px;
                    top:158px;
                }
                .auth_form .form_email_ico{
                    width:19px;
                    height:19px;
                    display:block;
                    position:absolute;
                    left:8px;
                    top:15px;
                    
                    background:url('/html/images/sprite.png') no-repeat;
                    background-position: 0 -438px;
                }
                
                .auth_form .form_password_ico{
                    width:19px;
                    height:19px;
                    display:block;
                    position:absolute;
                    left:8px;
                    top:15px;                    
                    background:url('/html/images/sprite.png') no-repeat;
                    background-position: 0 -418px;
                }
                
                .modal_page .auth_title{
                    display:block;            
                    width:100px;
                    margin:auto;
                    position:relative;
                    padding-left:20px;            
                    font-size:18px;
                    color: white;
                    font-weight:bolder;                
                    border-bottom: 2px solid transparent;        
                }

                .modal_page .auth_title span{
                    position:absolute;
                    left:-2px;
                    bottom:2px;
                    display:block;
                    width: 14px;
                    height: 19px;
                    background-position: 0 -312px;
                    background-image: url(/html/images/sprite.png);
                    background-repeat: no-repeat;
                }
                
                
            </style>
            <div class='close_btn'>
                <span class='close_title'>Закрыть</span>
                <span class='close_ico'></span>
            </div>
            <form class='auth_form' action='' method='POST'>                
                <div class='form-group'>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <div class='auth_title'>
                                <span></span>Войти
                            </div>                                
                        </div>
                    </div>
                </div>
                <div class='form-group'>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <div style='position:relative;'>
                                <input type='text' class='form-control' name='email' placeholder="Email">
                                <span class='form_email_ico'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='form-group'>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <div style='position:relative;'>
                                <input type='password' class='form-control' name='password' placeholder="Password">
                                <span class='form_password_ico'></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='form-group'>
                    <div class='row'>
                        <div class='col-sm-12'>
                            <input type='submit' class='btn btn-primary' value='ВОЙТИ'>
                        </div>
                    </div>
                </div>
            </form>
        </div>        
    </div>
</div>