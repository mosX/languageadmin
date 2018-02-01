<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>АРХИВЫ</div>
            <div class="buttons_wrapper">
                <a href="" class="svg_pipe">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
                <a href="" class="svg_list active">
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
                                <input type="text" class="datepicker form-control" value="date">
                            </div>

                            <div class="form-group">
                                <input type="text" placeholder="Тэги" name="tag" value="<?=$_GET['tag']?>" class="form-control">
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

        <div class="actions">
            <a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>
            <a data-toggle="modal" data-target="#addArchiveModal" class="button add_deal">+ НОВЫЙ АРХИВ</a>
        </div>
    </div>
</div>

<script>
    app.controller('archiveModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        $scope.form.epg = '0';
        $scope.settings = "0";
        $scope.channels_list = [];
        
        console.log('Archive Controller');
        
        //$('#addChannelModal').modal('show');
        
        $scope.submit = function(event){            
            console.log($scope.form);
            $http({
                method:'POST',
                url:'/archive/add/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
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

<div ng-controller="archiveModalCtrl" class="modal fade" id="addArchiveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Архив</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                     <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Канал</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.channel_id">
                                    <option value="0">Канал</option>
                                    <?php foreach($this->list as $item){ ?>
                                        <option value="<?=$item->id?>"><?=$item->name?></option>
                                    <?php } ?>
                                </select>
                                <div class="error name_error"></div>
                            </div>
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Начало</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control datepicker" name="start" value="" ng-model="form.start">
                                <div class="error number_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Конец</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control datepicker" name="stop" value="" ng-model="form.stop">
                                <div class="error link_error"></div>
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