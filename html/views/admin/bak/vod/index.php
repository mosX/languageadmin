<script>
    app.controller('vodCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.editForm = function(event,id){
            $http({
                url:'/vod/data/?id='+id,
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
            $scope.genre_id = id;
            //$scope.channel_name = name;
            
            event.preventDefault();
        }
        
        $scope.deleteGenre = function(){            
             $http({
                url:'/cms/delete_genre/?id='+$scope.genre_id,
                method:'GET',
            }).then(function(ret){                
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
                url:'/pages/publish/?id='+id+'&status='+status,
                type:'GET',
            }).then(function(ret){
                //console.log(ret.data);
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

<div id="page_wrapper" ng-controller="vodCtrl">
    <?= $this->m->module('topmenu/movies') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="<?=!$_GET['act'] || $_GET['act'] == 'all' ? 'active':''?>">
                    <a href="/pages/">Все</a>
                </li>
                <li class="<?=$_GET['act'] == 'published' ? 'active':''?>">
                    <a href="/pages/?act=published">Опубликованны</a>
                </li>
                <li class="<?=$_GET['act'] == 'unpublished' ? 'active':''?>">
                    <a href="/pages/?act=unpublished">Не Опубликованны</a>
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
                    <th>Постер</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Тип</th>
                    <th>Жанры</th>
                    
                    <th>Published</th>
                    <th>Дата</th>
                    <th style="width:100px"></th>
                </tr>
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <img style="max-width:100px; max-height:100px;" src="<?=$this->m->config->assets_url.DS.'posters'.DS.$item->poster?>">
                        </td>
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
                        <td style="white-space: nowrap; overflow:hidden;text-overflow:ellipsis"><?=$item->description?></td>
                        <td>
                            <?php
                                switch($item->type){
                                    case 1: echo "Фильм";break;
                                    case 2: echo "Мультфильм";break;
                                    case 3: echo "Телесериал";break;                                    
                                }
                            ?>
                        </td>
                        
                        <td style="white-space: normal; font-size:12px;">
                            <?php if($item->genres){ ?>
                                <?php 
                                    $str = '';
                                    foreach($item->genres as $item2){
                                        $str .= $this->m->genres_list[$item2]->name.', ';
                                    } 
                                    echo substr($str,0,-2);
                                ?>
                            <?php } ?>
                        </td>
                        
                        
                        <td>
                            <div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->published ? 'on':'off'?>"></div>
                        </td>
                        <td><?= date("d M Y",strtotime($item->date)) ?></td>
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
                $scope.deleteGenre();

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