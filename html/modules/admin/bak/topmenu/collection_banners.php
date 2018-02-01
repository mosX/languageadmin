<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Баннеры</div>
        </div>

        <div class="filter_overlay"></div>

        <form action='' class="search_wrapper">
            <script>
                $('document').ready(function(){
                    $('#search_input').click(function(){
                        $('#top_menu .filter_wrapper').css({'display':'flex'});
                        $('.filter_overlay').css({'display':'block'});
                    });

                    $('.filter_overlay').click(function(){
                        $('.filter_wrapper').css({'display':'none'});
                        $('.filter_overlay').css({'display':'none'});
                    });
                });
            </script>

            <div class="filter_wrapper">
                <div class="sidebar">
                    <ul>
                        <li><a href="">Полный список</a></li>
                        <li><a href="">Контакты без задач</a></li>
                        <li><a href="">Контакты с просроченным</a></li>
                        <li><a href="">Без сделок</a></li>
                        <li><a href="">Удаленные</a></li>
                    </ul>
                </div>
                <div class="filter">
                    <div class="row">
                        <div class="col-sm-4">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Название" name="name" value="<?=$_GET['name']?>" class="form-control">        
                                    </div>
                                </div>                            
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Добавлено</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="С" name="date_from" value="<?=$_GET['date_from']?>" class="datepicker form-control">        
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="По" name="date_to" value="<?=$_GET['date_to']?>" class="datepicker form-control">        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Принять">
                                <input type="button" class="btn btn-secondary reset_filter" value="Сбросить">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $('document').ready(function(){
                    $( ".datepicker" ).datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        startDate:'01-01-1996',
                        firstDay: 1
                    });
                });
            </script>
            <div class='filter_inner'>
                <div class="input_block">
                    <input type="text" id="search_input" placeholder="Поиск и фильтр" name='search' value='<?=$_GET['search']?>'>
                </div>
                <label for="search_input" class='options_cnt'>2 опции</label>
                <label for="search_input" class='ico'>
                    <svg class="svg_search_icon"><use xlink:href="#common--filter-search"></use></svg>
                </label>
            </div>        
        </form>

        <div class="actions">
            <a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>
            <!--<a data-toggle="modal" data-target="#addLogoModal" class="button add_deal">+ НОВЫЙ ЛОГОТИП</a>-->
            
            <!--<div data-toggle="modal" data-target="#addLogoModal" class="uploadFileBtn button add_deal">+ НОВЫЙ БАННЕР
                <iframe id="hiddenIframeUpload" src="{{'/cms/addbanner/'}}"></iframe>
            </div>-->
            
            <div data-toggle="modal" data-target="#addModal" class="button add_deal">+ НОВЫЙ БАННЕР</div>
        </div>
    </div>
</div>

<script>
    app.controller('pagesEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        /*$scope.$on('logoModalInit',function(e){
            console.log($scope.logo_id);
            $http({
                url:'/logos/getfilename/?id='+$scope.logo_id,
                method:'GET',                
            }).then(function(ret){
                console.log(ret.data);
                
                if(ret.data.status == 'success'){
                    $scope.filename = ret.data.filename;
                    $('#logosEditModal').modal('show');
                }
            });
        })*/
    }]);
</script>

<div ng-controller="pagesEditModalCtrl" class="modal fade" id="logosEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Страницу</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">                            
                            <div class="col-sm-8">
                                <div class="uploadFileBtn">Загрузить
                                    <iframe id="hiddenIframeUpload" src="{{'/logos/editlogo/?id='+logo_id}}"></iframe>
                                </div>
                            </div>
                            <div class="col-sm-4">                                
                                <img class="preview_logo" style="display:block; margin:auto;" src="<?=$this->config->assets_url?>/{{filename}}">
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        function editImage(filename,id){
                            $('.preview_logo').attr('src',filename);                            
                        }
                        function editError(error){

                        }
                    </script>
                   
                    <input type="hidden" name="id" value="" ng-model="logo_id">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('addModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
            $scope.form = {};            
            $scope.form.type = 'add';
                        
            $scope.selectBanner = function(id,filepath){
                $scope.hideBannerList();

                $('#addModal #add_modal_preview').attr('src',filepath).css({'display':'block'});
                $('#addModal #banner_id_input').val(id);               
            }
            
            $scope.submit = function(event){
                $scope.form.banner_id = $('#addModal #banner_id_input').val();
                
                console.log($scope.form);
                $http({
                    url:location.href,
                    method:'POST',
                    data:$scope.form
                }).then(function(ret){
                    if(ret.data.status == 'success'){
                        location.href = location.href;
                    }else{
                        console.log('error');
                    }
                });
                
                event.preventDefault();
            }
            
            $rootScope.$on('addBannerSuccess',function(e,data){
                console.log('addBannerSuccess');
                $('#addModal #add_modal_preview').attr('src','<?=$this->config->assets_url?>/banners/'+data.filename).css({'display':'block'});
                $('#addModal #banner_id_input').val(data.id);
                console.log(data);
            });
            
            $scope.showBannerEditModal = function(event){                
                $scope.$emit('showEditBannerModal');
                event.preventDefault();
            }
            
            $scope.showBannerAddModal = function(event){
                //$('#addBannerModal').modal('show');
                $scope.$emit('showAddBannerModal');
                event.preventDefault();
            }
    }]);
</script>

<style>
    #addBannerModal .preview img{
        max-width:100px;
        max-height:100px;
    }
</style>

