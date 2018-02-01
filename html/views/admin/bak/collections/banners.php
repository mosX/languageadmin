<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        
        /*$scope.editForm = function(event,id){
            //console.log(id);
            //$('#editBannerModal').modal('show');
            
            
            $http({
                url:'/collections/banner_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.$broadcast('editData', {
                    data: ret.data
                });                
            });
            event.preventDefault();
        }*/
            
        $scope.showBannerEditModal = function(event,id){
            /*$scope.$emit('showEditBannerModal',id);
            event.preventDefault();*/
            
            $http({
                url:'/collections/banner_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
                
                $scope.$emit('editData',{
                    data: ret.data
                });                
            });
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
                url:'/collections/delete_assignment/?id='+$scope.banner_id,
                method:'GET',
            }).then(function(ret){
                console.log(ret.data);
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
                url:'/collections/publish_banner/?id='+id+'&status='+status,
                type:'GET',
            }).then(function(ret){
                console.log(ret.data);
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
        
        $scope.showBannerList = function(el){            
            $('#banner_list',el).css({'display':'block'});
            $('#banner_list .list_content',el).animate({'right':'0px'},400,function(){
                
            });
        }
        
        $scope.hideBannerList = function(el){
            $('#banner_list .list_content',el).animate({'right':'-400px'},400,function(){
                $('#banner_list',el).css({'display':'none'});
            });
        }
        
        $('#banner_list').click(function(e){
            e.preventDefault();
            e.stopPropagation();
        });
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/collection_banners') ?>

    <div class="content">
        <div class="table_holder">
            <ul class="tabs_list">
                <li class="<?=!$_GET['act'] || $_GET['act'] == 'all' ? 'active':''?>">
                    <a href="/collections/banners/<?=$this->m->_path[2]?>/">Все</a>
                </li>
                <li class="<?=$_GET['act'] == 'published' ? 'active':''?>">
                    <a href="/collections/banners/<?=$this->m->_path[2]?>/?act=published">Опубликованны</a>
                </li>
                <li class="<?=$_GET['act'] == 'unpublished' ? 'active':''?>">
                    <a href="/collections/banners/<?=$this->m->_path[2]?>/?act=unpublished">Не Опубликованны</a>
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
                    <th>Порядок</th>
                    <th>Название</th>
                    <th>Published</th>
                    <th>Дата</th>
                    <th></th>
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
                            <div class="actions_panel">
                                <!--<a ng-click="editForm($event,<?=$item->id?>)" class="edit_tags" href=""><span></span>редактировать</a>
                                <a ng-click="deleteBannerConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user" href=""><span></span>удалить</a>-->
                            </div>
                            <div class="banner_container">
                                <img src="<?=$this->m->config->assets_url?>/banners/<?=$item->filename?>">
                            </div>
                        </td>
                        <td><?=$item->position?></td>
                        <td><?=$item->name?></td>
                        <td>
                            <div ng-click="publish($event,<?=$item->id?>)" class="trigger <?=$item->published ? 'on':'off'?>"></div>
                        </td>
                        <td><?= date("d M Y",strtotime($item->date)) ?></td>
                        <td>
                            <a ng-click="showBannerEditModal($event,'<?=$item->id?>')" class="edit_tags_ico" href=""></a>
                            <a ng-click="deleteBannerConfrimation($event,<?=$item->id?>,'<?=$item->name?>')" class="del_user_ico" href=""></a>
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

    <div class="modal fade" ng-controller="confirmationCtrl" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Удалить контакт</h5>
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