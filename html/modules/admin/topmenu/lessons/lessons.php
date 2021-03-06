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

        $scope.$on('editData', function (event, ret){
            
            $scope.terms_list = [];
            $scope.form  = ret.data;
            if($scope.form.terms){
                $scope.terms_list = $scope.form.terms;
            }           
        });
        
        $scope.addTerm = function(){
            $scope.terms_list.push({'act':'insert'});
        };
        
        $scope.submit = function(event){
            //console.log($scope.form);
            $scope.form.terms = [];
            
            $('#editLessonModal .terms_block .item').each(function(){                
                $scope.form.terms.push({'from':$('input[name=from]',this).val(),'to':$('input[name=to]',this).val(),'text':$('textarea[name=text]',this).val()});
            });
            
            $scope.form.show_answers = $('#editLessonModal input[name=show_answers]')[0].checked;
            $scope.form.poster_id = $('#editLessonModal input[name=poster_id]').val();
            
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
                                            <input type="checkbox" class="action_panel_triger" name='show_answers' ng-checked="form.show_answers == true" ng-model="form.show_answers">
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
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Язык</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.language">
                                            <option value="eng">Английский</option>
                                            <option value="pl">Польский</option>
                                        </select>
                                    </div>                                    
                                </div>
                            </div>
                             <style>
                                #editLessonModal .preview{
                                    width:200px;
                                    height:100px;
                                    background: grey;
                                }
                            </style>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Постер</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="uploadFileBtn">Загрузить
                                            <iframe id="hiddenIframeUpload" src="{{'/lessons/loadeditimage/'}}"></iframe>
                                        </div>
                                        <input type="hidden" name="poster_id" value="{{form.poster_id?form.poster_id:'0'}}">
                                        
                                        <div class='preview' ng-if="!form.filename"></div>
                                        <div ng-if="form.filename" ng-cloak class='preview' style="background:url(/assets/posters/{{form.filename}}) no-repeat center center; background-size:cover"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            function editImage(filename,id){
                                $('#editLessonModal .preview').css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                                $('#editLessonModal input[name=poster_id]').val(id);
                            }
                            function editError(error){
                                console.log('editError');
                            }
                        </script>
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
            $scope.form.poster_id = $('#addLessonModal input[name=poster_id]').val();    
            
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
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Язык</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" ng-model="form.language">
                                    <option value="eng">Английский</option>
                                    <option value="pl">Польский</option>
                                </select>
                            </div>                                    
                        </div>
                    </div>
                    <style>
                        #addLessonModal .preview{
                            width:200px;
                            height:100px;
                            background: grey;
                        }
                    </style>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Постер</label>
                            </div>
                            <div class="col-sm-8">
                                <div class="uploadFileBtn">Загрузить
                                    <iframe id="hiddenIframeUpload" src="{{'/lessons/loadaddimage/'}}"></iframe>
                                </div>
                                <input type="hidden" name="poster_id" value="0">

                                <div class='preview'></div>
                                <!--<div ng-if="item.filename" ng-cloak class='preview' style="background:url(/assets/images/{{item.filename}}) no-repeat center center; background-size:cover"></div>-->
                            </div>
                        </div>
                    </div>
                    <script>
                        function addImage(filename,id){
                            $('#addLessonModal .preview').css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                            $('#addLessonModal input[name=poster_id]').val(id);
                        }
                        function addError(error){
                            console.log('editError');
                        }
                    </script>

                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    app.controller('deleteModalCtrl', ['$scope', '$http', function ($scope, $http) {
        $scope.id = '';

        $scope.$on('delete', function (event, id){
            console.log(id); // Данные, которые нам прислали
            $scope.id = id;
            $('#deleteModal').modal('show');
        });
        
        $scope.submit = function(event){
            
            $http({
                url:'/lessons/delete_lesson/?id='+$scope.id,
                method:'GET'
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    
                }
            });
            event.preventDefault();
        }
    }]);
</script>
<div class="modal fade" ng-controller="deleteModalCtrl" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить Урок</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action='' method='POST' ng-submit="submit($event)">
                <div class="modal-body">
                    <p>Вы действительно хотите удалить предмет?</p>

                    <p>Все данные, как-либо связанные с ним, будут удалены. Восстановить удалённые данные будет невозможно.</p>
                </div>
                <div class="modal-footer">
                    <input type='hidden' name='id' value=''>
                    <input type="submit" class="btn btn-secondary" value='Подтвердить'>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>