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
        width:100px;
        height: 100px;
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
    app.controller('questionModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.form = {answers:[],score:1,lesson_id:<?=(int)$this->lesson_id?>,mode:'1'};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        $scope.answers_list[1] = [];
        
        $scope.mode = 'text';   //audio
        
        $scope.selectedItem = {};   //активный объект в случае если мы выбираем картинки из существующих
        
        $scope.submit = function(event){
            $scope.form.answers = [];            
            
            switch(parseInt($scope.form.mode)){
                case 1:case 5:case 6:case 3 :
                    $scope.form.audio_id = $('#addQuestionModal input[name=audio_id]').val();
                    $('#addQuestionModal .answers_block .answer_item').each(function(){
                        
                        $scope.form.answers.push({act:'insert',correct:$('input[type=radio]',this)[0].checked,value:$('textarea',this).val()});
                    });
                    break;
                case 4:
                    $scope.form.audio_id = $('#addQuestionModal input[name=audio_id]').val();
                    $('#addQuestionModal .answers_block .answer_item').each(function(){
                        
                        $scope.form.answers.push({act:'insert',value:$('textarea',this).val()});
                    });
                    break;
                case 2:
                    $('#addQuestionModal .answers_block .answer_item').each(function(){
                        $scope.form.answers.push({'act':'insert','correct':$('input[type=radio]',this)[0].checked,value:$('input[name=image_id]',this).val()});                
                    });
                    break;
                case 7:
                    $scope.form.image_id = $('#addQuestionModal input[name=question_image_id]').val();
                    $('#addQuestionModal .answers_block .answer_item').each(function(){
                        $scope.form.answers.push({act:'insert',correct:$('input[type=radio]',this)[0].checked,value:$('textarea',this).val()});
                    });
                    break;
            }
            /*console.log($scope.form);
            
            event.preventDefault();
            return false;*/
            $http({
                method:'POST',
                //url:'/lessons/add_image_question/',
                url:'/questions/add/',
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
            $scope.answers_list[$scope.form.mode].push({act:'insert'});
            
            event.preventDefault();
        }
        
        $scope.selectImage = function(event,item){
            $scope.selectedItem = item;
            
            console.log('!!!!!!!!',item);
            $rootScope.$emit('showSelectImagePanel',item);
        }
        
        /*$scope.selectAnswer = function(event){
            console.log('selectAnswer');
            $scope.answers_list.push({act:'select'});
            
            event.preventDefault();
        }*/
            
        $scope.listen = function(event){
            var url = $(event.target).attr('data-src');
            var audio = new Audio(url);
            audio.play();
        }
        
        $scope.changeMode = function(){
            console.log($scope.form.mode);
            if($scope.answers_list[$scope.form.mode] == undefined){
               $scope.answers_list[$scope.form.mode] = []; 
            }
            
            if($scope.form.mode == 5 || $scope.form.mode == 6){
                $scope.mode = 'audio';
            }else if($scope.form.mode == 7){
                $scope.mode = 'image';
            }else{
                $scope.mode = 'text';
            }            
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
                <h4 class="modal-title font-header"><p><strong>Добавить Новий Вопрос Картинкой</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Тип Вопроса</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.mode" ng-change="changeMode()">
                                            <option value="1">Выбор Ответа</option>
                                            <option value="2">Выбор Изображения</option>
                                            <option value="3">Пропущенное слово</option>
                                            <option value="4">Написать перевод</option>
                                            <option value="5">Прослушать и выбрать</option>
                                            <option value="6">Прослушать и написать</option>
                                            
                                            <option value="7">Изображение/Ответы</option>
                                        </select>
                                    </div>                                    
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if='mode=="audio"'>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Аудио</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="uploadFileBtn">Загрузить
                                            <iframe id="hiddenIframeUpload" src="{{'/questions/loadaddaudio/'}}"></iframe>
                                            <input type="hidden" name="audio_id" value="">
                                        </div>
                                        <div style="display:none" ng-click="listen($event)" data-src="" class="btn btn-primary listen_btn">Слушать</div>
                                        <script>
                                            function addAudio(filename,id,index){
                                                console.log('!!',filename,id);
                                                $('#addQuestionModal input[name=audio_id]').val(id);
                                                $('#addQuestionModal .listen_btn').attr('data-src',filename).css({'display':'inline-block'});
                                            }
                                            function addAudioError(error){
                                                console.log('editError');
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if='mode == "audio"'>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Описание</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="audio_description" ng-model="form.audio_description">                                        
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if='mode == "text"'>
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
                            
                            <div class="form-group" ng-if="mode == 'image'">
                                <div class="answer_item">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="uploadFileBtn">Загрузить
                                                <iframe id="hiddenIframeUpload" src="{{'/questions/loadaddquestion/'}}"></iframe>
                                            </div>
                                            <input type="hidden" name="question_image_id" value="0">
                                        </div>
                                        <div class="col-sm-6">
                                            <div class='preview_question' style="width:100px; height:100px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                                function addQuestionImage(filename,id){
                                    console.log(filename,id);
                                    $('.preview_question','#addQuestionModal').css({'background':'url("'+filename+'") no-repeat center center','background-size':'cover'});
                                    $('input[name=question_image_id]','#addQuestionModal').val(id);
                                }
                                function addQuestionError(error){
                                    console.log('editError');
                                }
                            </script>
                            
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
                            <div class="answers_block" ng-if="form.mode == '1'">
                                <div class="form-group" ng-repeat="item in answers_list[1]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="answers_block" ng-if="form.mode == '7'">
                                <div class="form-group" ng-repeat="item in answers_list[7]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="answers_block" ng-if="form.mode == '5'">
                                <div class="form-group" ng-repeat="item in answers_list[5]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="answers_block" ng-if="form.mode == '6'">
                                <div class="form-group" ng-repeat="item in answers_list[6]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <!--<input type="radio" name="answer">-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="answers_block" ng-if="form.mode == '3'">
                                <div class="form-group" ng-repeat="item in answers_list[3]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="answers_block" ng-if="form.mode == '4'">
                                <div class="form-group" ng-repeat="item in answers_list[4]">
                                    <div class="answer_item">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <textarea class="form-control" style="height: 40px;"></textarea>
                                            </div>
                                            <div class="col-sm-2">
                                                <!--<input type="radio" name="answer">-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="answers_block" ng-if="form.mode == '2'">
                                <style>
                                    #addQuestionModal .preview{
                                        width:100px;
                                        height:100px;
                                        background: grey;
                                    }
                                </style>
                                <div class="form-group" ng-repeat="item in answers_list[2]">
                                    <div class="answer_item" data-index='{{$index}}'>
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <div class="uploadFileBtn">Загрузить
                                                    <iframe id="hiddenIframeUpload" src="{{'/questions/loadaddimage/?index='+$index}}"></iframe>                                                    
                                                </div>
                                                <div ng-click="selectImage($event,item)" class="btn btn-primary">Выбрать</div>
                                                <input type="hidden" name="image_id" value="0">                                                
                                            </div>
                                            <div class="col-sm-5">
                                                <div class='preview' ng-if="!item.filename"></div>
                                                <div ng-if="item.filename" ng-cloak class='preview' style="background:url(/assets/images/{{item.filename}}) no-repeat center center; background-size:cover"></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function addImage(filename,id,index){
                                        var parent = $('#addQuestionModal .answers_block .answer_item[data-index='+index+']');                                        
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
    app.controller('questionEditModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.form = {};
        $scope.answers = <?=$this->answers ? json_encode($this->answers): '[]'?>;   //для селекта
        $scope.answers_list = [];   //для создания новых елементов
        
        $scope.mode = 'text';

        $scope.$on('editData', function (event, ret){
            console.log(ret.data); // Данные, которые нам прислали
            
            $scope.mode = ret.data.type;
            $scope.form  = ret.data;
            $scope.answer_edit = ret.data.answers;
            console.log($scope.answer_edit);
            
            if($scope.form.type == 5 || $scope.form.type == 6){
                $scope.type = 'audio';
            }else{
                $scope.type = 'text';
            }
            
            for(var key in $scope.answer_edit){
                $scope.answer_edit[key].act = 'update';
            }
            
            $('#editQuestionModal').modal('show');
        });
        
        $scope.createAnswer = function(event){
            console.log('createAnswer');
            //$scope.answers_list.push({act:'insert'});
            
            $scope.answer_edit.push({'act':'insert'});
            
            event.preventDefault();
        }
        
        $scope.selectImage = function(event,item){
            $scope.selectedItem = item;
            
            console.log('!!!!!!!!',item);
            $rootScope.$emit('showSelectImagePanel',item);
        }
        
        /*$scope.selectAnswer = function(event){
            console.log('selectAnswer');
            $scope.answers_list.push({act:'select'});
            
            event.preventDefault();
        }*/
    
        $scope.listen = function(event){
            var url = $(event.target).attr('data-src');
            var audio = new Audio(url);
            audio.play();
        }
        
        $scope.submit = function(event){
            $scope.form.answers = [];
            
            switch(parseInt($scope.mode)){
                case 1:case 3:case 5:case 6:
                    $scope.form.audio_id = $('#editQuestionModal input[name=audio_id]').val();
                    $('#editQuestionModal .answers_block .answer_item').each(function(){
                        var act = $(this).attr('data-act');
                        $scope.form.answers.push({'act':act,'correct':$('input[type=radio]',this)[0].checked,'id':$('.id',this).val(),value:$('textarea',this).val()});
                    });
                break;
                case 4:
                    $scope.form.audio_id = $('#editQuestionModal input[name=audio_id]').val();
                    $('#editQuestionModal .answers_block .answer_item').each(function(){
                        var act = $(this).attr('data-act');
                        $scope.form.answers.push({'act':act,'id':$('.id',this).val(),value:$('textarea',this).val()});
                    });
                break;
                case 2:
                    $('#editQuestionModal .answers_block .answer_item').each(function(){
                        var act = $(this).attr('data-act');
                        $scope.form.answers.push({'act':act,'correct':$('input[type=radio]',this)[0].checked,'id':$('.id',this).val(),value:$('input[name=image_id]',this).val()});
                    });
                break;
            }
            
            console.log($scope.form);
            
            $http({
                method:'POST',
                url:'/questions/edit/',
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
                            <div class="form-group" ng-if='type=="text"'>
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
                            
                            <div class="form-group" ng-if='type=="audio"'>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Аудио</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="uploadFileBtn">Загрузить
                                            <iframe id="hiddenIframeUpload" src="{{'/questions/loadeditaudio/'}}"></iframe>
                                            <input type="hidden" name="audio_id" value="{{form.audio_id}}">
                                        </div>
                                        
                                        <div  ng-click="listen($event)" data-src="{{'/assets/audios/'+form.filename}}" class="btn btn-primary listen_btn">Слушать</div>
                                        <script>
                                            function editAudio(filename,id,index){
                                                console.log('!!',filename,id);
                                                $('#editQuestionModal input[name=audio_id]').val(id);
                                                $('#editQuestionModal .listen_btn').attr('data-src',filename).css({'display':'inline-block'});
                                            }
                                            function editAudioError(error){
                                                console.log('editError');
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if='type == "audio"'>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Описание</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="audio_description" ng-model="form.audio_description">                                        
                                    </div>
                                </div>
                            </div>
                            <script>
                                function editAudio(filename,id,index){
                                    console.log('!!',filename,id);
                                    $('#editQuestionModal input[name=audio_id]').val(id);
                                    $('#editQuestionModal .listen_btn').attr('data-src',filename).css({'display':'inline-block'});
                                }
                                function editAudioError(error){
                                    console.log('editError');
                                }
                            </script>
                            
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
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 1">
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
                                
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 2">
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
                                
                                
                               <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 3">
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
                                
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 4">
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
                                
                                
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 5">
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
                                
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 6">
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
                                
                                <style>
                                    #editQuestionModal .preview{
                                        width:100px;
                                        height:100px;
                                        background: grey;
                                    }
                                </style>
                                
                                <div class="form-group" ng-repeat="item in answer_edit" ng-if="mode == 2">
                                    <div class="answer_item" data-act="{{item.act}}" data-index='{{$index}}'>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="uploadFileBtn">Загрузить
                                                    <iframe id="hiddenIframeUpload" src="{{'/questions/loadeditimage/?index='+$index}}"></iframe>
                                                </div>
                                                
                                                <div ng-click="selectImage($event,item)" class="btn btn-primary">Выбрать</div>
                                                <input type="hidden" name="image_id" value="{{item.image_id}}">
                                                <input type="hidden" class="id" name="id" value="{{item.id}}">                                                
                                            </div>
                                            <div class="col-sm-4">
                                                <div class='preview' style="background:url(/assets/images/{{item.filename}}) no-repeat center center; background-size:cover"></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <input type="radio" name="answer" ng-checked="form.correct == item.id">
                                            </div>
                                            <div class="col-sm-2">
                                                <div ng-click="item.act='delete'" class="glyphicon glyphicon-remove delete_element"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <script>
                                        function editImage(filename,id,index){
                                            var parent = $('#editQuestionModal .answers_block .answer_item[data-index='+index+']');                                        
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
                    </div>
                    <input value="Применить" class="btn btn-primary" type="submit" style="margin-bottom:15px;">
                    <input type="hidden" name="id" value="" ng-model="form.id">                    
                </form>
            </div>
        </div>
    </div>
</div>