<script>
    app.controller('filterCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        $scope.form.settings = "<?=(int)$_GET['settings']?>";
        $scope.form.ch_id = "<?=(int)$_GET['ch_id']?>";
        
        $scope.epg_channels = {};
        
        $scope.getChannels = function(){
            $http({                
                url:'/epg/getepgchannelslist/?setting_id='+$scope.form.settings,                
            }).then(function(ret){
               console.log(ret.data);
               $scope.epg_channels = ret.data;               
               $scope.form.ch_id = "<?=(int)$_GET['ch_id']?>";
            });
        }
        
        if($scope.form.settings){   //если сеттинг есть то получаем каналы
            console.log('get CHANNELS');
            $scope.getChannels();
        }
        
        $scope.selectSettings = function(){
            console.log('selectSettings');
            $scope.getChannels();
        }
    }]);
</script>
<div id="top_menu" ng-controller="filterCtrl">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>ВИЗУАЛИЗАЦИЯ EPG</div>        
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
                        <div class="col-sm-5">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Дата</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="date_from" class="datepicker form-control" value="<?=$this->filter->date_from?>">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" name="date_to" class="datepicker form-control" value="<?=$this->filter->date_to?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Каналы</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="channel">
                                            <option></option>
                                            <?php foreach($this->channels as $item){ ?>
                                                <option <?=$_GET['channel'] == $item->id ? 'selected="selected"':''?> value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Settings</label>
                                    </div>
                                    <div class="col-sm-{{epg_channels.length ? 4 : 8}}">
                                        <select class="form-control" name="settings" ng-model="form.settings" ng-change="selectSettings()">
                                            <option value="0"></option>
                                            <?php foreach($this->settings as $item){ ?>
                                                <option <?=$_GET['setting'] == $item->id ? 'selected="selected"':''?> value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div ng-if="epg_channels.length" class="col-sm-4">
                                        <select class="form-control" name="ch_id" ng-model="form.ch_id">
                                            <option value="0"></option>
                                            <option ng-repeat="item in epg_channels" value="{{item.ch_id}}">{{item.name}}</option>
                                        </select>
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
                  $( ".datepicker" ).datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        startDate:'01-01-1996',
                        firstDay: 1
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
    </div>
</div>

<script>
    app.controller('EPGModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        console.log('START');
        $scope.submit = function(event){
            $http({
                method:'POST',
                url:'/epg/addsetting/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
            });
            
            event.preventDefault();
        }
    }]);
</script>

<div ng-controller="EPGModalCtrl" class="modal fade" id="addEPGModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий EPG</strong></p></h4>
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
                                <div class="error firstname_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Ссылка</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="link" value="" ng-model="form.link">
                                <div class="error lastname_error"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <textarea class="pattern_block panel" style="padding:20px;display:none; width:100%; height:auto;"></textarea>
                    </div>
                    <input value="Применить" ng-click="test()" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('EPGEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        console.log('START');
        $scope.submit = function(event){
            $http({
                method:'POST',
                url:'/epg/editsetting/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
            });
            
            event.preventDefault();
        }
    }]);
</script>

<div ng-controller="EPGEditModalCtrl" class="modal fade" id="addEPGModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать EPG</strong></p></h4>
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
                                <div class="error firstname_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Ссылка</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="link" value="" ng-model="form.link">
                                <div class="error lastname_error"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <textarea class="pattern_block panel" style="padding:20px;display:none; width:100%; height:auto;"></textarea>
                    </div>
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
                
                
            </div>
        </div>
    </div>
</div>
