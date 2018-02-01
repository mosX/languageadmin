<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        console.log('START PAGE CONTROLLER');
        $scope.channel_id  = null;
        $scope.channel_name  = null;
        
        $scope.user_id = <?=(int)$this->_path[2]?>;
        
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
    }]);
</script>
<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu/channels_top_menu') ?>

        <div class="content">
            <div class="table_holder">
                <ul class="tabs_list">
                    <li class="active">
                        <a href="">Все клиенты</a>
                    </li>
                    <li>
                        <a href="">Неразобранное</a>
                    </li>
                </ul>

                <table class="table">
                    <tr>
                        <th style="width:60px;"></th>                    
                        <th>НАЗВАНИЕ</th>
                        <th>Logo</th>
                        <th>Ассоциация</th>
                        <!--<th>ССЫЛКА</th>
                        <th>АРХИВ</th>-->
                        <th>EPG</th>
                        <th>Просматривают</th>
                    </tr>

                    <script>
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
                    </script>

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>">
                            <td>
                                <?=$item->number?>
                                <label class='checkbox' style="display:inline-block;vertical-align: top;">
                                    <input type="checkbox" class="action_panel_triger">
                                    <div class='box'></div>
                                </label>                            
                            </td>
                            <td class="username_td">
                                <div class="actions_panel">
                                    <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                    <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                                </div>
                                <a class='username' href="/contacts/details/<?= $item->id ?>/"><?= $item->fullname ?></a>

                                <?=$item->name?>
                            </td>
                            <td>
                                <?php if($item->filename){ ?>
                                <img src="<?=$this->m->config->assets_url?>/<?=$item->filename?>">
                                <?php }?>
                            </td>
                            <td>
                                <?=$item->association?>
                            </td>
                            <!--<td>
                                <?=$item->url?>
                            </td>
                            <td>
                                <?=$item->archive_url?>
                            </td>-->
                            <td>
                                <?php if($item->epg){?>
                                    <?=$item->settings_name?> -> <?=$item->channel_name?>
                                <?php } ?>
                            </td>
                            <td>
                                <?=$item->watching?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
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
