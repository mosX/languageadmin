<script>
    app.config(['$sceProvider', function($sceProvider) {
        $sceProvider.enabled(false);
    }]);

    app.controller('pageCtrl', ['$scope','$sce','$http',function($scope,$sce,$http,$userinfo){
        $scope.logo_id = 0;
        $sce.trustAsUrl('0');  
        
        $scope.editForm = function(event,id){
            console.log(id);
            $scope.logo_id = id;
            $sce.trustAs($sce.URL,id);
            $scope.$broadcast('editData');
            
            event.preventDefault();
        }
        
        $scope.delLogo = function(event,id){
            $http({
                url:'/logos/remove/?id='+id,
                method:'GET'
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    console.log('SUCCESS');
                }else{
                    console.log('ERROR');
                }
            });
            
            event.preventDefault();
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/logos') ?>

    <div class="content">
        <div class="table_holder">
            <nav class='pull-right'>
                <?= $this->m->pagesNav ?>
            </nav>
            
            <div class='clearfix'></div>
            <ul class="tabs_list">
                <li class="active">
                    <a href="">Все логотипы</a>
                </li>
            </ul>
            
            <table class="table">
                <tr>
                    <th style="width:60px;"></th>
                    <th>Логотип</th>
                    <th>Закреплен</th>
                    <th style='width:200px;'>Дата</th>
                    <th style="width:100px;"></th>
                </tr>
           
                <?php foreach ($this->m->data as $item){ ?>
                    <tr data-id="<?=$item->id?>">
                        <td>
                            <?=$item->number?>
                            <!--<label class='checkbox' style="display:inline-block;vertical-align: top;">
                                <input type="checkbox" class="action_panel_triger">
                                <div class='box'></div>
                            </label>-->
                        </td>
                        
                        <td class="username_td">
                            <!--<div class="actions_panel">
                                <a ng-click='editForm($event,"<?=$item->id?>")' class="edit_tags" href=""><span></span>редактировать</a>
                                <a ng-click='delLogo($event,"<?=$item->id?>")' class="del_user" href=""><span></span>удалить</a>
                            </div>-->
                            <a class='username' href="/contacts/details/<?= $item->id ?>/"><?= $item->fullname ?></a>
                            
                            <?php if($item->filename){ ?>
                                <img src="<?=$this->m->config->assets_url?>/logos/small_<?=$item->filename?>">
                            <?php }?>
                        </td>
                        
                        <td>
                            <?php foreach($item->channels as $item2){ ?>
                                <div><?=$item2->name?></div>
                            <?php } ?>
                        </td>
                        
                        <td><?=date("Y-m-d H:i:s",strtotime($item->date))?></td>
                        <td>
                            <a ng-click='editForm($event,"<?=$item->id?>")' class="edit_tags_ico" href=""></a>
                            <a ng-click='delLogo($event,"<?=$item->id?>")' class="del_user_ico" href=""></a>
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
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        startDate:'01-01-1996',
        firstDay: 1
    });
</script>
