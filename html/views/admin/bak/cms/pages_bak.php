<div id="page_wrapper">
    <?= $this->m->module('topmenu/pages') ?>

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
                    <th>Описание</th>
                    <th>Дата</th>
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
                                <a ng-click="deleteChannelConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                            </div>

                            <a href="/cms/page_banners/<?=$item->id?>/"><?=$item->name?></a>
                        </td>
                        <td>
                            <?= $item->description ?>
                        </td>
                        <td>
                            <?= $item->date ?>
                        </td>
                        
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<script>
    $('document').ready(function(){
        $('#tagsModal form').submit(function(){
            var name = $('input[name=name]',this).val();
            var user_id = $('input[name=id]',this).val();
            
            $.ajax({
                url:'/tags/add/',
                type:'POST',
                data:{name:name,user_id:user_id},
                success:function(msg){
                    console.log(msg);
                }
            });
            return false;
        });
    });
</script>
<div class="modal fade" id="tagsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action='/test'>
                <div class="modal-header">
                    <h5 class="modal-title">Теги</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input name='id' value='' type='hidden' class='form-control'>
                        <input name='name' type='text' class='form-control'>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" class="btn btn-secondary" value='Сохранить'>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('document').ready(function(){
        $('#taskModal form').submit(function(){
            var user_id = parseInt($('input[name=id]',this).val());
            var comment = $('textarea[name=comment]',this).val();
            var date = $('input[name=date]',this).val();
            var time = $('select[name=time] option:selected',this).val();
            
            $.ajax({
                url:'/tasks/add/',
                type:'POST',
                data:{user_id:user_id,comment:comment,date:date,time:time},
                success:function(msg){
                    console.log(msg);
                }
            });
            
            return false;
        });
    });
</script>
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить задачу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action='' method='POST'>
                <div class="modal-body">
                    <div class="form-group">
                        <div class='row'>
                            <div class='col-sm-8'>
                                <input name='date' type='text' placeholder='Выберите дату' class='datepicker form-control'>
                            </div>
                            <div class='col-sm-4'>
                                <?php $time = strtotime(date('Y-m-d',time())) ?>
                                <select class='form-control' name='time'>
                                    <?php for($i=0;$i < 86400;$i+=1800){ ?>
                                        <option value='<?=$i?>'><?=date("H:i",$time+$i)?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="form-control">
                            <option><?=$this->m->_user->email?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name='comment' placeholder="Добавить комментарий"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type='hidden' name='id' value=''>
                    <input type="submit" class="btn btn-secondary" value='Сохранить'>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $('document').ready(function(){
        //$('#confirmModal').modal('show');
        $('#confirmModal form').submit(function(){
            var user_id = parseInt($('input[name=id]',this).val());
            
            $.ajax({
                url:'/users/del_user/',
                type:'POST',
                data:{user_id:user_id},
                success:function(msg){
                    console.log(msg);
                }
            });
            
            return false;
        });
    });
</script>
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить контакт</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action='' method='POST'>
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
<script>
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        startDate:'01-01-1996',
        firstDay: 1
    });
</script>
