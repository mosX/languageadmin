<div id="top_menu">    
    <?= $this->module('header') ?>

    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Расписание Занятий</div>
            <div class="buttons_wrapper">
                <a href="" class="svg_pipe">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
            </div>
        </div>

        <div class="filter_overlay"></div>

        <form action='' class="search_wrapper">
            <script>
                $('document').ready(function () {
                    $('#search_input').click(function () {
                        $('#top_menu .filter_wrapper').css({'display': 'flex'});
                        $('.filter_overlay').css({'display': 'block'});
                    });

                    $('.filter_overlay').click(function () {
                        $('.filter_wrapper').css({'display': 'none'});
                        $('.filter_overlay').css({'display': 'none'});
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
                                <input type="text" placeholder="Тэги" name="tag" value="<?= $_GET['tag'] ?>" class="form-control">
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
                $(".datepicker").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    startDate: '01-01-1996',
                    firstDay: 1
                });
            </script>
            <div class='filter_inner'>
                <div class="input_block">
                    <input type="text" id="search_input" placeholder="Поиск и фильтр" name='search' value='<?= $_GET['search'] ?>'>
                </div>
                <label for="search_input" class='options_cnt'>2 опции</label>
                <label for="search_input" class='ico'>
                    <svg class="svg_search_icon"><use xlink:href="#common--filter-search"></use></svg>
                </label>
            </div>
        </form>

        <style>
            #top_menu .filter_inner{
                display:flex;
                align-items: center;
                width:100%;
            }
            #top_menu .filter_inner label.ico{
                width:20px;
                order:2;
                margin-right:5px;
            }
            #top_menu .options_cnt{
                display:none;
                color:#595d64;
                background: #dfebf8;
                border : 1px solid #9dc1e7;
                border-radius: 2px;
                padding:0px 5px;
                margin-right:15px;
                order: 1;
                cursor:pointer;
            }

            #top_menu .filter_inner .input_block{
                flex:1;
                order:3;            
            }
            #top_menu .filter_inner .svg_search_icon{
                left:0px;
                top:0px;
                vertical-align: middle;

                position:relative;
            }
        </style>

        <div class="actions">
            <a href="" data-toggle="modal" data-target="#addModal" class="button add_deal">+ ДОБАВИТЬ РАСПИСАНИЕ</a>
        </div>
    </div>
</div>

<script>
    app.controller('addModalCtrl', ['$scope','$rootScope', '$http', function ($scope,$rootScope, $http){
            $scope.form = {};
            $scope.student_selects = [true];
                    
            $scope.lessons_list = <?=$this->lessons ?json_encode($this->lessons):'{}'?>;
            $scope.students_list = <?=$this->students ?json_encode($this->students):'{}'?>;
            
            $scope.addStudentForm = function($event){
                $scope.student_selects.push(true);
            }
            
            $rootScope.$on('setDate',function(event,data){
                data.day = data.day < 10 ? '0'+data.day : data.day;
                data.month = data.month < 10 ? '0'+data.month : data.month;
                
                $scope.date = data.year + '-'+data.month+'-'+data.day;
                $('#addModal').modal('show');
            });
            
            $scope.submit = function (event){
                $scope.form.color = $('#color_picker').val();
                $scope.form.date = $('input[name=date]').val();
                console.log($scope.form);
                
                $http({
                    method: 'POST',
                    url: location.href,
                    data: $scope.form
                }).then(function (ret) {
                    console.log(ret.data);
                    if (ret.data.status == 'success') {
                        location.href = location.href;
                    } else {
                        console.log('ERROR');
                    }
                });

                event.preventDefault();
            }
            
            $scope.setEnd = function(){
                var parent = $('#addModal form');
                var arr = $('input[name=start]', parent).val().split(':');

                var hours = arr[0];
                var minutes = arr[1];

                var d = new Date();

                d.setHours(hours);
                d.setMinutes(minutes);

                var new_d = new Date(d.getTime() + 60 * 90 * 1000);

                var new_hours = new_d.getHours();
                var new_minutes = new_d.getMinutes();

                $scope.form.end = new_hours + ':' + new_minutes;
                $scope.$digest();
                //$('input[name=end]', parent).val(new_hours + ':' + new_minutes);
            }
            
            $('#addModal .clockpicker_start').clockpicker({
                placement: 'bottom',
                align: 'left',
                donetext: 'OK',
                autoclose: true,
                afterDone: function(){
                    $scope.setEnd(false);                    
                }
            });

            $('#addModal .clockpicker').clockpicker({
                placement: 'bottom',
                align: 'left',
                donetext: 'OK',
                autoclose: true
            });
        }]);
