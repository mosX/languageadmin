<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.delTask = function(event,id){
            $http({
                url:'/tasks/delete/?id='+id                
            }).then(function(ret){
                console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }
            });
            event.preventDefault();
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <div class='content'>
        <?= $this->m->module('topmenu/tasks_top_menu') ?>
        <div class="table_holder">
            <nav class='pull-right'>
                <?= $this->m->pagesNav ?>
            </nav>
            <div class='clearfix'></div>
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
                    <th style="width:37px;"></th>
                    <th>ДАТА ИСПОЛНЕНИЯ / ОТВ.</th>
                    <th>КОНТАКТ ИЛИ СДЕЛКА</th>
                    <th>ТЕКСТ ЗАДАЧИ</th>
                    <th>РЕЗУЛЬТАТ</th>
                    <th></th>
                </tr>
                <style>
                    .icon.follow{
                        background: url('/html/images/sprite2.png');
                        background-position: 0 -215px;
                        display:inline-block;
                        vertical-align: middle;
                        width:17px;
                        height:17px;
                        margin-right: 10px;
                    }
                </style>
                <script>
                    $('document').ready(function(){
                        $('.actions_panel .close_task').click(function(){
                            $('#closeTaskModal').modal('show');
                            return false;
                        });
                    });
                </script>
                <?php foreach ($this->m->data as $item){ ?>
                    <tr data-id="<?=$item->id?>">
                        <td style='vertical-align: middle'>
                            <!--<label class='checkbox'>
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>-->
                        </td>
                        <td>
                            <div><?=date("M d H:i",strtotime($item->date))?></div>
                            <?=$item->parentname?>
                            
                            <!--<div class="actions_panel" style='align-items: center'>
                                <a class="open_task" href=""><span></span>открыть задачи</a>
                                <a class="close_task" href=""><span></span>закрыть задачи</a>
                                <a ng-click="delTask($event,<?=$item->id?>)" class="del" href=""><span></span>удалить</a>
                            </div>-->
                        </dt>
                        <td>
                            <?=$item->username?>
                        </td>
                        <td>
                            <span class="icon follow"></span><?=$item->comment?>
                        </td>
                        <td><?=$item->result?></td>
                        <td>
                            <a class="open_task_ico" href=""></a>
                            <a class="close_task_ico" href=""></a>
                            <a ng-click="delTask($event,<?=$item->id?>)" class="del_ico" href=""></a>
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

<div class="modal fade" id="closeTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action='/test'>
                <div class="modal-header">
                    <h5 class="modal-title">Результат</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input name='id' value='' type='hidden' class='form-control'>
                        <textarea class="form-control" name="result"></textarea>
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