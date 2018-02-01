<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyChQwAXEXRThQkqgC-xW18anW640loh6IA&sensor=false&libraries=places&v=3"></script>
<style>
    #top_menu{
        height: 60px;
    }
</style>
<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
            $scope.$on('completeTaskCall', function (event, data){
                $http({
                    url:'/tasks/getdata/?id='+data.items.id,
                }).then(function(ret){
                    console.log(ret.data);
                    
                    $scope.taskComplete = ret.data.data;
                });
                
                console.log(data.items); // Данные, которые нам прислали
            });
    }]);
</script>

<div ng-controller="pageCtrl">
<script>
    app.controller('cardCtrl', ['$scope','$http',function($scope,$http){
        $scope.delTag = function(event,id){
            console.log(id);
            $http({
                url:'/tags/del/?id='+id,
            }).then(function(ret){
                console.log(ret.data);
                if(ret.data.status == 'success'){
                    $(event.target).closest('li').remove();
                }
            });
            
            event.preventDefault();
        }
        
        $scope.editTag = function(event,id){
            console.log('edit TAG');
            if($('.edit_input',$(event.target).closest('li')).css('display') == 'none'){
                $('.edit_input',$(event.target).closest('li')).css({'display':'block'});
                $('.text',$(event.target).closest('li')).css({'visibility':'hidden'});
                $('.edit_input input',$(event.target).closest('li')).focus();
            }else{
                $('.edit_input',$(event.target).closest('li')).css({'display':'none'});
                $('.text',$(event.target).closest('li')).css({'visibility':'visible'});
                
                $http({
                    url:'/tags/edit/',
                    method:'POST',
                    data:{id:id,tag:$('.edit_input input',$(event.target).closest('li')).val()}
                }).then(function(ret){
                    console.log(ret.data);
                });
            }
            
            event.preventDefault();
        }
        
        $scope.updateTagName = function(event){
            console.log($(event.target).val());
            
            $('.text',$(event.target).closest('li')).text($(event.target).val());
        }
    }]);
</script>
<div id='top_menu'>
    <?=$this->m->module('header')?>
</div>

