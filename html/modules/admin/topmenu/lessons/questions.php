<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Уроки/Вопросы</div>
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
            <a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>            
            <a data-toggle="modal" data-target="#addQuestionModal" class="button add_deal">+ НОВЫЙ ВОПРОС</a>
        </div>
    </div>
</div>

<script>
    app.controller('questionEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.form = {};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов

        $scope.$on('editData', function (event, ret){
            console.log(ret.data); // Данные, которые нам прислали
            
            $scope.form  = ret.data;
            $scope.answer_edit = ret.data.answers;
        });
        
        $scope.createAnswer = function(event){
            console.log('createAnswer');
            $scope.answers_list.push({act:'insert'});
            
            event.preventDefault();
        }
        
        $scope.selectAnswer = function(event){
            console.log('selectAnswer');
            $scope.answers_list.push({act:'select'});
            
            event.preventDefault();
        }
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('.answers_block .answer_item').each(function(){
                switch($(this).attr('data-act')){
                    case 'update':
                        $scope.form.answers.push({'act':'update','correct':$('input[type=radio]',this)[0].checked,'id':$('.id',this).val(),value:$('textarea',this).val()});
                        break;
                    case 'insert':
                        $scope.form.answers.push({'act':'insert','correct':$('input[type=radio]',this)[0].checked,value:$('textarea',this).val()});
                        break;
                    case 'select':
                        $scope.form.answers.push({'act':'select','correct':$('input[type=radio]',this)[0].checked,value:$('select option:selected',this).val()});
                        break;
                }
            });
            
            console.log($scope.form);
            
            $http({
                method:'POST',
                url:'/lessons/questions/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
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
<style>
    #editQuestionModal .modal-dialog{
        width:900px;
    }
</style>
<div ng-controller="questionEditModalCtrl" class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Вопрос</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Вопрос</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" ng-model="form.value" style="height: 80px;"></textarea>
                                        <div class="error name_error"></div>
                                    </div>
                                    <div ng-show="errors.name" class="error">{{errors.name}}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Балы</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.score">
                                        <div class="error name_error"></div>
                                    </div>
                                    <div ng-show="errors.name" class="error">{{errors.name}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            Добавление ответов
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="createAnswer($event)">Создать</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="selectAnswer($event)">Выбрать</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="answers_block">
                                <div class="form-group" ng-repeat="item in answer_edit">
                                    <div class="answer_item" data-act="update">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;">{{item.text}}</textarea>
                                                <input type='hidden' class='id' value='{{item.answer_id}}'>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                   
                                <div class="form-group" ng-repeat="item in answers_list">
                                    <div class="answer_item" data-act="insert" ng-if="item.act == 'insert'">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="answer_item" data-act="select" ng-if="item.act == 'select'">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <select class="form-control">
                                                    <option ng-repeat="answer in answers" value="{{answer.id}}">{{answer.text}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
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
    app.controller('questionModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {answers:[]};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('.answers_block .answer_item').each(function(){
                if($('textarea',this).length > 0){
                    $scope.form.answers.push({'act':'insert','correct':$('input[type=radio]',this)[0].checked,value:$('textarea',this).val()});                    
                }else if($('select',this).length > 0){
                    $scope.form.answers.push({'act':'select','correct':$('input[type=radio]',this)[0].checked,value:$('select option:selected',this).val()});
                }
            });
            
            $http({
                method:'POST',
                url:'/lessons/questions/',
                data:$scope.form
            }).then(function(ret){
               console.log(ret.data);
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    console.log('ERROR');
                }
            });
            
            event.preventDefault();
        }
        
        $scope.createAnswer = function(event){
            console.log('createAnswer');
            $scope.answers_list.push({act:'insert'});
            
            event.preventDefault();
        }
        
        $scope.selectAnswer = function(event){
            console.log('selectAnswer');
            $scope.answers_list.push({act:'select'});
            
            event.preventDefault();
        }
    }]);
</script>
<style>
    #addQuestionModal .modal-dialog{
        width:900px;
    }
</style>
<div ng-controller="questionModalCtrl" class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Вопрос</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Вопрос</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" ng-model="form.value" style="height: 80px;"></textarea>
                                        <div class="error name_error"></div>
                                    </div>
                                    <div ng-show="errors.name" class="error">{{errors.name}}</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Балы</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.score">
                                        <div class="error name_error"></div>
                                    </div>
                                    <div ng-show="errors.name" class="error">{{errors.name}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            Добавление ответов
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="createAnswer($event)">Создать</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="selectAnswer($event)">Выбрать</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="answers_block">
                                <div class="form-group" ng-repeat="item in answers_list">
                                    <div class="answer_item" data-act="insert" ng-if="item.act == 'insert'">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="answer_item" data-act="select" ng-if="item.act == 'select'">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <select class="form-control">
                                                    <option ng-repeat="answer in answers" value="{{answer.id}}">{{answer.text}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>