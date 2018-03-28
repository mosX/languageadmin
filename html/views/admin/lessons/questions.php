<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        console.log('START PAGE CONTROLLER');
        $scope.channel_id  = null;
        $scope.channel_name  = null;
        
        $scope.editForm = function(event,id){
            console.log(id);
            $http({
                url:'/questions/question_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                $scope.$broadcast('editData', {
                    data: ret.data
                });
                //$('#editQuestionModal').modal('show');
            });
            event.preventDefault();
        }
        
        /*$scope.deleteChannelConfrimation = function(event,id,name){
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
        }*/
        
        $scope.remove = function(event,id){
            $scope.$broadcast('delete', id);
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
        <?= $this->m->module('topmenu'.DS.'lessons'.DS.'questions') ?>

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
                        <th>ВОПРОС</th>
                        <th>ОТВЕТОВ</th>
                        <th>В Уроках</th>
                        <th style="width:100px;"></th>
                        <th style="width:100px"></th>
                    </tr>

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>" class="<?=$item->user_id ? 'personal':''?>">
                            <td><?=$item->id?></td>
                            
                            <td><a href="/lessons/answer_collections/<?=$item->id?>"><?=$item->value?></a></td>
                            <td><?=(int)$item->answers?></td>
                            <td><?=(int)$item->lessons?></td>
                                
                            <td>
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                                <!--<a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>-->
                                <a ng-click="remove($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
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