<div id="card_holder" ng-controller="cardCtrl">
    <div id="card_fields">
        <div class="fields_block">
            <form action="" method="POST">
                <div class="form_top">
                    <div class="top_name_block">
                        <div class="card_name_holder">
                            <input type="text" value="<?=$this->m->data->fullname?>" placeholder="Имя Фамилия" name="fullname" id="personal_name">
                            <input type="hidden" value="<?=$this->m->data->id?>" name="user_id">
                        </div>
                    </div>
                    <style>
                        .del_tag{
                            display:none;
                            width:15px ;
                            height:15px;
                            background: url('/html/images/close.png');
                            background-size:cover;
                            border-radius: 8px;
                            position:absolute;
                            top:-7px;
                            right:-10px;
                            z-index:100;
                        }
                        .add_tag_block li{
                            cursor:pointer;
                            position:relative;
                        }
                        .add_tag_block li:hover .del_tag{
                            display: block
                        }   
                        .add_tag_block li .edit_input{
                            display:none;
                        }
                        .add_tag_block li .edit_input input{
                            cursor:pointer;
                            position:absolute;
                            top:0px;                          
                            left:0px;
                            padding-left:10px;
                            width:100%;
                            display:block;
                        }
                    </style>
                <script>
                    //TAGS in ADD CONTACT AND DETAILS
                    //TODO переделать на ангулар
                    $('document').ready(function(){
                        $('.add_tag').click(function(){
                            $(this).css({'display':'none'});
                            $('.add_tag_block').css({'display':'block'});
                            return false;
                        });
                        $('.add_tag_block input[name=tag]').focus(function(e){
                            $('.add_tag_block .tag_confirm').css({'display':'flex'});
                        });

                        $('.add_tag_block #new_tag_input').keyup(function(e){
                            $('.add_tag_block .tag_confirm .tag').text($(this).val());
                        });

                        $('.add_tag_block .tag_confirm').click(function(e){
                            if($(e.target).attr('class').indexOf('close') != -1){
                                $('.add_tag_block .tag_confirm').css({'display':'none'});
                                return false;
                            }

                            var name = $('.add_tag_block #new_tag_input').val();
                            var user_id = $('.fields_block form input[name=user_id]').val();

                            //добавляем тег в блок
                            if(!user_id){   //если создаем нового пользователя то отправлять будем при сабмите пользователя
                                $('.add_tag_block ul li:last-child').before('<li>'+name+'</li>');
                                $('.add_tag_block input').val('');
                                $('.add_tag_block .tag_confirm').css({'display':'none'});
                            }else{
                                $.ajax({
                                    url:'/tags/add/',
                                    type:'POST',
                                    data:{name:name,user_id:user_id},
                                    success:function(msg){
                                        var json = JSON.parse(msg);
                                        if(json.status == 'success'){
                                            $('.add_tag_block ul li:last-child').before('<li>'+name+'</li>');
                                            $('.add_tag_block input').val('');
                                            $('.add_tag_block .tag_confirm').css({'display':'none'});
                                        }else{
                                            $('.add_tag_block input').val('');
                                            $('.add_tag_block .tag_confirm').css({'display':'none'});
                                        }
                                        
                                    }
                                });
                            }
                        });
                    });
                </script>
                    <div class="tags_container">
                        <?php if(!$this->m->tags){ ?>
                            <a href="" class="add_tag">#тегировать</a>
                        <?php } ?>
                            
                        <div class="add_tag_block" style="<?=$this->m->tags ? 'display:block':''?>">
                            <ul>
                                <?php if($this->m->tags){ ?>
                                    <?php foreach($this->m->tags as $item){ ?>
                                        <li>
                                            <div ng-click="editTag($event,<?=$item->id?>)">
                                                <span class="text"><?=$item->name?></span>
                                                <span class="edit_input"><input ng-keyup="updateTagName($event)" type="text" value="<?=$item->name?>"></span>
                                            </div>
                                            <span ng-click="delTag($event,<?=$item->id?>)" class="del_tag"></span>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <li><input id="new_tag_input" style="border-bottom:1px solid #82a0e7" placeholder="Добавить Тег" type="text" name="tag" value=""></li>
                            </ul>
                            <div class="tag_confirm">
                                <button type="button" class="close">&times;</button>
                                
                                <div class="tag"></div>
                            </div>
                        </div>
                    </div>
                <script>
                    $('document').ready(function(){
                        console.log('READY');
                        //card_tabs_wrapper
                        $('.form_groups').css({'display':'none'});
                        
                        var tab = $('.card_tabs_wrapper .item.active').attr('data-tab');
                        $(tab+'.form_groups').css({'display':'block'});
                        
                        $('.card_tabs_wrapper .item').click(function(){
                            $('.card_tabs_wrapper .item').removeClass('active');
                            $(this).addClass('active');
                            
                            $('.form_groups').css({'display':'none'});
                            var tab = $(this).attr('data-tab');
                            $(tab+'.form_groups').css({'display':'block'});
                        });
                    });
                </script>
                    <div class="card_tabs_wrapper">
                        <div class="item active" data-tab="#personalInfoTab">
                            <a href="">Основное</a>
                        </div>
                        <div class="item" data-tab="#dealsTab">
                            <a href="">Сделки</a>
                        </div>
                    </div>
                </div>
                
                <script>
                    $('document').ready(function(){
                        $('.form_groups').on('click','.form_group_container .form_label button',function(){
                            $(this).next('.list').css({"display":"block"});                            
                            return false;
                        });
                        
                        $('.form_groups').on('click','.form_group_container .form_label .list li',function(){
                            var parent = $(this).closest('.form_label');
                            $('.list li',parent).removeClass('selected');
                            $(this).addClass('selected');
                            $('button',parent).text($(this).text());
                            
                            $('.list',parent).css({'display':'none'});
                            return false;
                        });
                        
                        $('.form_groups').on('mouseleave','.form_group_container .form_label .list',function(){
                            $(this).css({"display":"none"});
                        });
                    });
                </script>
                
                <script>
                    $('document').ready(function(){
                        $('.form_groups').on('keyup','.form_group_container[data-id=1] input',function(){
                            $('.form_group_container[data-id=1]:last .add_line_btn').css({'display':'block'});  
                        });
                        
                        $('.form_groups').on('click','.form_group_container[data-id=1] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=1]:first').clone();
                            
                            $('.form_group_container[data-id=1]:last').after(element);
                            $('.form_group_container[data-id=1] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                        
                        $('.form_groups').on('keyup','.form_group_container[data-id=2] input',function(){                            
                            $('.form_group_container[data-id=2]:last .add_line_btn').css({'display':'block'});  
                        });                        
                        $('.form_groups').on('click','.form_group_container[data-id=2] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=2]:first').clone();
                            $('.form_group_container[data-id=2]:last').after(element);
                            $('.form_group_container[data-id=2] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                        
                        $('.form_groups').on('keyup','.form_group_container[data-id=3] input',function(){
                            $('.form_group_container[data-id=3]:last .add_line_btn').css({'display':'block'});  
                        });
                        $('.form_groups').on('click','.form_group_container[data-id=3] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=3]:first').clone();
                            $('.form_group_container[data-id=3]:last').after(element);
                            $('.form_group_container[data-id=3] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                    });
                </script>
                <div class="form_groups" id="dealsTab">
                    TEST
                </div>
                <div class="form_groups" id="personalInfoTab">
                    <div class="form_group_container">
                        <div class="form_label">Отв-ный</div>

                        <div class="form_value">
                            <input type="text" value="<?=$this->m->_user->email?>" placeholder="...">
                        </div>
                    </div>
                    
                    <!--PHONE-->                    
                    <?php foreach($this->m->data->contacts[1] as $contact){ ?>
                        <div class="form_group_container" data-id="1">
                            <div class="form_label">
                                <button type="button"><?=$this->m->config->phoneTypes[$contact->type]?></button>
                                <ul class="list">
                                    <?php foreach($this->m->config->phoneTypes as $key=>$item){ ?>
                                        <li data-id="<?=$key?>" class="<?=$key==$contact->type?'selected':''?>"><?=$item?></li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <div class="form_value">
                                <div class='add_line_btn'>
                                    <div class='ico'></div>
                                </div>

                                <input type="text" value="<?=$contact->value?>" placeholder="...">
                                <input type="hidden" value="<?=$contact->id?>" name="id">
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="form_group_container" data-id="1">
                        <div class="form_label">
                            <button type="button">Раб.тел.</button>
                            <ul class="list">
                                <?php foreach($this->m->config->phoneTypes as $key=>$item){ ?>
                                    <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="form_value">
                            <div class='add_line_btn'>
                                <div class='ico'></div>
                            </div>

                            <input type="text" value="" placeholder="...">
                            <input type="hidden" value="0" name="id">
                        </div>  
                    </div>
                    
                    <!--EMAIL-->
                    <?php foreach($this->m->data->contacts[2] as $contact){ ?>
                        <div class="form_group_container" data-id="2">
                            <div class="form_label">
                                <button type="button"><?=$this->m->config->emailTypes[$contact->type]?></button>

                                <ul class="list">
                                    <?php foreach($this->m->config->emailTypes as $key=>$item){ ?>
                                        <li data-id="<?=$key?>" class="<?=$key==$contact->type?'selected':''?>"><?=$item?></li>
                                    <?php } ?>                                
                                </ul>
                            </div>
                            <div class="form_value">
                                <div class='add_line_btn'>
                                    <div class='ico'></div>
                                </div>
                                <input type="text" value="<?=$contact->value?>" placeholder="...">
                                <input type="hidden" value="<?=$contact->id?>" name="id">
                            </div>
                        </div>
                    <?php } ?>
                                        
                    <div class="form_group_container" data-id="2">
                        <div class="form_label">
                            <button type="button">Email раб.</button>

                            <ul class="list">
                                <?php foreach($this->m->config->emailTypes as $key=>$item){ ?>
                                    <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                                <?php } ?>                                
                            </ul>
                        </div>
                        <div class="form_value">
                            <div class='add_line_btn'>
                                <div class='ico'></div>
                            </div>
                            <input type="text" value="" placeholder="...">
                            <input type="hidden" value="0" name="id">
                        </div>
                    </div>
                    
                    <!--MESSANGER-->
                    <?php foreach($this->m->data->contacts[3] as $contact){ ?>
                        <div class="form_group_container" data-id="3">
                            <div class="form_label">

                                <button type="button"><?=$this->m->config->messangerTypes[$contact->type]?></button>

                                <ul class="list">
                                    <?php foreach($this->m->config->messangerTypes as $key=>$item){ ?>
                                        <li data-id="<?=$key?>" class="<?=$key==$contact->type?'selected':''?>"><?=$item?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="form_value">
                                <div class='add_line_btn'>
                                    <div class='ico'></div>
                                </div>
                                <input type="text" value="<?=$contact->value?>" placeholder="...">
                                <input type="hidden" value="<?=$contact->id?>" name="id">
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="form_group_container" data-id="3">
                       <div class="form_label">

                           <button type="button">Skype</button>

                           <ul class="list">
                               <?php foreach($this->m->config->messangerTypes as $key=>$item){ ?>
                                   <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                               <?php } ?>
                           </ul>
                       </div>
                       <div class="form_value">
                           <div class='add_line_btn'>
                               <div class='ico'></div>
                           </div>
                           <input type="text" value="" placeholder="...">
                           <input type="hidden" value="0" name="id">
                       </div>
                   </div>
                    
                    <div class="form_group_container">
                        <div class="form_label">
                            Адресс
                        </div>
                        <div class="form_value">
                            <input id="address" type="text" value="<?=$this->m->data->address?>" placeholder="...">
                        </div>
                    </div>
                    <div class="form_group_container suggestion_block" style="display:none;" >
                        <div class="form_label">
                            
                        </div>
                        <div class="form_value">
                            <input id="suggestion" type="text" value="" placeholder="..." onClick="$('#address').val($('#suggestion').val())">
                        </div>
                    </div>
                    
                    <div class="form_group_container">
                        <div class="form_label">
                            Почтовый Код
                        </div>
                        <div class="form_value">
                            <input id="postal_code" type="text" value="<?=$this->m->data->postal_code?>" placeholder="...">
                            
                            <input name="apartment" type="hidden" value="<?=$this->m->data->apartment?>">
                            <input name="street" type="hidden" value="<?=$this->m->data->street?>">
                            <input name="city" type="hidden" value="<?=$this->m->data->city?>">
                            <input name="country" type="hidden" value="<?=$this->m->data->country_value?>">
                            
                            <input name="lat" type="hidden" value="<?=$this->m->data->lat?>">
                            <input name="lng" type="hidden" value="<?=$this->m->data->lng?>">
                            <input name="place_id" type="hidden" value="<?=$this->m->data->place_id?>">
                        </div>
                    </div>
                                        
                    <div style="width:100%; height:300px; display:no2ne" id="map"></div>
                </div>
            </form>
        </div>
        <script>
            $('document').ready(function(){
                $('#card_fields form input').keyup(function(){
                    $('.buttons_block').css({'display':'flex'});
                });
                
                $('.list li').click(function(){                   
                    $('.buttons_block').css({'display':'flex'});
                });
            });
        </script>
        
        <script>
            $('document').ready(function(){
                $('.buttons_block .save').click(function(){
                    
                    //сохраняем данные 
                    var name = $('#personal_name').val();
                    
                    //получать контакты
                    var phones = $('.form_group_container[data-id=1]');
                    var emails = $('.form_group_container[data-id=2]');
                    var messangers = $('.form_group_container[data-id=3]');
                            
                    var data = {
                        user_id:$('.fields_block form input[name=user_id]').val(),
                        fullname : $('#personal_name').val(),
                        address : $('.fields_block form #address').val(),
                        apartment : $('.fields_block form input[name=apartment]').val(),
                        street : $('.fields_block form input[name=street]').val(),
                        city : $('.fields_block form input[name=city]').val(),
                        country : $('.fields_block form input[name=country]').val(),
                        postal_code : $('.fields_block form #postal_code').val(),
                        place_id : $('.fields_block form input[name=place_id]').val(),
                        
                        lat : $('.fields_block form input[name=lat]').val(),
                        lng : $('.fields_block form input[name=lng]').val(),
                        
                        phone_types:[],
                        phone_ids:[],
                        phones:[],
                        email_types:[],
                        email_ids:[],
                        emails:[],
                        messanger_types:[],
                        messanger_ids:[],
                        messangers:[],
                        messages:[],
                        messages_date:[],
                    }
                    
                    phones.each(function(){
                        data.phone_ids.push(parseInt($('input[name=id]',this).val()));
                        data.phone_types.push($('.list li.selected',this).attr('data-id'));
                        data.phones.push($('.form_value input',this).val());                        
                    });
                    
                    emails.each(function(){
                        data.email_ids.push(parseInt($('input[name=id]',this).val()));
                        data.email_types.push($('.list li.selected',this).attr('data-id'));
                        data.emails.push($('.form_value input',this).val());                        
                    });
                    messangers.each(function(){
                        data.messanger_ids.push(parseInt($('input[name=id]',this).val()));
                        data.messanger_types.push($('.list li.selected',this).attr('data-id'));
                        data.messangers.push($('.form_value input',this).val());                        
                    });
                    
                    $.ajax({
                        url:'/contacts/update/',
                        type:'POST',
                        data:data,
                        
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
        <div class='buttons_block'>
            <a href='' class='btn btn-primary save'>Сохранить</a>
            <a href='' class='btn btn-default cancel'>Отменить</a>
        </div>
    </div>
    
    <script>
        $('document').ready(function(){            
            $('.notes_block .notes_actions .cancel').click(function(){
                $('.notes_block .notes_actions').css({"display":'none'});
                return false;
            });
            
            $('.notes_wrapper .notes_block .input_block input').keyup(function(){
                $('.notes_block .notes_actions').css({"display":'block'});
                return false;
            });
            
            $('.note_item_wrapper .actions_block .edit').click(function(){
                console.log('Изменить');
                return false;
            });
        });
    </script>
    <script>
        app.controller('notesCtrl', ['$scope','$http',function($scope,$http){
            $scope.form = {};
            $scope.user_id = <?=$this->m->_path[2]?>;
            $scope.notes = <?=json_encode($this->m->notes)?>;
            
            $scope.formatDate = function(date){
                date *= 1000;
                //console.log(date);
                var current_date = new Date();
                
                current_date.setHours(0);
                current_date.setMinutes(0);
                current_date.setSeconds(0);
                                
                var result =(date - current_date.getTime()) / 1000 / 60/60;
                var date_value = '';
                var d = new Date(date);
                
                var hours = d.getHours();
                hours = hours < 10 ? '0'+hours : hours;
                var minutes = d.getMinutes();
                minutes = minutes < 10 ? '0'+minutes : minutes;
                var seconds = d.getSeconds();
                seconds = seconds < 10 ? '0'+seconds : seconds;

                if(result > 0 && result < 24){
                    date_value = 'Сегодня';
                }else if(Math.abs(result) < 24 && result < 0){
                    date_value = 'Вчера';
                }else if(result > 24 && result < 48){
                    date_value = 'Завтра';
                }else{                    
                    var year = d.getYear()+1900;
                    
                    var month = d.getMonth()+1;
                    month = month < 10? '0'+month : month;
                    var day = d.getDate();
                    day = day < 10? '0'+day : day;
                            
                    date_value = year + '-'+month+'-'+day;
                }
                
                return date_value+ ' ' + hours+':'+minutes+':'+seconds
            }
            
            $scope.deleteNote = function(event,id){
                console.log(id);
                $http({
                    url:'/notes/del/',
                    method:'POST',
                    data:{id:id}
                }).then(function(ret){
                    if(ret.data.status == 'success'){
                        $(event.target).closest('.note_box').remove();
                    }else{
                        console.log('ERROR');
                    }
                });
                
                event.preventDefault();
            }
            
            $scope.editNoteShow = function(event,id){
                var parent = $(event.target).closest('.note_item_wrapper');
                
                $('.message',parent).css({'display':'none'});
                $('.content .edit',parent).css({'display':'block'});
                
                //event.prevetnDefault();
            }
            $scope.blurNoteEdit = function(event,index){
                console.log('Blur');
                
                
                //var message = $('.content .edit textarea',parent).val();
                //$scope.notes[index].message = $('.content .edit textarea',parent).val();
                
                $http({
                    url:'/notes/edit/',
                    method:'POST',
                    data:{id:$scope.notes[index].id,message:$scope.notes[index].message}
                }).then(function(ret){
                    console.log(ret.data);
                    
                    var parent = $(event.target).closest('.note_item_wrapper')
                    //$('.message',parent).text(message);
                    $('.message',parent).css({'display':'block'});
                    $('.content .edit',parent).css({'display':'none'});
                });
                console.log($scope.notes[index]);
            }
            
            $scope.submitNote = function(event){
                $scope.form.user_id = $scope.user_id;
                $scope.form.date = new Date().getTime()/100;
                //console.log($scope.form);
                $http({
                    url:'/notes/add/',
                    method:'POST',
                    data:$scope.form,
                }).then(function(ret){                                            
                    //$('.notes_inner').append(json.html);
                    $scope.form.type = 'note';
                    $scope.form.date = new Date().getTime()/1000;
                    $scope.notes.unshift($scope.form);
                });
                
                event.preventDefault();
            }
            
            $scope.completeTaskForm = function(event,index){
                $('#completeTaskModal').modal('show');
                
                $scope.$emit('completeTaskCall',{
                    items: $scope.notes[index] // посылайте что хотите
                });
                
                event.preventDefault();                
            }
            
            
        }]);
    </script>
    <style>
        .note_item_wrapper.notes .message{
            display:block;
        }
        .note_item_wrapper.notes .content .edit{
            display:none;
            
        }
        .note_item_wrapper.notes .edit textarea{
            width:80%;
            resize:none;
            padding:10px;
            height:auto;            
        }
    </style>
    <style>
        #section_wrapper{
            position:absolute;
            top:0px;
            left:29%;
            right:0px;                
            bottom:0px;                
            z-index:100;
            overflow:auto;
        }
        #section_wrapper .inner_cotent{
            width:100%;
            height:100%;
        }
        #section_wrapper .notes_wrapper{
            background: white;
            border-bottom:2px solid #8591a5;
        }
        #section_wrapper .block{
            width:100%;
            height: 400px;
            background:white;
            margin-top:10px;
            margin-bottom:10px;
            padding:20px;
        }
    </style>

    <div id="section_wrapper">
        <div class="inner_content">
            <div class="notes_wrapper" ng-controller="notesCtrl">
                <div class="notes_inner" >
                    <span ng-repeat-start="item in notes"></span>

                    <div class="note_box">
                        <!--NOTE-->
                        <div class='note_item_wrapper notes' data-id="<?=$item->id?>" ng-if="item.type == 'note'">
                            <div class='icon'>
                                <svg class=""><use xlink:href="#notes--feed-note"></use></svg>
                            </div>
                            <div class='content'>
                                <div class="header">{{formatDate(item.date)}} {{item.parent_name}}</div>
                                <div class="message">{{item.message}}</div>
                                <div  class="edit"><textarea ng-model="item.message" ng-blur="blurNoteEdit($event,$index)"></textarea></div>
                            </div>

                            <div class="actions_block">
                                <a class="pin" tabindex="-1" href=""><svg style="width:16px;height:15px;fill:transparent; stroke:#feaa18"><use xlink:href="#notes--pin"></use></svg> Закрепить</a>
                                <a class="del" ng-click="deleteNoteShow($event,item.id)" tabindex="-1" href=""><svg style="width:14px;height:14px; fill:#f86161"><use xlink:href="#notes--context-delete"></use></svg> Удалить</a>
                                <a class="edit" ng-click="editNoteShow($event,item.id)" tabindex="-1" href=""><svg style="width:9.58px;height:13.813px; fill:#a3acba"><use xlink:href="#notes--context-edit"></use></svg>Изменить</a>
                            </div>
                        </div>

                        <!--History-->
                        <div class='note_item_wrapper login' data-id="<?=$item->id?>" ng-if="item.type == 'history'">
                            <span>ВХОД</span> {{formatDate(item.date)}}
                        </div>

                        <!--VIEWS-->
                        <div class='note_item_wrapper views' data-id="<?=$item->id?>" ng-if="item.type == 'views'">
                            <div class='icon'>
                                <img src="/html/images/tv_icon.png" style="max-height:21px; display:block; margin:auto; margin-top:5px;">
                            </div>

                            <div class='content'>
                                <div class="header">{{formatDate(item.date)}}</div>
                                <div class="message"><span>ПРОСМОТР</span> <img src="<?=$this->m->config->assets_url?>\{{item.channel_logo}}"> {{item.channel_name}}</div>
                            </div>                    
                        </div>

                        <!--TASKS-->
                        <div class='note_item_wrapper tasks' data-id="<?=$item->id?>" ng-if="item.type == 'tasks'">
                            <div class='icon'>
                                <img src="/html/images/alarmclock.png" style="max-height:21px; display:block; margin:auto;">
                            </div>
                            <div class='content'>
                                <div class="header">{{formatDate(item.date)}} {{item.parent_name}}</div>
                                <div class="message">{{item.message}}</div>
                                <div class="message">{{item.task_result}}</div>
                            </div>
                            <div class="actions_block">
                                <a ng-click="completeTaskForm($event,$index)" class="pin" tabindex="-1" href=""><svg style="width:16px;height:15px;fill:transparent; stroke:#feaa18"><use xlink:href="#notes--pin"></use></svg>Завершить</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <span ng-repeat-end=""></span>    
                </div>

                <div class="notes_block">
                    <form ng-submit="submitNote($event)">
                        <div class='message_block'>
                            <div class='note_type_list'>
                                <div class='current'>Примечание: </div>
                            </div>
                            <div class='input_block'>

                                <input ng-model="form.message" type="text" placeholder='' name="message">
                                <!--<input type="hidden" name="user_id" value="<?=$this->m->_path[2]?>">-->
                            </div>
                        </div>
                        <div class='notes_actions'>
                            <input type='submit' class='btn btn-primary save' value='Добавить'>
                            <input type='button' class='btn btn-default cancel' value='Отмена'>
                        </div>
                    </form>
                </div>        
            </div>  
            
            <div class="block">
                <div class="inner">
                    Блок с Подписками
                </div>
            </div>
            
            <div class="block">
                <div class="inner">
                    Блок с Просмотрами телеканалов
                </div>
            </div>
            
            <div class="block">
                <div class="inner">
                    Блок с Девайсами
                </div>
            </div>
            
            <div class="block">
                <div class="inner">
                    Еще какой то блок
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('completeTaskCtrl', ['$scope','$http',function($scope,$http){                
        $scope.submit = function(event){
            console.log($scope.taskComplete);
            
            $http({
                url:'/tasks/complete/',
                method:'POST',
                data:{id:$scope.taskComplete.id, result:$scope.taskComplete.result}
            }).then(function(ret){
                
                if(ret.data.status == 'success'){
                    $('#completeTaskModal').modal('hide');
                }
            });
            
            event.preventDefault();
        }
    }]);
</script>
<div ng-controller="completeTaskCtrl" class="modal fade" id="completeTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Завершить Задачу</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                     <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Коментарий</label>
                            </div>
                            <div class="col-sm-12">
                                {{taskComplete.comment}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Пользователь</label>
                            </div>
                            <div class="col-sm-12">
                                {{taskComplete.login}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Ответственный</label>
                            </div>
                            <div class="col-sm-12">
                                {{taskComplete.parentname}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Результат</label>
                            </div>
                            <div class="col-sm-12">
                                <textarea style="resize: none; width:100%" ng-model="taskComplete.result"></textarea>
                            </div>
                        </div>
                    </div>

                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>
</div>