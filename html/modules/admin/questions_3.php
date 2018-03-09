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

<style>
    #editImageQuestionModal .modal-dialog{
        width:900px;
    }
</style>
<script>
    app.controller('imageQuestionEditModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.form = {};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.selectedItem = {};   //активный объект в случае если мы выбираем картинки из существующих
        
        $rootScope.$on('selectImage',function(event,ret){
            console.log("IMAGE ",ret);
        });

        $scope.$on('editImageData', function (event, ret){
            console.log(ret.data); // Данные, которые нам прислали
            
            $scope.form  = ret.data;
            $scope.answer_edit = ret.data.answers;
            for(var key in $scope.answer_edit){
                $scope.answer_edit[key].act = 'update';
            }
            console.log($scope.answer_edit);
        });
        
        $scope.createAnswer = function(event){
            console.log('createAnswer');
            //$scope.answers_list.push({act:'insert'});
            
            $scope.answer_edit.push({'act':'insert','images_id':'0','id':'0'});
            
            event.preventDefault();
        }
        
        $scope.selectAnswer = function(event){
            console.log('selectAnswer');
            $scope.answers_list.push({act:'select'});
            
            event.preventDefault();
        }
        
        $scope.selectImage = function(event,item){
            $scope.selectedItem = item;
            
            console.log('!!!!!!!!',item);
            $rootScope.$emit('showSelectImagePanel',item);
        }
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('#editImageQuestionModal .answers_block .answer_item').each(function(){
                var act = $(this).attr('data-act');
                $scope.form.answers.push({'act':act,'correct':$('input[type=radio]',this)[0].checked,'id':$('.id',this).val(),value:$('input[name=image_id]',this).val()});
            });
            
            console.log($scope.form);
            
            $http({
                method:'POST',
                url:'/lessons/add_image_question/',
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

<div ng-controller="imageQuestionEditModalCtrl" class="modal fade" id="editImageQuestionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Вопрос Картинкой</strong></p></h4>
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
                                #editImageQuestionModal .preview{
                                    width:100px;
                                    height:100px;
                                    background: grey;
                                }
                                #editImageQuestionModal .answer_item[data-act="delete"]{
                                    display:none;
                                }
                            </style>
                            <div class="answers_block">
                                <div class="form-group" ng-repeat="item in answer_edit">
                                    <div class="answer_item" data-act="{{item.act}}" data-index='{{$index}}'>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="uploadFileBtn">Загрузить
                                                    <iframe id="hiddenIframeUpload" src="{{'/lessons/loadeditimage/?index='+$index}}"></iframe>
                                                </div>
                                                
                                                <div ng-click="selectImage($event,item)" class="btn btn-primary">Выбрать</div>
                                                <input type="hidden" name="image_id" value="{{item.image_id}}">
                                                <input type="hidden" class="id" name="id" value="{{item.id}}">                                                
                                            </div>
                                            <div class="col-sm-4">
                                                
                                                <div class='preview' style="background:url(/assets/images/{{item.filename}}) no-repeat center center; background-size:cover">
                                                    
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
                                            <div class="col-sm-2">
                                                <div ng-click="item.act='delete'" class="glyphicon glyphicon-remove delete_element"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    function editImage(filename,id,index){
                                        var parent = $('#editImageQuestionModal .answers_block .answer_item[data-index='+index+']');                                        
                                        $('.preview',parent).css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                                        $('input[name=image_id]',parent).val(id);
                                    }
                                    function editError(error){
                                        console.log('editError');
                                    }
                                </script>
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

<style>
    #select_image_overflow{
        display:none;
        cursor:pointer;
        z-index:1051;
        position:fixed;
        background: transparent;
        top:0px;
        left:0px;
        right:0px;
        bottom:0px;
    }
    #select_image_panel{
        position:fixed;
        z-index:1052;
        width:500px;
        top:0px;
        bottom:0px;
        right:-500px;
        background: white;
        border-left: 2px solid black;
        overflow:auto;
        padding:0px 25px
    }
    #select_image_panel .item{
        margin:auto;
        margin-bottom:20px;
        cursor:pointer;
    }
    #select_image_panel img{
        max-width: 100%;
    }
