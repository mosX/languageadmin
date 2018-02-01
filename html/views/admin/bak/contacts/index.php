<script>
    app.controller('usersCtrl', ['$scope','$http','$userinfo',function($scope,$http,$userinfo){
        $scope.user = {};
        $scope.block = {};
        $scope.userinfo = function(event,id){
            $userinfo.init(id,function(data){
                $scope.user = data;
                $scope.$broadcast('getUser',$scope.user);
            });
            
            if(event)event.preventDefault();
        }
        
        //$scope.userinfo(null,8);
        
        $scope.showBlockModal = function(event,user_id){
            $('#blockModal').modal('show');
            $scope.block.user_id = user_id;            
            event.preventDefault();
        }
        
        $scope.blockUserChannels = function(event){
            var id = $('#blockModal input[name=id]').val();
            var date = $('#blockModal input[name=date]').val();
            
            $http({
                url:'/channels/block/',
                method:'POST',
                data:{id:id,date:date}
            }).then(function(ret){
                console.log(ret.data)
            });
            
            event.preventDefault();
        }
        
        $scope.blockUser = function(event,id){
            $http({
                method: 'GET',
                url:'/userinfo/block/?id='+id,
            }).then(function(res){
                if(res.data.status == 'error'){
                    alert(res.data.message);
                }else if(res.data.status == 'success'){

                    if(res.data.result == 'on'){
                        $(event.target).removeClass('disableuser').addClass('enableuser');
                    }else if(res.data.result == 'off'){
                        $(event.target).removeClass('enableuser').addClass('disableuser');
                    }
                }
            });
        }
        
        $scope.showTagsModal = function(event,id){
            $('#tagsModal form input[name=id]').val(id);
            $('#tagsModal').modal('show');
            
            event.preventDefault();
        }
        
        $scope.showTasksModal = function(event,id){
            $('#taskModal').modal('show');
            event.preventDefault();
        }
        
    }]);
</script>
<script>
    $('document').ready(function(){
        $('.timepicker').datetimepicker({
            locale: 'ru',
            format: 'DD-MM-YYYY HH:mm'
        });
    });
</script>

<div ng-controller="usersCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu/contacts_top_menu') ?>
        <?= $this->m->module('userinfo') ?>

        <div class="content">
            <div class="table_holder">
                <nav class='pull-right'>
                    <?= $this->m->pagesNav ?>
                </nav>
                <div class='clearfix'></div>
                
                <ul class="tabs_list">
                    <li class="active">
                        <a href="">ВСЕ</a>
                    </li>
                    <li>
                        <a href="">С ПОДПИСКОЙ</a>
                    </li>
                </ul>
                <table class='table table-hover'>
                    <tr>
                        <!--<th style="width:37px;"></th>-->
                        <th style="width:350px;">ИМЯ</th>
                        <th>EMAIL</th>
                        <th>ТЕЛЕФОН</th>
                        <th>АКТИВНАЯ ПОДПИСКА</th>
                        <th>ДАТА ОКОНЧАНИЯ</th>
                        <!--<th>ДОП.КОНТАКТЫ</th>-->
                        <!--<th>АДРЕСС</th>-->
                        <!--<th>ПРОСМАТРИВАЕТ</th>-->
                        <th>Created At</th>
                        <th></th>
                    </tr>

                    <!--<script>
                        $('document').ready(function(){
                            $('.table .edit_btn').click(function(){
                                $('.edit_panel').remove();
                                $(this).css({'display':'none'});

                                var parent = $(this).closest('.td');
                                var username = $('.username',parent).text();

                                $(parent).append('<div class="edit_panel">'
                                                    +'<input type="text" value="'+username+'" placeholder="">'

                                                    +'<div class="buttons">'
                                                        +'<a href="" class="btn btn-primary save">Сохранить</a>'
                                                        +'<a href="" class="btn btn-secondary cancel">Отменить</a>'
                                                    +'</div>'
                                                +'</div>');

                                $('.edit_panel input',parent).focus();
                            });
                            $('.table').on('click','.edit_panel .cancel',function(){
                                $(this).closest('.edit_panel').remove();
                                return false;
                            });
                        });
                    </script>-->
