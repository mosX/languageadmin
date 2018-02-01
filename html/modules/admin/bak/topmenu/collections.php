<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Коллекции</div>
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
            <a data-toggle="modal" data-target="#addCollectionModal" class="button add_page">+ НОВАЯ КОЛЛЕКЦИЯ</a>
        </div>
    </div>
</div>

<script>
    app.controller('collectionsEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){            
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

<!--<div ng-controller="collectionsEditModalCtrl" class="modal fade" id="collectionsEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Коллекцию</strong></p></h4>
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
</div>-->

<script>
    app.controller('addCollectionModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        /*$scope.$on('logoModalInit',function(e){
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
        });*/
            
        $scope.form = {type:1};

        $scope.submit = function(event){
            $http({
                url:'/collections/',
                method:'POST',
                data:$scope.form
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    console.log('ERROR');
                }
            });
            event.preventDefault();
        }
    }]);
</script>

<div ng-controller="addCollectionModalCtrl" class="modal fade" id="addCollectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Коллекцию</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="name" value="" ng-model="form.name" type="text">
                                        <div class="error name_error"></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Тип</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.type">
                                            <option value=""></option>
                                            <option value="1">Баннеры</option>
                                            <option value="2">Каналы</option>
                                        </select>
                                    </div>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Описание</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="name" value="" ng-model="form.description" type="text"></textarea>
                                        <div class="error description_error"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="1" ng-model="form.type">
                                <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
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
    app.controller('editCollectionModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.$on('editData',function(e,ret){
            $scope.form = ret.data;            
            $('#editModal').modal('show');
        });
        
            $scope.form = {};
            $scope.send = {};
            //$scope.form.banner_id = '0';
            //$scope.page_id = '<?=$this->m->_path[2]?>';
            
            $scope.submit = function(event){
                //$scope.form.banner_id = $('#banner_assignment_id_input').val();

                $scope.send.id = $scope.form.id;
                $scope.send.name = $scope.form.name;
                $scope.send.description = $scope.form.description;
                console.log($scope.send);
                $http({
                    url:location.href,
                    method:'POST',
                    data:$scope.send
                }).then(function(ret){
                    console.log(ret);
                    if(ret.data.status == 'success'){
                        location.href = location.href;
                    }else{
                        console.log('error');
                    }
                });
                
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

<div ng-controller="editCollectionModalCtrl" class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Колекцию</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">                            
                             <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input class="form-control" name="name" value="" ng-model="form.name" type="text">
                                        <div class="error name_error"></div>
                                    </div>
                                </div>
                            </div>
                         
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Описание</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="name" value="" ng-model="form.description" type="text"></textarea>
                                        <div class="error description_error"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="id" value="" ng-model="form.id">
                                <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
