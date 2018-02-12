<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Уроки</div>
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
            <a data-toggle="modal" data-target="#addLessonModal" class="button add_deal">+ НОВЫЙ УРОК</a>
        </div>
    </div>
</div>

<script>
    app.controller('lessonEditModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {};
        $scope.terms_list = [];
        //$this->m->data->terms

        $scope.$on('editData', function (event, ret){
            console.log(ret.data); // Данные, которые нам прислали
            
            $scope.form  = ret.data;
            $scope.terms_list = $scope.form.terms;
        });
        
        $scope.addTerm = function(){
            $scope.terms_list.push({'act':'insert'});
        }
        
        $scope.submit = function(event){
            console.log($scope.form);
            $scope.form.terms = [];
            
            $('#editLessonModal .terms_block .item').each(function(){                
                $scope.form.terms.push({'from':$('input[name=from]',this).val(),'to':$('input[name=to]',this).val(),'text':$('textarea[name=text]',this).val()});
            });
            
            $http({
                method:'POST',
                url:'/lessons/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.href = location.href;    
                }else{
                    $scope.errors = ret.data.message;    
                }
                
            });
            
            event.preventDefault();
        }
    }]);
</script>

<style>
    #editLessonModal .modal-dialog{
        width:800px;
    }
</style>

<div ng-controller="lessonEditModalCtrl" class="modal fade" id="editLessonModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Урока</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                        <div class="col-sm-6">
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
                                        <label>Выводить ответы</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-checked="form.show_answers == 1" ng-model="form.show_answers">
                                            <div class='box'></div>
                                        </label>
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
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="btn btn-primary" ng-click="addTerm($event)">Добавить Условие</div>                                    
                            </div>
                            <div class="terms_block">
                                <div class="form-group item" data-act="item.act" ng-repeat="item in terms_list">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" placeholder="С" name="from" value="{{item.from}}">
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" placeholder="По" name="to" value="{{item.to}}">
                                        </div>
                                        <div class="col-sm-12" style="margin-top:5px;">
                                            <textarea name="text" class="form-control">{{item.text}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                    <input type="hidden" name="id" value="" ng-model="form.id">                                        
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('lessonsModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        
        $scope.submit = function(event){
            $http({
                method:'POST',
                url:'/lessons/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.href = location.href;    
                }               
            });
            
            event.preventDefault();
        }
    }]);
</script>

<div ng-controller="lessonsModalCtrl" class="modal fade" id="addLessonModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Урок</strong></p></h4>
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

                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>