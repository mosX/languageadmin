<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        
    }]);
</script>

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu/radio') ?>

        <div class="content">
            <div class="table_holder">
                <nav class='pull-right'>
                    <?= $this->m->pagesNav ?>
                </nav>
                <div class='clearfix'></div>
                <ul class="tabs_list">
                    <li class="<?=!$this->m->_path[2] || $this->m->_path[2] == 'all' ? 'active':''?>">
                        <a href="/channels/">Все радио</a>
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
                        <th style="width:60px;"></th>
                        <th>НАЗВАНИЕ</th>
                        <th>Ссылка</th>                        
                        <th>Добавлен</th>
                        <th style="width:100px"></th>
                    </tr>

                   <!-- <script>
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

                    <?php foreach ($this->m->data as $item){ ?>
                        <tr data-id="<?=$item->id?>" class="<?=$item->user_id ? 'personal':''?>">
                            <td>                                
                                <!--<label class='checkbox' style="display:inline-block;vertical-align: top;">
                                    <input type="checkbox" class="action_panel_triger">
                                    <div class='box'></div>
                                </label>                            -->
                            </td>
                            <td class="username_td">
                                <!--<div class="actions_panel">
                                    <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                    <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                                </div>-->

                                <?=$item->name?>
                            </td>
                            <td><?=$item->url?></td>
                            <td><?=date("Y-m-d H:i:s",strtotime($item->date))?></td>
                            <td>
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                                <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
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