<style>
    .contacts_block{
        cursor: pointer;
        position:relative;
        height: 50px;
    }
    
    .contacts_block .inner{
        padding:10px;
        padding-top:0px;
        padding-bottom:0px;
        height: 36px;
        overflow:hidden;
    }
    .contacts_block:hover .inner{
        background: white;
        padding-bottom:10px;
        z-index:10;
        position:absolute;
        border: 1px solid #ddd;
        overflow: visible;
        height: auto;
        min-width:200px;
    }
    .contacts_block .dots{
        display:block;        
        position:absolute;
        bottom:0px;
        left:50%;
        margin-left:-15px;
        
    }
    .contacts_block .dots i{
        width:5px;
        height:5px;
        display:block;
        float:left;
        background: #4c8bf7;
        border-radius: 5px;
        margin-right: 3px;
    }
</style>
                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>">
                            <!--<td>
                                <label class='checkbox'>
                                    <input type="checkbox" class="action_panel_triger">
                                    <div class='box'></div>
                                </label>
                            </td>   -->
                            <td class="username_td">
                                <div class="actions_panel">
                                    <a ng-click="showTasksModal($event,<?=$item->id?>)" class="add_task" href=""><span></span>доб.задачу</a>
                                    <a ng-click='showBlockModal($event,<?=$item->id?>)' class="del_user" href=""><span></span>блок</a>
                                    <a ng-click="showTagsModal($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>ред.теги</a>
                                    <!--<a ng-click="userinfo($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>INFO</a>-->
                                </div>
                                <div class='username_block' style='position:relative;display:inline-block;'>
                                    <a class='username' href="/contacts/details/<?= $item->id ?>/"><?= $item->firstname ?> <?=$item->lastname?></a>
                                    <div class='hover_block'>
                                        <div ng-click="userinfo($event,<?=$item->id?>)" class='ico info_ico'></div>
                                        <!--<a href="/channels/personalization/<?=$item->id?>" class='ico channels_ico'></a>-->
                                    </div>
                                </div>
                                
                                <div class="warning_block" style="position:absolute;right:10px; top:3px;">
                                    <?php foreach($item->blocks as $item2){ ?>
                                        <div title='<?=(int)$item2->till_date ? 'Заблокирован до '.$item2->till_date : "Заблокирован постоянно"?>' class="ico block_ico"></div>      
                                    <?php } ?>
                                </div>

                                <?php if($item->tags){?>
                                    <div class='tags_panel'>
                                        <?php foreach($item->tags as $item2){ ?>
                                            <a href=''><?=$item2->name?></a>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </td>
                            <td><?=$item->email?></td>
                            <td><?=$item->phone?></td>                                
                            <td></td>

                            <td></td>
                            
                            <!--<td style="position:relative;">
                                <div class="contacts_block">
                                    <div class="inner">
                                        <?php $cnt = 0 ?>
                                        <?php for($i=1;$i<=3;$i++){ ?>
                                             <?php foreach ($item->contacts[$i] as $item2) { ?>
                                                <?php $cnt++ ?>
                                                 <div><?= $item2->value ?></div>
                                             <?php } ?>
                                        <?php } ?>
                                     </div>
                                    <?php if($cnt > 3){ ?>
                                        <div class="dots"><i></i><i></i><i></i></div>
                                    <?php } ?>
                                </div>
                            </td>-->
                            
                            <!--<td style="white-space: nowrap; text-overflow:ellipsis;overflow:hidden;">
                                <?= $item->address ?>
                            </td>-->
                            <!--<td>
                                <?php  foreach($item->views as $item2){ ?>
                                    <div><?=$item2->name?></div>
                                <?php } ?>                            
                            </td>-->
                            <td><?=date('d M Y',strtotime($item->date))?></td>
                                
                            <td>
                                <a ng-click="showTasksModal($event,<?=$item->id?>)" class="add_task_ico" href=""></a>
                                <a ng-click='showBlockModal($event,<?=$item->id?>)' class="del_user_ico" href=""></a>
                                <a ng-click="showTagsModal($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <nav class='pull-right'>
                <?= $this->m->pagesNav ?>
            </nav>            
        </div>
        
    </div>

    <div class="modal fade" id="blockModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action='/' ng-submit='blockUserChannels($event)'>
                    <div class="modal-header">
                        <h5 class="modal-title">Добавить Блок</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Если вы хотите заблокировать перманентно тогда не указывайте дату.</p>

                        <div class="form-group">                        
                            <div class='row'>
                                <div class='col-sm-12'>
                                    <label>Дата</label>
                                </div>
                                <div class='col-sm-12'>
                                    <input name='date' type='text' id="datetimepicker" class='form-control timepicker'>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input name='id' value='{{block.user_id}}' type='hidden' class='form-control'>   
                        <input type="submit" class="btn btn-secondary" value='Заблокировать'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('document').ready(function(){
            $('#tagsModal form').submit(function(){
                var name = $('input[name=name]',this).val();
                var user_id = $('input[name=id]',this).val();

                $.ajax({
                    url:'/tags/add/',
                    type:'POST',
                    data:{name:name,user_id:user_id},
                    success:function(msg){
                        console.log(msg);
                    }
                });
                return false;
            });
        });
    </script>
    <div class="modal fade" id="tagsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action='/test'>
                    <div class="modal-header">
                        <h5 class="modal-title">Теги</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input name='id' value='' type='hidden' class='form-control'>
                            <input name='name' type='text' class='form-control'>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-secondary" value='Сохранить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('document').ready(function(){
            $('#taskModal form').submit(function(){
                var user_id = parseInt($('input[name=id]',this).val());
                var comment = $('textarea[name=comment]',this).val();
                var date = $('input[name=date]',this).val();
                var time = $('select[name=time] option:selected',this).val();

                $.ajax({
                    url:'/tasks/add/',
                    type:'POST',
                    data:{user_id:user_id,comment:comment,date:date,time:time},
                    success:function(msg){
                        console.log(msg);
                    }
                });

                return false;
            });
        });
    </script>
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить задачу</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST'>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class='row'>
                                <div class='col-sm-8'>
                                    <input name='date' type='text' placeholder='Выберите дату' class='datepicker form-control'>
                                </div>
                                <div class='col-sm-4'>
                                    <?php $time = strtotime(date('Y-m-d',time())) ?>
                                    <select class='form-control' name='time'>
                                        <?php for($i=0;$i < 86400;$i+=1800){ ?>
                                            <option value='<?=$i?>'><?=date("H:i",$time+$i)?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group">
                            <select class="form-control">
                                <option><?=$this->m->_user->email?></option>
                            </select>
                        </div>-->
                        <div class="form-group">                            
                            <?=$this->m->_user->email?>                           
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name='comment' placeholder="Добавить комментарий"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type='hidden' name='id' value=''>
                        <input type="submit" class="btn btn-secondary" value='Сохранить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--
    <script>
        $('document').ready(function(){
            //$('#confirmModal').modal('show');
            $('#confirmModal form').submit(function(){
                var user_id = parseInt($('input[name=id]',this).val());

                $.ajax({
                    url:'/users/del_user/',
                    type:'POST',
                    data:{user_id:user_id},
                    success:function(msg){
                        console.log(msg);
                    }
                });

                return false;
            });
        });
    </script>
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удалить контакт</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST'>
                    <div class="modal-body">
                        <p>Вы действительно хотите удалить «<span class="username">Имя не указано</span>»?</p>

                        <p>Все данные, как-либо связанные с «Имя не указано», будут удалены. Восстановить удалённые данные будет невозможно.</p>
                    </div>
                    <div class="modal-footer">
                        <input type='hidden' name='id' value=''>
                        <input type="submit" class="btn btn-secondary" value='Подтвердить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>-->
    <script>
        $( ".datepicker" ).datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            startDate:'01-01-1996',
            firstDay: 1
        });
    </script>
</div>