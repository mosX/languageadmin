<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
        console.log('START PAGE CONTROLLER');
        $scope.channel_id  = null;
        $scope.channel_name  = null;
        
        $scope.editForm = function(event,id){
            console.log(id);
            console.log('edit FORM');
            $http({
                url:'/questions/question_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                $scope.$broadcast('editData', {
                    data: ret.data
                });
                
            });
            event.preventDefault();
        }
        
        $scope.editImageForm = function(event,id){                 
            $http({
                url:'/lessons/question_image_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                $scope.$broadcast('editImageData', {
                    data: ret.data
                });
                $('#editImageQuestionModal').modal('show');
            });
            event.preventDefault();
        }
        
        
        
        $scope.deleteChannelConfrimation = function(event,id,name){
            $('#confirmModal').modal('show');
            $scope.channel_id = id;
            $scope.channel_name = name;
            
            event.preventDefault();
        }
        
        $scope.remove = function(event,id){
            $scope.$broadcast('delete', id);
        }
        
        /*$scope.confirmDeleting = function(event){
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
        
        $scope.publish = function(event,id){
            var status = 0;
            if($(event.target).hasClass('off')) status = 1;
            
            $http({
                url:'/lessons/publish_question/?id='+id,
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
        <?= $this->m->module('topmenu'.DS.'lessons'.DS.'question_collections') ?>

        <div class="content">
            <div class="table_holder">
                <nav class='pull-right'>
                    <?= $this->m->pagesNav ?>
                </nav>
                <div class='clearfix'></div>
                <ul class="tabs_list">
                    <li class="<?=!$this->m->_path[2] || $this->m->_path[2] == 'all' ? 'active':''?>">
                        <a href="/lessons/">Все каналы</a>
                    </li>
                    <li class="<?=$this->m->_path[2] == 'active' ? 'active':''?>">
                        <a href="/lessons/index/active/">Активные</a>
                    </li>
                    <li class="<?=$this->m->_path[2] == 'unactive' ? 'active':''?>">
                        <a href="/lessons/index/unactive/">Отключенные</a>
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
                        <th>Вопрос</th>
                        <th>Ответ</th>
                        <th>Балов</th>
                        <th>Ответов</th>
                        <th style="width:100px;"></th>
                        <th style="width:100px;"></th>
                    </tr>

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>" class="<?=$item->user_id ? 'personal':''?>">
                            <td><?=$item->question_id?></td>
                                
                            <td class="username_td" style='overflow:hidden;'>
                                <a href="/lessons/answer_collections/<?=$item->question_id?>/"><?=$item->value?></a>
                            </td>
                            
                            <?php if($item->type == 1){ ?>
                                <td><?=$item->answer?></td>
                            <?php }else if($item->type == 2){ ?>
                                <td><img style="max-width:50px; max-height: 50px;" src="<?=$this->m->config->assets_url?>/images/<?=$item->filename?>"></td>
                            <?php } ?>
                            <td><?=$item->score?></td>
                            <td><?=$item->answers?></td>
                            
                            <td>
                                <a ng-click="editForm($event,<?=$item->question_id?>)" class="edit_tags_ico" href=""></a>
                                <!--<?php if($item->type == 1){ ?>
                                    <a ng-click="editForm($event,<?=$item->question_id?>)" class="edit_tags_ico" href=""></a>
                                <?php }else{ ?>
                                    <a ng-click="editImageForm($event,<?=$item->question_id?>)" class="edit_tags_ico" href=""></a>
                                <?php } ?>-->
                                <a ng-click="remove($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
                            </td>
                            <td><div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->published ? 'on':'off'?>"></div></td>
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
<script>
    /*$( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        startDate:'01-01-1996',
        firstDay: 1
    });*/
</script>
