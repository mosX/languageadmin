<script>    
    app.config(['$sceProvider', function($sceProvider) {
        $sceProvider.enabled(false);
    }]);
    app.controller('mailCtrl', function($scope,$http){
        $scope.patterns = [];
        $scope.messages = [];
        
        $scope.reply_id = null; //айдиоткыртого письма
        $scope.reply_index = null; //индекс письма в массиве
        
        $scope.form = {mail:{}};   //форма ответа
        
        $scope.updatePatternsList = function(callback){ //load Patterns List
            $http({
                method: 'GET',
                url:'/mail/patterns_list/'
            }).then(function successCallback(res){
                //console.log(res.data);
                callback(res.data);
            });
        }

        $scope.setPatternsModal = function(){
            $scope.updatePatternsList(function(data){
                console.log(data);
                $scope.patterns = data;
                $('#patternsModal').modal('show');
            });
        }

        $scope.addPatterModal = function(event){
            $('#addPatternsModal').modal('show');
            event.preventDefault();
        }

        $scope.savePattern = function(event){
            $http({
                method: 'POST',
                data:$scope.patter_form,
                url:'/mail/save_pattern/'
            }).then(function successCallback(res){
                if($scope.patter_form.id == undefined){ //добавляем
                    $scope.patterns.unshift($scope.patter_form);
                }else{  //меняем существующий
                    $scope.patterns[$scope.patter_form.id] = $scope.patter_form;
                }

                $('#addPatternsModal').modal('hide');
                $scope.patter_form = {};
            });

            event.preventDefault();
        }

        $scope.editPatternModal = function(id,event){
            $http({
                method: 'POST',
                data:{id:id},
                url:'/mail/edit_data/'
            }).then(function successCallback(res){
                console.log(res.data);
                $scope.patter_form = res.data;
                $('#addPatternsModal').modal('show');
            });

            event.preventDefault();
        }

        $scope.insertAtCursor = function(myValue){
            var myField = $('#addPatternsModal textarea')[0];

            if (document.selection){
                myField.focus();
            }else if (myField.selectionStart || myField.selectionStart == '0') {
                var position = myField.selectionStart;

                myField.value = myField.value.substring(0,myField.selectionStart) + myValue + myField.value.substring(myField.selectionEnd,myField.value.length);
                myField.selectionStart = myField.selectionEnd = position  + myValue.length;
            }else{
                myField.value += myValue;
            }
            myField.focus();
        }

        $scope.initReplyModal = function(id){
            $http({
                method: 'GET',                    
                url:'/mail/get_mail/'+id+'/'
            }).then(function successCallback(res){
                $scope.patterns = res.data.patterns;
                $scope.form.mail = res.data.mail;

                $('#replyModal').modal('show');
            });
        }

        $scope.loadPattern = function(event){   
            $http({
                method: 'GET',                    
                url:'/mail/loadpattern/'+$scope.pattern_selected+'/'
            }).then(function successCallback(res){
                $('#replyModal textarea').val(res.data.text);

                $('#replyModal').modal('show');
            });
        }

        $('input,textarea','#addPatternsModal form').keyup(function(){
            $('#addPatternsModal .close_block').css({'display':'none'});
            $('#addPatternsModal .controls').css({'display':'block'});
        });
        
        $scope.replyMessage = function(event,id){                        
            $scope.form.mail.id = $scope.messages[$scope.reply_index].id;
            $scope.form.mail.from = $scope.messages[$scope.reply_index].from;
            $scope.form.mail.subject = 'Re:'+$scope.messages[$scope.reply_index].subject;
            
            $('#replyModal').modal('show');
            
            event.preventDefault();
        }
        
        $scope.getReplyIndex = function(id,index){
            $scope.reply_id  == id ? $scope.reply_index = index :null;
        }

        $scope.readMessage = function(id){
            $scope.reply_id = id;
            
            $http({
                method: 'GET',                    
                url:'/mailbox/readmessage/?id='+id,
            }).then(function successCallback(res){                
                $scope.messages = res.data;
                $('.readletter_page').animate({'left':'0px'},700);
            });
        }

        $scope.hidePanel = function(event){  //спрятать блок с письмами
            $('.readletter_page').animate({'left':'100%'},700);
            event.preventDefault();
        }
    });
