<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        
        $scope.editForm = function(event,id){
            $http({
                url:'/subscriptions/plans_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.$broadcast('editData', {                    
                    data: ret.data
                });                
            });
            event.preventDefault();
        }
               
        $scope.deleteConfrimation = function(event,id){
            $('#confirmModal').modal('show');
            $scope.id = id;
            
            event.preventDefault();
        }
        
        $scope.delete = function(){            
             $http({
                url:'/subscriptions/delete/?id='+$scope.id,
                method:'GET',
            }).then(function(ret){                
                if(ret.data.status == 'success'){
                    location.reload();
                }else{
                    
                }
            });
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/subscriptions_plans') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="active">
                    <a href="/subscriptions/">Все</a>
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
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Каналов</th>
                    <th>Цена</th>
                    <th>Дата</th>
                    <th style="width:100px"></th>
                </tr>
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <!--
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

                            <a href="/subscriptions/plan_channels/<?=$item->id?>/"><?=$item->name?></a>
                        </td>
                        <td style="white-space: normal">
                            <?=$item->description?>
                        </td>
                        <td>
                            <?=$item->channels?>
                        </td>
                        <td>
                            <?=number($item->price)?>
                        </td>
                        <td>
                            <?= date("d M Y",strtotime($item->date)) ?>
                        </td>
                        <td>
                            <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                            <a ng-click="deleteConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
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
    <div class="modal fade" id="confirmModal" ng-controller="confirmationCtrl" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удалить Баннер</h5>
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

