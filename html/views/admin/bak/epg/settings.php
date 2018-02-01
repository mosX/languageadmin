<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        console.log('START PAGE CONTROLLER');        
        
        $scope.editForm = function(event,id){
            console.log(id);
            $('#EPGEditModalCtrl').modal('show');
            
            event.preventDefault();
        }
        
        $scope.updateEPGs = function(event,id){
            
            $http({
                url:'/epg/update/?id='+id,
                
            }).then(function(ret){
                console.log(ret.data);
            });;
            
            event.preventDefault();
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/epg_settings_top_menu') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="active"><a href="">Все</a></li>
                <li><a href="">Активные</a></li>
            </ul>
            
            <table class="table">
                <tr>
                    <th style="width:60px;"></th>
                    <th style="width:300px;">НАЗВАНИЕ</th>
                    <th>ССЫЛКА</th>
                    <th>ОБНОВЛЕНО</th>
                </tr>
                             
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <?=$item->number?>
                            <label class='checkbox' style="display:inline-block;vertical-align: top;">
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>
                        </td>
                        <td>
                            <div class="actions_panel">
                                <a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>ред.</a>
                                <a class="del_user" href=""><span></span>удалить</a>
                                <a ng-click="updateEPGs($event,<?=$item->id?>)" class="update_ico" href=""><span></span>обновить</a>
                            </div>
                            <a href="/epg/programs/?setting=<?=$item->id?>"><?=$item->name?></a>
                        </td>                        
                        <td class="username_td">
                            
                            
                            <?=$item->url?>
                        </td>
                        <td>
                            <?=$item->updated_to?>
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