</script>
<div id="page_wrapper" style="padding-top:0px" ng-controller="mailCtrl">
    
    <div class="page_left">
        <h2>Почта <span class='panel_triger'></span></h2>

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
        <h2>
            <span class='panel_triger'></span> Входящие
            <div class='patterns_menu pull-right' ng-click="setPatternsModal()">
                <button><span></span>Шаблоны</button>
            </div>
        </h2>
        <div class="table_holder">
            <?= $this->m->pagesNav ?>


    <div align="right"><?=$this->m->pagesNav?></div>
    <ul class='tabs_list'>
        <li<?=$this->m->_path[1] == 'new' ? ' class="active"' : ''?>><a href="/mailbox/new/">Новые</a></li>
        <li<?=$this->m->_path[1] == 'inbox' ? ' class="active"' : ''?>><a href="/mailbox/inbox/">Входящие</a></li>
        <li<?=$this->m->_path[1] == 'sent' ? ' class="active"' : ''?>><a href="/mailbox/sent/">Отправленные</a></li>
    </ul>
    
     <script>
        $('document').ready(function(){
            $('.table .readmessage').click(function(){
                var id = parseInt($(this).attr('data-id'));
                
                $.ajax({
                    url:'/mailbox/readmessage/?id='+id,
                    type:'GET',
                    success:function(msg){
                        console.log(msg);
                    }
                });
                
                $('.readletter_page').animate({'left':'0px'},700);
                return false;
            });
            
            $('.readletter_page').on('click','.back_btn',function(){
                $('.readletter_page').animate({'left':'100%'},700);
                return false;
            });
        });
    </script>
 

    <div class='table'>
        <?php if (count($this->m->mailbox->rows)) { ?>
            <?php foreach($this->m->mailbox->rows as $mail) { ?>        
                <div class='tr' style="cursor: pointer;<?=$mail->status_unread ? "font-weight: bold;" : ""?>" id="<?=$mail->id?>">
                    <div class='td' style="width:30px;" onclick="javascript:readmessage(<?=$mail->id?>);">
                        <div id="answered-<?=$mail->id?>" class="<?=$mail->status_answered ? 'mailbox-answered' : ''?>"></div>
                        <label class='checkbox'>
                            <input type="checkbox" class="action_panel_triger">
                            <div class='box'></div>
                        </label>
                    </div>
                    <div class='td'>
                        <?php if ($mail->user_id){ ?>
                            <div class='actions_panel'>
                                <a class="userinfo" href="/userinfo/?id=<?=$mail->user_id?>"></a>
                                <a class="userdep modal_action" href="/analize/transactions/?datatype=ajax&id=<?=$mail->user_id?>"></a>
                                <a class="userbonus modal_action" href="/analize/bonuses/?datatype=ajax&id=<?=$mail->user_id?>"></a>
                                <a class="finduser modal_action" href="/analize/?datatype=ajax&id=<?=$mail->user_id?>"></a>
                                <div class="history modal_action" href="/analize/history/?id=<?=$mail->user_id?>"></div>
                            </div>

                            <span title="<?=$mail->title_ru?>" class="flag <?=strtolower($mail->code)?>"></span>
                            <span class='username' onclick="javascript:readmessage(<?=$mail->id?>)"><?=$mail->email ?></span>
                        <?php } else { ?>                        
                            <span ng-click='load'><?=$mail->from?></span>
                        <?php } ?>                       
                    </div>
                    <div class='td' ng-click='readMessage(<?=$mail->id?>)'>
                        <?=htmlspecialchars($mail->subject)?>
                    </div>
                    <div class='td' onclick="javascript:readmessage(<?=$mail->id?>);"><?=htmlspecialchars(substr($mail->plaintext, 0, 50))?>...</div>
                    <div class='td' onclick="javascript:readmessage(<?=$mail->id?>);"><?=$mail->date_receive?></div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
            
<div align="right"><?=$this->m->pagesNav?></div>
    </div>


<script>
    var prev_scrolltop_pos = 0;

    function readmessage(id) {
        prev_scrolltop_pos = $('body').scrollTop();

        var prevstyle = $("div#answered-"+id).attr("class");
        $("div#answered-"+id).removeClass(prevstyle).addClass("mailbox-readpreloader");

        //$("#readmessage").load("/manage/index.php?act=readmessage&id="+id, function() {
        $("#readmessage").load("/mailbox/readmessage/?id="+id, function() {
            $("div#mailboxlist").css("display","none");
            $("#readmessage").css("display","");
            $("tr#"+id).css("font-weight","normal");
            $(".btn000").button();
            
            load_pattern();

            $("div#answered-"+id).removeClass("mailbox-readpreloader").addClass(prevstyle);
        });

        return false;
    }

    function replymessage(id,lang) {
        $("#divreplyform").load("/mailbox/replymessage/?id="+id+"&lang="+lang, function() {
            $("#body").focus();
    
            $(".btn000").button();
        });

        return false;
    }
    
    function sentmessage(id){
        var to = $('#replyform input[name=to]').val();
        var subject = $('#replyform input[name=subject]').val();
        var body = $('#replyform textarea[name=body]').val();
        var id = $('#replyform input[name=id]').val();
        var user_id = $('#replyform input[name=user_id]').val();
        
            $.ajax({
                url:'/mailbox/send',
                type:'POST',
                data:{to:to,subject:subject,body:body,id:id,user_id:user_id},
                success:function(msg){
                    var json = JSON.parse(msg);                    
                    if (json.error == false) {
                        $("span#replyformstatus").css("color","green");
                        $("div#answered-"+id).addClass("mailbox-answered");
                        $("span#replyformstatus").html(json.errorText);
                        returntolist(id);
                    } else {
                        $("span#replyformstatus").css("color","red");
                        $("span#replyformstatus").html(json.errorText);
                    }
                },
                beforeSubmit: function(){
                    $("span.preloader").css("display","inline-block");
                }
            });
            return false;
    }
    function returntolist(id){
        $("#readmessage").css("display","none");
        $("div#mailboxlist").css("display","");

        $('body').scrollTop(prev_scrolltop_pos);

        return false;
    }
</script>
<style>
    .letter_block{
        height: 160px;
        overflow: hidden;
        position:relative;        
    }
    .letter_block .inner{
        overflow: hidden;
        height:100%;
        width:100%;
    }
    
    .expander{
        height: 10px;
        background: #c6c6c6;
        position:absolute;
        bottom:0px;
        left:0px;
        width:100%;        
        cursor:pointer;        
    }
    .expander:after{
        display:block;
        
        content:"";
        position:absolute;
        margin:auto;        
        border: 1px solid white;
        border-width: 6px 6px 0px 6px;
        border-color: white transparent transparent transparent;        
        left:50%;
        margin-left:-3px;
        bottom: 2px;
    }
</style>

<script>
    $('document').ready(function(){
       
        $('.readletter_page').on('click','.letter_block .expander',function(){        
            var parent  = $(this).closest('.letter_block');
            console.log($(parent).css('height'));
            if(parseInt($(parent).css('height')) > 160){
                $(this).closest('.letter_block').css({'height':'160px'});
            }else{
                $(this).closest('.letter_block').css({'height':'auto'});
            }
            
            return false;
        });
    });
</script>
    
    <div class='readletter_page' tabindex="-1">
        <div style='margin-bottom:20px;'>
            <div ng-click='hidePanel($event)' class='back_btn'></div>
            <h2>второе письмо</h2>
        </div>

        <a href="" tabindex="-1" ng-click="replyMessage($event,reply_id)" class='reply_btn btn btn-primary'>ОТВЕТИТЬ</a>

        <div class='letter_block' ng-repeat="item in messages"  ng-init="getReplyIndex(item.id , $index)" style='{{item.folder === "sent" ? "margin-left:15px;":""}}' data-id="{{item.id}}">
            <div class="inner">                
                <div class='left'>
                    {{item.subject}}
                </div>
                <div class='right'>
                    {{item.date_receive}}
                </div>
                <div class='receiver'><span>От:</span> {{item.firstname}} {{item.lastname}} <span>< {{item.email}} ></span></div>
                <div class='content' ng-bind-html="item.plaintext"></div>                
            </div>
            
            <div class="expander"></div>
        </div>            
    </div>
    
    <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Отправить письмо</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST'>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Шаблон:</label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control" ng-change='loadPattern($event)' ng-model='pattern_selected'>
                                        <option value='0'>(Без Шаблона)</option>
                                        <option ng-repeat="item in patterns" value='{{item.id}}'>{{item.name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Кому:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" ng-model="form.mail.from">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="text" value="Re:" class="form-control" ng-model="form.mail.subject">
                                </div>
                            </div>
                        </div>
                            
                        <div class="form-group">
                            <div class="row">                                
                                <div class="col-sm-12">
                                    <textarea class="form-control" name='message'></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type='hidden' name='id' value=''>
                        <input type="submit" class="btn btn-secondary" value='Отправить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                        <input type="hidden" class="form-control" ng-model="form.mail.id">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="patternsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action=''>
                    <div class="modal-header">
                        <h5 class="modal-title">Шаблоны</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class='patterns_block'>
                            
                            <div ng-repeat="item in patterns" class='item' data_id='{{item.id}}' ng-click="editPatternModal(item.id,$event)">
                                <div class='edit_btn'></div>
                                {{item.name}}
                            </div>
                            
                        </div>
                        <button class='add_pattern' ng-click="addPatterModal($event)"><span></span>Добавить Шаблон</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="addPatternsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action='' ng-submit="savePattern($event)">
                    <div class='form-group'>
                        <div class='flex_block'>
                            <div class='col-1'>
                                <input type='text' class='form-control' placeholder='Новый Шаблон' value='' name='name' ng-model="patter_form.name">
                            </div>
                            
                            <div class='col close_block'>
                                <a href='' data-dismiss="modal" aria-label="Close">Закрыть</a>
                            </div>
                            
                            <div class='col controls'>
                                <input type='text' data-dismiss="modal" class='btn btn-secondary' value='Отмена'>
                                <input type='submit' class='btn btn-primary' value='Сохранить'>
                                <input type='hidden' name='id' ng-model="patter_form.id">
                            </div>
                        </div>
                    </div>
                        
                    <div class='form-group'>
                        <div class='row'>
                            <div class='col-sm-12'>
                                <input type='text' class='form-control' placeholder='Тема Письма' name='subject' ng-model="patter_form.subject">
                            </div>
                        </div>
                    </div>
                        
                    <div class='form-group'>
                        <div class='row'>
                            <div class='col-sm-12'>
                                <textarea name='text' ng-model="patter_form.text" class='form-control'></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <div><a class='add_name' ng-click="insertAtCursor('{contact.name}')">{contact.name}</a> - Имя контакта адресата письма</div>
                        <div><a>{profile.name}</a> - Имя профиля пользователя amoCRM</div>
                        <div><a>{profile.phone}</a>- Номер телефона пользователя amoCRM</div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>