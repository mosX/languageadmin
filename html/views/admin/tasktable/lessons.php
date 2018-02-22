<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
        $scope.editModal = function(event,id){
            $http({
                url:'/tasktable/lesson_edit_data/?id='+id,
                method:'POST',
            }).then(function(ret){
                //$scope.form = ret.data;
                
                $scope.$broadcast('editData', {
                    data: ret.data
                });
                
                $('#editModal').modal('show');
            });
            
            event.preventDefault();
        }
        
        $scope.submit = function(event){
            $http({
                url:location.href,
                method:'POST',
                data:{role:$scope.form.role,id:$scope.form.id}
            }).then(function(ret){                
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    console.log('ERROR');
                }
            });
            
            event.preventDefault();
        }        
        $scope.remove = function(event,id){
            $scope.$broadcast('delete', id);
            
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

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu'.DS.'tasktable'.DS.'lessons') ?>
        
        <div class="content">
            <div class="table_holder">
                <nav class='pull-right'>
                    <?= $this->m->pagesNav ?>
                </nav>
                <div class='clearfix'></div>
                
                <ul class="tabs_list">
                    <li class="<?=!$_GET['act'] || $_GET['all'] ? 'active':''?>">
                        <a href="/system/admins/">Все</a>
                    </li>
                    <li class="<?=$_GET['act'] == 'admins' ? 'active' : ''?>">
                        <a href="/system/admins/?act=admins">Админы</a>
                    </li>
                    <li class="<?=$_GET['act'] == 'operators' ? 'active':''?>">
                        <a href="/system/admins/?act=operators">Операторы</a>
                    </li>
                    <li class="<?=$_GET['act'] == 'support' ? 'active':''?>">
                        <a href="/system/admins/?act=support">Суппорт</a>
                    </li>
                </ul>
                <table class='table'>
                    <tr>
                        <th style="width:37px;"></th>
                        <th style="width:350px;">ИМЯ</th>
                        
                        
                        <th>Created At</th>
                        <th style="width:100px"></th>
                    </tr>

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>">
                            <td>
                                <!--<label class='checkbox'>
                                    <input type="checkbox" class="action_panel_triger">
                                    <div class='box'></div>
                                </label>-->
                            </td>   
                            <td class="username_td">                                
                                <div class='username_block' style='position:relative;display:inline-block;'>
                                    <a class='username' href="/contacts/details/<?= $item->id ?>/"><?= $item->name ?></a>
                                </div>
                            </td>
                            
                            <td><?=date('d M Y',strtotime($item->date))?></td>
                            <td>
                                <a ng-click="editModal($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                                <a ng-click='remove($event,<?=$item->id?>)' class="del_user_ico" href=""></a>
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
</div>