<style>
    #banner_list{
        display:none;
        position:fixed;
        top:0px;
        left:0px;
        width:100%;
        height: 100%;                
        z-index:120;
    }

    #banner_list .tint{
        position:absolute;
        left:0px;
        top:0px;
        width: 100%;
        height: 100%;
        z-index:0;
    }
    #banner_list .list_content{
        position:absolute;                
        top:0px;
        left:auto;
        bottom:0px;
        right:-400px;
        width:400px;
        background: white;
        border-left: 2px solid #ddd;
        padding:20px;
        overflow:hidden;
        z-index:1;
    }
    #banner_list .item img{
        max-width:300px;
        max-height: 150px;
    }
    #banner_list .item{
        cursor:pointer;
        text-align: center;
        border-bottom:1px solid #ddd;
        padding-bottom:10px;
        padding-top:10px;
    }
    #banner_list .inner{
        position:relative;
        top:0px;
        left:0px;
    }
</style>
<div ng-controller="addModalCtrl" class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div id="banner_list">
        <div class="tint" ng-click="hideBannerList('#addBannerModal')"></div>
        <div class="list_content">
            <div class="scroller" scroller data-parent="#banner_list" data-inner="#banner_list .inner"></div>
            <div class="inner">
                <?php foreach($this->banners_list as $item){ ?>
                    <div class="item" ng-click="selectBanner(<?=$item->id?>,'<?=$this->config->assets_url?>/banners/<?=$item->filename?>')">
                        <img src="<?=$this->config->assets_url?>/banners/<?=$item->filename?>">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header">
                    <div class="btn-group">
                        <a href="" ng-click="showBannerAddModal($event)" class="btn btn-primary">Добавить Баннер</a>
                        <a href="" ng-click="showBannerList('#addModal')" class="btn btn-primary">Выбрать Баннер</a>
                    </div>
                </h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Порядок</label>
                            </div>

                            <div class="col-sm-8">
                                <input class="form-control" name="sequence" value="" ng-model="form.sequence" type="text">
                                <div style="color:#666; font-size: 12px;">Не обязтельное поле. Указывает порядок отображения в Каруселе</div>
                            </div>
                        </div>                                
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <img id="add_modal_preview" src="" style="max-width:100px; max-height: 100px;display:none">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                        <input type="hidden" id="banner_id_input" value="">
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    app.controller('editModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){            
            $scope.form = {};
            $scope.form.type = 'add';
            
            $scope.selectBanner = function(id,filepath){
                $scope.hideBannerList();

                $('#editModal #add_modal_preview').attr('src',filepath).css({'display':'block'});
                $('#editModal #banner_id_input').val(id);
            }
            
            $scope.submit = function(event){
                $scope.form.banner_id = $('#editModal #banner_id_input').val();
                
                console.log($scope.form);
                $http({
                    url:location.href,
                    method:'POST',
                    data:$scope.form
                }).then(function(ret){
                    if(ret.data.status == 'success'){
                        location.href = location.href;
                    }else{
                        console.log('error');
                    }
                });
                
                event.preventDefault();
            }
            
            console.log('editModalCtrl');
            $rootScope.$on('editData',function(e,ret){
                
                $scope.form.id = ret.data.id;
                $scope.form.position = ret.data.position;
                $('#editModal #banner_id_input').val(ret.data.banner_id);
                $('#editModal').modal('show');
            });
            
            $rootScope.$on('addBannerSuccess',function(e,data){
                console.log('addBannerSuccess');
                $('#editModal #add_modal_preview').attr('src','<?=$this->config->assets_url?>/banners/'+data.filename).css({'display':'block'});
                $('#editModal #banner_id_input').val(data.id);
                console.log(data);
            });
            
            $scope.showBannerEditModal = function(event){
                $scope.$emit('showEditBannerModal');
                event.preventDefault();
            }
            
            $scope.showBannerAddModal = function(event){
                $scope.$emit('showAddBannerModal');
                event.preventDefault();
            }
    }]);
</script>

<style>
    #editBannerModal .preview img{
        max-width:100px;
        max-height:100px;
    }
</style>

<div ng-controller="editModalCtrl" class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div id="banner_list">
        <div class="tint" ng-click="hideBannerList('#editModal')"></div>
        <div class="list_content">
            <div class="scroller" scroller data-parent="#banner_list" data-inner="#banner_list .inner"></div>
            <div class="inner">
                <?php foreach($this->banners_list as $item){ ?>
                    <div class="item" ng-click="selectBanner(<?=$item->id?>,'<?=$this->config->assets_url?>/banners/<?=$item->filename?>')">
                        <img src="<?=$this->config->assets_url?>/banners/<?=$item->filename?>">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header">
                    <div class="btn-group">
                        <a href="" ng-click="showBannerAddModal($event)" class="btn btn-primary">Добавить Баннер</a>
                        <a href="" ng-click="showBannerList('#editModal')" class="btn btn-primary">Выбрать Баннер</a>
                    </div>
                </h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Порядок</label>
                            </div>

                            <div class="col-sm-8">
                                <input class="form-control" name="position" value="" ng-model="form.position" type="text">
                                <div style="color:#666; font-size: 12px;">Не обязтельное поле. Указывает порядок отображения в Каруселе</div>
                            </div>
                        </div>                                
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <img id="add_modal_preview" src="" style="max-width:100px; max-height: 100px;display:none">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                        <input type="hidden" id="banner_id_input" value="">
                        <input type="hidden" value="" ng-model='form.id'>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<?=$this->module('modals/banners')?>