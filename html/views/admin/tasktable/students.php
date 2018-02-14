<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
        $scope.editModal = function(event,id){
            $http({
                url:'/system/getdata/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.form = ret.data;
                
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
        <?= $this->m->module('topmenu'.DS.'tasktable'.DS.'students') ?>
        
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
                </ul>
                <table class='table'>
                    <tr>
                        <th style="width:37px;"></th>
                        <th style="width:350px;">ИМЯ</th>
                        <th>ПРАВА</th>
                        <th>КЕМ СОЗДАН</th>                        
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
                                <!--<div class="actions_panel">
                                    <a ng-click="editModal($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                    <a ng-click='showBlockModal($event,<?=$item->id?>)' class="del_user" href=""><span></span>блок</a>
                                </div>-->
                                
                                <div class='username_block' style='position:relative;display:inline-block;'>
                                    <a class='username' href="/contacts/details/<?= $item->id ?>/"><?= $item->email ?></a>
                                </div>
                            </td>
                            
                            <td>
                                <?php
                                    switch($item->gid){
                                        case 10:echo 'Admin';break;
                                        case 20:echo 'Support';break;
                                        case 30:echo 'Operator';break;
                                    }
                                ?>
                            </td>
                            
                            <td style="position:relative;">
                                <?=$item->partner_email?>
                            </td>
                            
                            <td><?=date('d M Y',strtotime($item->date))?></td>
                            <td>
                                <a ng-click="editModal($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                                <a ng-click='showBlockModal($event,<?=$item->id?>)' class="del_user_ico" href=""></a>
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action='/' ng-submit='submit($event)'>
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать пользователя {{form.email}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">                        
                        <div class="form-group">
                            <div class='row'>
                                <div class='col-sm-12'>
                                    <label>Роль</label>
                                </div>
                                <div class='col-sm-12'>
                                    <select ng-model="form.role" class="form-control">
                                        <option value="30">Оператор</option>
                                        <option value="20">Супорт</option>
                                        <option value="10">Админ</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <input name='id' value='{{form.id}}' type='hidden' class='form-control'>   
                        <input type="submit" class="btn btn-secondary" value='Подтвердить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>