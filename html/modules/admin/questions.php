<script>
    app.controller('selectQuestionModalCtrl', ['$scope','$http',function($scope,$http){
                
        $scope.submit = function(event){
            $http({
                url:location.href,
                method:'POST',
                data:{'question':$scope.question_id}
            }).then(function(ret){               
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }
            });
            event.preventDefault();
        }        
    }]);
</script>
<div ng-controller="selectQuestionModalCtrl" class="modal fade" id="selectQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Вопрос</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Вопрос</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="question_id">
                                            <?php foreach($this->list as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->value?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="error name_error"></div>
                                    </div>
                                    <div ng-show="errors.name" class="error">{{errors.name}}</div>
                                </div>
                            </div>
                           
                     
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                    
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    app.controller('imageQuestionModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {answers:[],score:1,lesson_id:<?=(int)$this->lesson_id?>};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('#addImageQuestionModal .answers_block .answer_item').each(function(){
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
    #addImageQuestionModal .modal-dialog{
        width:900px;
    }
</style>
<div ng-controller="imageQuestionModalCtrl" class="modal fade" id="addImageQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Вопрос Картинкой</strong></p></h4>
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
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="btn btn-primary" style="width:100%" ng-click="createAnswer($event)">Создать</div>
                                    </div>
                                </div>
                            </div>
                            <style>
                                #addImageQuestionModal .preview{
                                    width:100px;
                                    height:100px;
                                    background: grey;
                                }
                            </style>
                            <div class="answers_block">
                                <div class="form-group" ng-repeat="item in answers_list">
                                    <div class="answer_item" data-act="insert" data-index='{{$index}}' ng-if="item.act == 'insert'">
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <div class="uploadFileBtn">Загрузить
                                                    <iframe id="hiddenIframeUpload" src="{{'/lessons/loadaddimage/?index='+$index}}"></iframe>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class='preview'>
                                                    <img src=''>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function editImage(filename,id,index){
                                        var parent = $('#addImageQuestionModal .answers_block .answer_item[data-index='+index+']');                                        
                                        $('.preview',parent).css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                                    }
                                    function editError(error){
                                        console.log('editError');
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                    
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    app.controller('questionModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {answers:[],score:1,lesson_id:<?=(int)$this->lesson_id?>};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('#addQuestionModal .answers_block .answer_item').each(function(){
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
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="btn btn-primary" style="width:100%" ng-click="createAnswer($event)">Создать</div>
                                    </div>
                                    <!--<div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="selectAnswer($event)">Выбрать</div>
                                    </div>-->
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
            $('#editQuestionModal .answers_block .answer_item').each(function(){
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
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="btn btn-primary" style="width:100%" ng-click="createAnswer($event)">Создать</div>
                                    </div>
                                    <!--<div class="col-sm-6">
                                        <div class="btn btn-primary" style="width:100%" ng-click="selectAnswer($event)">Выбрать</div>
                                    </div>-->
                                </div>
                            </div>
                            
                            <div class="answers_block">
                                <div class="form-group" ng-repeat="item in answer_edit">
                                    <div class="answer_item" data-act="update">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;">{{item.text}}</textarea>
                                                <input type='hidden' class='id' value='{{item.id}}'>
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