</script>


<style>
    #addModal .modal-dialog{
        width:700px;
    }
</style>

<div ng-controller="addModalCtrl" class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить в расписание</strong></p></h4>
            </div>

            <div class="modal-body">
                <form class="form" action="" method="POST" ng-submit="submit($event)">
                    
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Выбранная Дата</div>

                            <div class="col-sm-8">
                               <input type="text" name="date" class="datetimepicker form-control" value="" ng-model="date">
                                
                                <script>
                                    $('document').ready(function(){
                                        $(".datetimepicker").datetimepicker({
                                            format: "YYYY-MM-DD",
                                        });
                                    });
                                </script>
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Заметка</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="message" value="" ng-model="form.message">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Цвет заметки</div>

                            <div class="col-sm-8">
                                <input type="text"  class="form-control jscolor {valueElement:'color_picker',value:'ffffff'}" value="">
                                <input type="hidden" name="color" value="" id="color_picker">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>-->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Постояное расписание</div>

                            <div class="col-sm-8">
                                <ul class="list-inline">
                                    <li>
                                        ПН
                                         <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[1]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        ВТ
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[2]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        СР
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[3]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        ЧТ
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[4]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        ПТ
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[5]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        СБ
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[6]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                    <li>
                                        НД
                                        <label class='checkbox'>
                                            <input type="checkbox" class="action_panel_triger" ng-model="form.permanent[7]" ng-true-value="'true'" ng-false-value="''">
                                            <div class='box'></div>
                                        </label>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Предмет</div>

                            <div class="col-sm-8">
                                <select name="type" class="form-control" ng-model="form.type">
                                    <option value="0">Без типа</option>
                                    <option ng-repeat="item in lessons_list" value="{{item.id}}">{{item.name}}</option>
                                </select>
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
              
                    <style>
                        .student_block .element:first-child .remove_student{
                            display:none;
                        }
                        .student_block .form-group .remove_student{
                            cursor:pointer;
                            font-size:18px;
                            margin-top:5px;
                            color: red;
                        }
                    </style>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Студент</div>

                            <div class="col-sm-8">
                                <div class="student_block">
                                    <div class="form-group element" ng-repeat="item in student_selects track by $index">
                                        <div class="row">                                            
                                            <div class="col-sm-10">
                                                <select class="form-control" ng-model="form.students[$index]">
                                                    <option>Пусто</option>
                                                    <option ng-repeat="item in students_list" value="{{item.id}}">{{item.firstname}} {{item.lastname}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 text-center">
                                                <span class="glyphicon glyphicon-remove remove_student"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn btn-primary add_student" ng-click="addStudentForm($event)">Добавить</div>

                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Начало</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control clockpicker_start" name="start" value="" ng-model="form.start">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Окончание</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control clockpicker" name="end" value="" ng-model="form.end">
                                <div class="error"><?= $this->m->error->end ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" class="btn btn-primary" value="Сохранить">
                            </div>
                        </div>
                    </div>      
                </form>
                
            </div>
        </div>
    </div>
    
</div>



<script>
    app.controller('editModalCtrl', ['$scope','$rootScope', '$http', function ($scope,$rootScope, $http){
            $scope.form = {};
            $scope.student_selects = [true];
                    
            $scope.lessons_list = <?=$this->lessons ?json_encode($this->lessons):'{}'?>;
            $scope.students_list = <?=$this->students ?json_encode($this->students):'{}'?>;
            $scope.form.students = [];
            
            $scope.$on('editData', function (event, ret){
                console.log(ret.data); // Данные, которые нам прислали
                //
                //$scope.form  = ret.data;
                $scope.date = ret.data.date;
                $scope.form.type = ret.data.lesson;
                $scope.form.start = ret.data.start;
                $scope.form.end = ret.data.end;
                
                if(ret.data.students){
                    $scope.student_selects = [];
                    
                    for(var key in ret.data.students){
                        $scope.student_selects.push(ret.data.students[key].student_id);
                        console.log(ret.data.students[key].student_id);
                        $scope.form.students.push(ret.data.students[key].student_id);
                    }
                }
                
                console.log($scope.form);
                
            });
            
            $scope.addStudentForm = function($event){
                $scope.student_selects.push(true);
            }
                        
            $scope.submit = function (event){
                $scope.form.color = $('#color_picker').val();
                $scope.form.date = $('input[name=date]').val();
                console.log($scope.form);
                
                $http({
                    method: 'POST',
                    url: location.href,
                    data: $scope.form
                }).then(function (ret) {
                    console.log(ret.data);
                    if (ret.data.status == 'success') {
                        location.href = location.href;
                    } else {
                        console.log('ERROR');
                    }
                });

                event.preventDefault();
            }
            
            $scope.setEnd = function(){
                var parent = $('#editModal form');
                var arr = $('input[name=start]', parent).val().split(':');

                var hours = arr[0];
                var minutes = arr[1];

                var d = new Date();

                d.setHours(hours);
                d.setMinutes(minutes);

                var new_d = new Date(d.getTime() + 60 * 90 * 1000);

                var new_hours = new_d.getHours();
                var new_minutes = new_d.getMinutes();

                $scope.form.end = new_hours + ':' + new_minutes;
                $scope.$digest();
                //$('input[name=end]', parent).val(new_hours + ':' + new_minutes);
            }
            
            $('#editModal .clockpicker_start').clockpicker({
                placement: 'bottom',
                align: 'left',
                donetext: 'OK',
                autoclose: true,
                afterDone: function(){
                    $scope.setEnd(false);                    
                }
            });

            $('#editModal .clockpicker').clockpicker({
                placement: 'bottom',
                align: 'left',
                donetext: 'OK',
                autoclose: true
            });
        }]);
</script>


<style>
    #addModal .modal-dialog{
        width:700px;
    }
