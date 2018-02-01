<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        console.log('START PAGE CONTROLLER');
        $scope.channel_id  = null;
        $scope.channel_name  = null;
        
        $scope.editForm = function(event,id){
            console.log(id);
            $http({
                url:'/channels/editdata/?id='+id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                $scope.$broadcast('editData', {
                    data: ret.data
                });
                $('#editChannelModal').modal('show');
            });
            event.preventDefault();
        }
        
        $scope.deleteChannelConfrimation = function(event,id,name){
            $('#confirmModal').modal('show');
            $scope.channel_id = id;
            $scope.channel_name = name;
            
            event.preventDefault();
        }
        
        $scope.confirmDeleting = function(event){
            $http({
                url:'/channels/delete/?id='+$scope.channel_id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.reload();
                }else{
                    
                }
            });
            
            event.preventDefault();
        }
        
        $scope.publish = function(event,id){
            var status = 0;
            if($(event.target).hasClass('off')) status = 1;
            
            $http({
                url:'/channels/publish/?id='+id+'&status='+status,
                type:'GET',
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    if(ret.data.result == '1'){
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

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        
        <?= $this->m->module('topmenu'.DS.'lessons'.DS.'lessons') ?>

        <div class="content">
            <div class="table_holder">
                <nav class='pull-right'>
                    <?= $this->m->pagesNav ?>
                </nav>
                <div class='clearfix'></div>
                <ul class="tabs_list">
                    <li class="<?=!$this->m->_path[2] || $this->m->_path[2] == 'all' ? 'active':''?>">
                        <a href="/channels/">Все каналы</a>
                    </li>
                    <li class="<?=$this->m->_path[2] == 'active' ? 'active':''?>">
                        <a href="/channels/index/active/">Активные</a>
                    </li>
                    <li class="<?=$this->m->_path[2] == 'unactive' ? 'active':''?>">
                        <a href="/channels/index/unactive/">Отключенные</a>
                    </li>
                </ul>
                <style>
                    .table .personal {        
                        background : #f8faff;
                        font-weight: bolder;
                    }
                </style>
                <table class="table">
                    <tr>
                        
                        <th style="width:60px;">ID</th>
                        <th>НАЗВАНИЕ</th>                        
                        <th style="width:100px;"></th>                        
                        <th style='width:70px;'></th>
                    </tr>

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>" class="<?=$item->user_id ? 'personal':''?>">
                            <td>
                                <?=$item->id?>                                
                            </td>
                            <td class="username_td">
                                <a href="/lessons/question_collections/<?=$item->id?>"><?=$item->name?></a>
                            </td>
                            
                            <td>
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                                <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
                            </td>
                            <td><div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->status ? 'on':'off'?>"></div></td>
                        </tr>
                    <?php } ?>
                </table>                
            </div>
            <nav class='pull-right'>
                <?= $this->m->pagesNav ?>
            </nav>
        </div>
    </div>
    
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удалить Канал</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST' ng-submit="confirmDeleting($event)">
                    <div class="modal-body">
                        <p>Вы действительно хотите удалить «<span class="username">{{channel_name}}</span>»?</p>

                        <p>Все данные, как-либо связанные с «{{channel_name}}», будут удалены. Восстановить удалённые данные будет возможно.</p>
                    </div>
                    <div class="modal-footer">
                        <input type='hidden' ng-model="channel_id" name='id' value=''>
                        <input type="submit" class="btn btn-secondary" value='Подтвердить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
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
