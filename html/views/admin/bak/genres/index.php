<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        
        $scope.editForm = function(event,id){
            //console.log(id);
            console.log('edit Form');
            $http({
                url:'/genres/genre_data/?id='+id,
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
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/genres') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="active">
                    <a href="/genres/">Все</a>
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
                    <th style="width:90px;">Порядок</th>
                    <th style="width:90px;">Логотип</th>
                    <th>Название</th>
                    <th>Каналов</th>
                    <th>Дата</th>
                    <th style="width:100px"></th>
                </tr>
                <!--<script>
                    $('document').ready(function(){
                        $('.table .username_td').hover(function(){
                            $('.edit_btn',this).css({'display':'inline-block'});
                        },function(){
                            $('.edit_btn',this).css({'display':'none'});
                        });
                        
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
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <?=$item->sequence?>
                            <!--<label class='checkbox' style="display:inline-block;vertical-align: top;">
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>-->
                        </td>
                        <td>
                            <?php if($item->filename){ ?>
                                <img src="<?=$this->m->config->assets_url?>/genres/thumb_<?=$item->filename?>">
                            <?php } ?>
                        </td>
                        
                        <td class="username_td">
                            <!--<div class="actions_panel">
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                <a ng-click="deleteConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                            </div>-->

                            <?=$item->name?>
                        </td>
                        <td><?=$item->cnt?></td>

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
