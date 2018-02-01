<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){        
        $scope.editForm = function(event,id){
            $http({
                url:'/collections/channel_data/?id='+id,
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
                url:'/collections/publish_channel/?id='+id+'&status='+status,
                type:'GET',
            }).then(function(ret){
                console.log(ret.data);
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
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/collection_channels') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="<?=!$_GET['act'] || $_GET['act'] == 'all' ? 'active':''?>">
                    <a href="/collections/channels/<?=$this->m->_path[2]?>">Все</a>
                </li>
                <li class="<?=$_GET['act'] == 'published' ? 'active':''?>">
                    <a href="/collections/channels/<?=$this->m->_path[2]?>/?act=published">Опубликованны</a>
                </li>
                <li class="<?=$_GET['act'] == 'unpublished' ? 'active':''?>">
                    <a href="/collections/channels/<?=$this->m->_path[2]?>/?act=unpublished">Не Опубликованны</a>
                </li>
            </ul>
            <style>
                .banner_container img{
                    max-width:100px;
                    max-height: 100px;
                }
            </style>
            <table class="table">   
                <tr>
                    <th style="width:60px;"></th>
                    <th>Канал</th>
                    <th>Лого</th>
                    <th>Позиция</th>
                    <th>Published</th>
                    <th>Дата</th>
                    <th></th>
                </tr>
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <!--<?=$item->number?>
                            <label class='checkbox' style="display:inline-block;vertical-align: top;">
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>-->
                        </td>
                        
                        <td class="username_td">
                            <!--<div class="actions_panel">
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                <a ng-click="deleteConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                            </div>-->

                            <?=$item->name?>                            
                        </td>
                        <td>
                            <?php if($item->filename){ ?>
                                <img src="<?=$this->m->config->assets_url?>/logos/thumb_<?=$item->filename?>">
                            <?php }?>
                        </td>
                        <td>
                            <?=$item->position?>
                        </td>
                        
                        <td>
                            <div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->published ? 'on':'off'?>"></div>
                        </td>
                        
                        <td>
                            <?= date("d M Y",strtotime($item->date)) ?>
                        </td>
                        <td>
                            <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""><span></span></a>
                            <a ng-click="deleteConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""><span></span></a>
                        </td>
                        
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    <script>
        app.controller('confirmationCtrl', ['$scope','$http',function($scope,$http,$userinfo){
            $scope.submit = function(event){
                $scope.delete();

                event.preventDefault();
            };    
        }]);
    </script>

    <div class="modal fade" ng-controller="confirmationCtrl" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удалить контакт</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST' ng-submit="submit($event)">
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
</div>