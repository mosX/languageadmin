<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        
        /*$scope.editForm = function(event,id){
            $http({
                url:'/banners/banner_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.$broadcast('editData', {
                    data: ret.data
                });                
            });
            event.preventDefault();
        }*/
        
        $scope.showBannerEditModal = function(event,id){
            $scope.$emit('showEditBannerModal',id);
            event.preventDefault();
        }
               
        $scope.deleteBannerConfrimation = function(event,id,name){
            $('#confirmModal').modal('show');
            $scope.banner_id = id;
            //$scope.channel_name = name;
            
            event.preventDefault();
        }
        
        $scope.deleteBanner = function(){
             $http({
                url:'/banners/delete_banner/?id='+$scope.banner_id,
                method:'GET',
            }).then(function(ret){
                //console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.reload();
                }else{
                    
                }
            });
        }
        
        $scope.getCollectionList = function(callback){
            $http({
                url:'/banners/getcollectionlist/',
                type:'GET'
            }).then(function(ret){
                $scope.collections = ret.data;
                if(callback != undefined) callback(ret);
                
                
                /*console.log(ret.data);
                $scope.collections = ret.data;
                $('#addBannerModal').modal('show');*/
            });
        };
        
        $scope.newBannerForm = function(event){
            $scope.getCollectionList(function(ret){
                console.log(ret.data);
                
                $('#addBannerModal').modal('show');
            });
            
            /*$http({
                url:'/banners/getcollectionlist/',
                type:'GET'
            }).then(function(ret){
                console.log(ret.data);
                $scope.collections = ret.data;
                $('#addBannerModal').modal('show');
            });*/
            
            event.preventDefault();
        }
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/banners') ?>

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
            <style>
                .banner_container img{
                    max-width:100px;
                    max-height: 100px;
                }
            </style>
            <table class="table">
                <tr>
                    <th style="width:60px;"></th>
                    <th>Preview</th>
                    <th>Название</th>
                    <th>Коллекции</th>
                    <th>Страницы</th>
                   
                    <th>Дата</th>
                    <th style='width:100px;'></th>
                </tr>
                
                <?php foreach ($this->m->data as $item) { ?>
                    <tr data-id="<?=$item->id?>">
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
                                <a ng-click="deleteBannerConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>
                            </div>-->

                            <div class="banner_container">
                                <img src="<?=$this->m->config->assets_url?>/banners/<?=$item->filename?>">
                            </div>
                        </td>
                        
                        <td><?=$item->name?></td>
                        
                        <td>
                          <?php foreach($item->collections as $collection_id=>$collection){ ?>
                                <div><a href="/collections/banners/<?=$collection_id?>"><?=$collection?></a></div>
                            <?php } ?>
                        </td>
                        
                        <td>
                          <?php foreach($item->pages as $pages){ ?>
                                <div><a href="/pages/"><?=$pages?></a></div>
                            <?php } ?>
                        </td>
                        
                        <td><?= date("d M Y",strtotime($item->date))?></td>
                            
                        <td>
                            <a title='Редакировать' ng-click="showBannerEditModal($event,<?=$item->id?>)" class="edit_tags_ico" href=""></a>
                            <a title='Удалить' ng-click="deleteBannerConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
    
    <script>
        app.controller('confirmationCtrl', ['$scope','$http',function($scope,$http,$userinfo){
            $scope.submit = function(event){
                $scope.deleteBanner();

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
