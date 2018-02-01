<script>
    $('document').ready(function(){
        $('.panel_triger').click(function(){
            if($('.page_left').css('display') == 'none'){
                $('.page_left').css({'display':'block'});
                $('.page_right .panel_triger').css({'display':'none'});
            }else{
                $('.page_left').css({'display':'none'});
                $('.page_right .panel_triger').css({'display':'inline-block'});
            }
        });
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
        
        <div class="table">
            <div class="tr">
                <div class="th" style="width:37px;"></div>

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
                            <a ng-click='replyMessage($event,"<?=$item->id?>")' class="reply" href=""><span></span>ответить</a>
                            <a class="del_user" href=""><span></span>удалить</a>
                        </div>
                        <?=$item->from_sendername?>
                    </div>
                    <div class="td">
                        <a  class="readmessage" href><?=$item->subject?></a>
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

    <script>
        $('document').ready(function(){
            $('.table .readmessage').click(function(){
                $('.readletter_page').animate({'left':'0px'},700);
                return false;
            });
            
            $('.readletter_page').on('click','.back_btn',function(){
                $('.readletter_page').animate({'left':'100%'},700);
                return false;
            });
        });
    </script>
    <div class='readletter_page' tabindex="-1">
        <div style='margin-bottom:20px;'>
            <div class='back_btn'></div>
            <h2>второе письмо</h2>
        </div>
        
        <a href="" tabindex="-1" class='reply_btn btn btn-primary'>ОТВЕТИТЬ</a>
        
        <div class='letter_block'>
            <div class='letter_header'>
                <div class='left'>
                    Славик Сивинюк <span>< s.sivinyuk@gmail.com ></span>
                </div>
                <div class='right'>
                    Сегодня, 12:49
                </div>
                <div class='receiver'><span>Кому:</span> new58bed42d58b70@mail.amocrm.ru</div>
                <div class='content'>
                    Попытка номер два 
                </div>
            </div>
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
                                    <input type="text" value="Re:" class="form-control">
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
    
    <script>    
        app.controller('mailCtrl', function($scope,$http){
            $scope.patterns = [{name:'name',id:'id'}];
            
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
                        

        });
    </script>
    
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