<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
        $scope.editForm = function(event,id){
            $http({
                url:'/collections/collection_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.$broadcast('editData', {
                    data: ret.data
                });                
            });
            event.preventDefault();
        }
        
        $scope.deleteConfrimation = function(event,id,name){
            $('#confirmModal').modal('show');
            $scope.channel_id = id;
            
            event.preventDefault();
        }
        
        $scope.delete = function(){
             $http({
                url:'/collections/delete_channel/?id='+$scope.channel_id,
                method:'GET',
            }).then(function(ret){
                //console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.reload();
                }else{
                    
                }
            });
        }
        
        $scope.publish = function(event,id){
            var status = 0;
            if($(event.target).hasClass('off')) status = 1;
            
            $http({
                url:'/collections/publish/?id='+id+'&status='+status,
                type:'GET',
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    if(ret.data.result == 'on'){
                        $(event.target).removeClass('off').addClass('on');
                    }else{
                        $(event.target).removeClass('on').addClass('off');
                    }
                }else{
                    
                }
            });
            
            event.preventDefault();
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/collections') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="<?=!$_GET['act'] || $_GET['act'] == 'all' ? 'active':''?>">
                    <a href="/collections/">Все</a>
                </li>
                <li class="<?=$_GET['act'] == 'published' ? 'active':''?>">
                    <a href="/collections/?act=published">Опубликованны</a>
                </li>
                <li class="<?=$_GET['act'] == 'unpublished' ? 'active':''?>">
                    <a href="/collections/?act=unpublished">Не Опубликованны</a>
                </li>
            </ul>
            <table class="table">
                <tr>
                    <th style="width:60px;"></th>
                    <th>НАЗВАНИЕ</th>
                    <th>Тип</th>
                    <th>Описание</th>
                    <th>Published</th>
                    <th>Дата</th>
                    <th style="width:100px"></th>
                </tr>
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
<!--                        <?=$item->number?>
                            <label class='checkbox' style="display:inline-block;vertical-align: top;">
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>                            -->
                        </td>
                        <td class="username_td">
                            <!--<div class="actions_panel">
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                            </div>-->

                            <?php 
                                switch($item->type){
                                    case 1:$page = 'banners'; break;
                                    case 2:$page = 'channels'; break;
                                }
                            ?>
                            <a href="/collections/<?=$page?>/<?=$item->id?>/"><?=$item->id?> <?=$item->name?></a>
                        </td>
                        <td>
                            <?php 
                                switch($item->type){
                                    case 1:echo 'Баннеры'; break;
                                    case 2:echo 'Каналы'; break;
                                }
                            ?>
                        </td>
                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow:hidden;" title="<?= $item->description ?>">
                            <?= $item->description ?>
                        </td>
                        <td>
                            <div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->published ? 'on':'off'?>"></div>
                        </td>
                        <td><?= date("d M Y",strtotime($item->date)) ?></td>
                        <td>
                            <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                            <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<!--<script>
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
                    -->
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
                    <div class="form-group">
                        <select class="form-control">
                            <option><?=$this->m->_user->email?></option>
                        </select>
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
</div>
<script>
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        startDate:'01-01-1996',
        firstDay: 1
    });
</script>