</style>

<div ng-controller="editModalCtrl" class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить в расписание</strong></p></h4>
            </div>

            <div class="modal-body">
                <form class="form" action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Дата</div>

                            <div class="col-sm-8">
                               <input type="text" name="date" class="datetimepicker form-control" value="" ng-model="date">
                                
                                <script>
                                    $('document').ready(function(){
                                        $(".datetimepicker").datetimepicker({
                                            format: "YYYY-MM-DD",
                                        });
                                    });
                                </script>
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Заметка</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="message" value="" ng-model="form.message">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Цвет заметки</div>

                            <div class="col-sm-8">
                                <input type="text"  class="form-control jscolor {valueElement:'color_picker',value:'ffffff'}" value="">
                                <input type="hidden" name="color" value="" id="color_picker">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>-->
                  
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Предмет</div>

                            <div class="col-sm-8">
                                <select name="type" class="form-control" ng-model="form.type">
                                    <option value="0">Без типа</option>
                                    <option ng-repeat="item in lessons_list" value="{{item.id}}">{{item.name}}</option>
                                </select>
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
              
                    <style>
                        .student_block .element:first-child .remove_student{
                            display:none;
                        }
                        .student_block .form-group .remove_student{
                            cursor:pointer;
                            font-size:18px;
                            margin-top:5px;
                            color: red;
                        }
                    </style>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Студент</div>

                            <div class="col-sm-8">
                                <div class="student_block">
                                    <div class="form-group element" ng-repeat="item in student_selects track by $index">
                                        <div class="row">                                            
                                            <div class="col-sm-10">
                                                <select class="form-control" ng-model="form.students[$index]">
                                                    <option>Пусто</option>
                                                    <option ng-repeat="item in students_list" value="{{item.id}}">{{item.firstname}} {{item.lastname}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2 text-center">
                                                <span class="glyphicon glyphicon-remove remove_student"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn btn-primary add_student" ng-click="addStudentForm($event)">Добавить</div>

                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Начало</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control clockpicker_start" name="start" value="" ng-model="form.start">
                                <div class="error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">Окончание</div>

                            <div class="col-sm-8">
                                <input type="text" class="form-control clockpicker" name="end" value="" ng-model="form.end">
                                <div class="error"><?= $this->m->error->end ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" class="btn btn-primary" value="Редактировать">
                            </div>
                        </div>
                    </div>      
                </form>
                
            </div>
        </div>
    </div>
    
</div>
