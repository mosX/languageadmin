<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>ЛОГОТИПЫ</div>
            
            <div class="buttons_wrapper">
                <a href="/channels/sequence/" class="svg_pipe <?=$this->_action == 'sequence' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
                <a href="/channels/" class="svg_list <?=$this->_action == 'index' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--list"></use></svg>
                </a>
            </div>
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
            <!--<a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>-->
            <!--<a data-toggle="modal" data-target="#addLogoModal" class="button add_deal">+ НОВЫЙ ЛОГОТИП</a>-->
            
            <div data-toggle="modal" data-target="#addLogoModal" class="uploadFileBtn button add_deal">+ НОВЫЙ ЛОГОТИП
                <iframe id="hiddenIframeUpload" src="{{'/logos/addlogo/'}}"></iframe>
            </div>            
        </div>
    </div>
</div>

<script>
    function addSuccess(filename,id){
        location.href = location.href;
    }
    function addError(error){

    }
</script>


<script>
    

    app.controller('logosEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
            
        $scope.$on('logoModalInit',function(e){
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
        })
        
        //$scope.logo_id
        /*$scope.form = {};
        $scope.form.epg = '0';
        $scope.form.group = '5';
        $scope.settings = "1";
        $scope.channels_list = [];
                
        //$('#addChannelModal').modal('show');
        
        $scope.$on('editData', function (event, ret) {
            console.log(ret.data); // Данные, которые нам прислали
            
            $scope.form  = ret.data.channel;
            $scope.settings = ($scope.form.setting_id).toString();
            $scope.filename = ret.data.channel.filename;
            
            //console.log('SETTINGS',$scope.form.settings);
            
            $scope.channels_list  = ret.data.list;
        });
        
        $scope.submit = function(event){
            console.log($scope.form);
            if($('#editChannelModal input[name=logo]').val()){
                $scope.form.logo_id = $('#editChannelModal input[name=logo]').val();
            }
            
            if($scope.user_id){
                $scope.form.user_id = $scope.user_id;
            }
            
            console.log('LOGO',$scope.form.logo_id);
            //console.log($scope.form.logo_id);
            $http({
                method:'POST',
                url:'/channels/edit/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
                $scope.errors = ret.data.message;
                //console.log("EDIT ERRORS",$scope.errors);
                //location.reload();
                location.href = location.href;
            });
            
            event.preventDefault();
        }
        
        $scope.getChannelsList = function(){
            console.log($scope.settings);
            
            $http({
                methdo:'GET',
                url:'/channels/list/?id='+$scope.settings,                
            }).then(function(ret){
                console.log(ret.data);
                $scope.channels_list = ret.data;
            });
        }*/
    }]);
</script>

<div ng-controller="logosEditModalCtrl" class="modal fade" id="logosEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Логотип</strong></p></h4>
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
<!--
<script>
    app.controller('addLogoModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.$on('logoModalInit',function(e){
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
        });
    }]);
</script>

<div ng-controller="addLogoModalCtrl" class="modal fade" id="addLogoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Логотип</strong></p></h4>
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
-->