<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>НАСТРОЙКИ EPG</div>        
        </div>

        <div class="actions">
            <a data-toggle="modal" data-target="#addEPGModal"   class="button add_deal">+ НОВЫЙ EPG</a>
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
                    <input value="Применить" ng-click="test()" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
                
                
            </div>
        </div>
    </div>
</div>
