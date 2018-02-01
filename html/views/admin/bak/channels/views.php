<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        console.log('START PAGE CONTROLLER');

    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
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
                    <th>Пользователь</th>                    
                    <th>Канал</th>
                    <th>SSID</th>
                    <th>Начало</th>
                    <th>Проверка</th>
                    <th>Статус</th>
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
                <?php foreach ($this->m->data as $item) { ?>
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
                                <a class="del_user" href=""><span></span>удалить</a>
                            </div>
                            
                            <?=$item->login?>
                        </td>
                        
                        <td>
                            <?=$item->name?>
                        </td>
                        <td>
                            <?=$item->sessid?>
                        </td>
                        <td>
                            <?=$item->date?>
                        </td>
                        <td>
                            <?=$item->last_check?>
                        </td>
                        <td>
                            <?=$item->status?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
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
