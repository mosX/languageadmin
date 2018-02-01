<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>КАНАЛЫ</div>
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
            <script>                
                function reloadChannels(){
                    console.log('RELOAD CHANNELS');
                    $.ajax({
                        url:'http://195.88.159.227:5500/?action=reloadChannels',
                        type:'GET',
                        success:function(msg){
                            console.log(msg);
                        }
                    });
                }
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
            <a onClick="reloadChannels(); return false;" class="button add_deal">RELOAD CHANNELS</a>
            <a data-toggle="modal" data-target="#addChannelModal" class="button add_deal">+ НОВЫЙ КАНАЛ</a>
        </div>
    </div>
</div>

<script>
    app.controller('channelsEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
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
            $scope.form.streamid = ret.data.channel.association;
            
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
        }
    }]);
</script>

<div ng-controller="channelsEditModalCtrl" class="modal fade" id="editChannelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Канал</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Название</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.name">
                                <div class="error name_error"></div>
                            </div>
                            <div ng-show="errors.name" class="error">{{errors.name}}</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Описание</label>
                            </div>
                            <div class="col-sm-8">
                                <textarea class="form-control" ng-model="form.description" style="height: 140px;"></textarea>
                            </div>
                            <div ng-show="errors.description" class="error">{{errors.description}}</div>
                        </div>
                    </div>
                    
                    <!--<div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Номер</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.number">
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Stream ID</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.stream_id">
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Жанр</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.group_id">
                                    <?php foreach($this->groups as $item){ ?>
                                        <option value="<?=$item->id?>"><?=$item->name?></option>
                                    <?php } ?>
                                </select>
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Ссылка</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="url" value="" ng-model="form.url">
                                <div class="error link_error"></div>
                            </div>
                        </div>
                    </div>
                 
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Логотип</label>
                            </div>
                            <div class="col-sm-4">
                                <div class="uploadFileBtn">Загрузить
                                    <iframe id="hiddenIframeUpload" src="/channels/loadeditlogo/"></iframe>
                                </div>
                            </div>
                            <div class="col-sm-4">                                
                                <img class="preview_logo" style="display:block; margin:auto;" src="<?=$this->config->assets_url?>/logos/small_{{filename}}">
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>EPG</label>
                            </div>
                            
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control" ng-model="form.epg">
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control" ng-model="settings" ng-change="getChannelsList()">
                                            <option value="0">Настройки</option>
                                            <?php foreach($this->settings as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control" ng-model="form.epg">
                                            <option value="0">Каналы</option>
                                            <option ng-repeat="item in channels_list" value="{{item.id}}">{{item.name}}</option>                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="error logo_id_error"></div>
                            </div>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>EPG</label>
                            </div>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" ng-model="form.epg">
                            </div>
                        </div>
                    </div>
                    <script>
                        function editImage(filename,id){
                            console.log(filename);
                            if($('#addChannelModal').css('display') != 'none'){
                                $('#addChannelModal input[name=logo]').val(id);
                                $('#addChannelModal .preview_logo').attr('src',filename);
                            }else if($('#editChannelModal').css('display') != 'none'){
                                $('#editChannelModal input[name=logo]').val(id);
                                $('#editChannelModal .preview_logo').attr('src',filename);
                            }
                        }
                        function editError(error){

                        }
                    </script>
                   
                    <div>
                        <textarea class="pattern_block panel" style="padding:20px;display:none; width:100%; height:auto;"></textarea>
                    </div>
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                    <input type="hidden" name="logo" value="" ng-model="form.logo_id">
                    <input type="hidden" name="id" value="" ng-model="form.id">
                    <input type="hidden" name="user_id" value="" ng-model="form.user_id">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('channelsModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        $scope.form.group_id = '5';
        $scope.form.epg = '0';
        $scope.settings = "0";
        $scope.channels_list = [];
        
        //$('#addChannelModal').modal('show');
        
        $scope.submit = function(event){
            console.log($scope.form);
            $scope.form.logo_id = $('#addChannelModal input[name=logo]').val();
            
            if($scope.user_id){
                $scope.form.user_id = $scope.user_id;
            }
            
            $http({
                method:'POST',
                url:'/channels/add/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.href = location.href;    
                }               
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
        }
    }]);
</script>

<div ng-controller="channelsModalCtrl" class="modal fade" id="addChannelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Канал</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Название</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.name">
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
                                <textarea class="form-control" ng-model="form.description" style="height: 140px;"></textarea>
                            </div>
                            <div ng-show="errors.description" class="error">{{errors.description}}</div>
                        </div>
                    </div>
                    
<!--                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Номер</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.number">
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>-->

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>StreamID</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="streamid" value="" ng-model="form.streamid">
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Жанр</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.group_id">
                                    <?php foreach($this->groups as $item){ ?>
                                        <option value="<?=$item->id?>"><?=$item->name?></option>
                                    <?php } ?>
                                </select>
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Ссылка</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="url" value="" ng-model="form.url">
                                <div class="error link_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!--
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Архив</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="archive" value="" ng-model="form.archive">
                                <div class="error archive_error"></div>
                            </div>
                        </div>
                    </div>-->
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Логотип</label>
                            </div>
                            <div class="col-sm-4">
                                <!--<div class="uploadFileBtn">Загрузить
                                    <iframe id="hiddenIframeUpload" src="/channels/loadaddlogo/"></iframe>
                                </div>-->
                                <div class="uploadFileBtn">Загрузить
                                    <iframe id="hiddenIframeUpload" src="/channels/loadeditlogo/"></iframe>
                                </div>
                                <input type="hidden" name="logo_id" value="">                                
                            </div>
                            <div class="col-sm-4">                                
                                <img class="preview_logo" style="display:block; margin:auto;" src="<?=$this->config->assets_url?>/{{filename}}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>EPG</label>
                            </div>
                            
                            <div class="col-sm-8">
                                <input type="text" class="form-control" ng-model="form.epg">
                            </div>
                        </div>
                    </div>
                    
                   <!-- <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>EPG</label>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <select class="form-control" ng-model="settings" ng-change="getChannelsList()">
                                            <option value="0">Настройки</option>
                                            <?php foreach($this->settings as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <select class="form-control" ng-model="form.epg">
                                            <option value="0">Каналы</option>
                                            <option ng-repeat="item in channels_list" value="{{item.id}}">{{item.name}}</option>                                            
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="error logo_id_error"></div>
                            </div>
                        </div>
                    </div>-->
                    
                    <script>
                        function addImage(filename,id){
                            console.log(filename);
                            $('#addChannelModal input[name=logo]').val(id);
                            $('#addChannelModal .preview_logo').attr('src',filename);
                        }
                        function addError(error){

                        }
                    </script>
                    

                    <div>
                        <textarea class="pattern_block panel" style="padding:20px;display:none; width:100%; height:auto;"></textarea>
                    </div>
                    <input type="hidden" name="logo" value="">
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>