</style>
<script>
    app.controller('selectImagePanelCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.activeObject = {};
        
        $scope.showPanel = function(){
            if($scope.images == undefined){
                $http({
                    url:'/lessons/get_images_list/',
                    type:'GET'
                }).then(function(ret){
                    console.log(ret.data);
                    $scope.images = ret.data;
                });
            }
            
            $('#select_image_panel').animate({'right':'0px'},600,function(){
               $('#select_image_overflow').css({'display':'block'});
            });            
        };        
        //$scope.showPanel();
        
        $rootScope.$on('showSelectImagePanel',function(event,ret){
            console.log("showSelectImagePanel",ret);
            $scope.activeObject = ret;
            
            $scope.showPanel();
        });
        
        $scope.hidePanel = function(event){
            $('#select_image_panel').animate({'right':'-500px'},600,function(){
               $('#select_image_overflow').css({'display':'none'});
            });
            if(event) event.preventDefault();
        }
        
        $scope.selectImage = function(item){
            $rootScope.$emit('selectImage',item);
            $scope.activeObject.filename = item.filename;
            $scope.activeObject.image_id = item.id;
            $scope.hidePanel();
        }
    }]);
</script>
<div  ng-controller="selectImagePanelCtrl">
    
    <div id="select_image_panel" >
        <div class="select_container">
            <div class="row">
                <div class="col-sm-4" ng-repeat="item in images">
                    <div class="item" ng-click="selectImage(item)">
                        <img src="{{'<?=$this->config->assets_url?>/images/'+item.filename}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="select_image_overflow" ng-click="hidePanel($event)"></div>
</div>

<script>
    app.controller('imageQuestionModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {answers:[],score:1,lesson_id:<?=(int)$this->lesson_id?>};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            $('#addImageQuestionModal .answers_block .answer_item').each(function(){
                $scope.form.answers.push({'act':'insert','correct':$('input[type=radio]',this)[0].checked,value:$('input[name=image_id]',this).val()});                
            });
            
            $http({
                method:'POST',
                url:'/lessons/add_image_question/',
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
                                                    <input type="hidden" name="image_id" value="0">
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class='preview'>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function addImage(filename,id,index){
                                        var parent = $('#addImageQuestionModal .answers_block .answer_item[data-index='+index+']');                                        
                                        $('.preview',parent).css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                                        $('input[name=image_id]',parent).val(id);
                                    }
                                    function addError(error){
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
            
            for(var key in $scope.answer_edit){
                $scope.answer_edit[key].act = 'update';
            }
        });
        
        $scope.createAnswer = function(event){
            console.log('createAnswer');
            //$scope.answers_list.push({act:'insert'});
            
            $scope.answer_edit.push({'act':'insert'});
            
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
                var act = $(this).attr('data-act');
                $scope.form.answers.push({'act':act,'correct':$('input[type=radio]',this)[0].checked,'id':$('.id',this).val(),value:$('textarea',this).val()});
            });
            
            $http({
                method:'POST',
                url:'/lessons/questions/',
                data:$scope.form
            }).then(function(ret){
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
    #editQuestionModal .answer_item[data-act="delete"]{
        display:none;
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
                                    <div class="answer_item" data-act="{{item.act}}">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <textarea class="form-control" style="height: 40px;">{{item.text}}</textarea>
                                                <input type='hidden' class='id' value='{{item.id}}'>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
                                            
                                            <div class="col-sm-2">
                                                <div ng-click="item.act='delete'" class="glyphicon glyphicon-remove delete_element"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                   
                                <!--<div class="form-group" ng-repeat="item in answers_list">
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
                                </div>-->
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