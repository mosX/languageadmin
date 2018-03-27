<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
        $scope.editModal = function(event,id){
            $http({
                url:'/system/getdata/?id='+id,
                method:'GET',
            }).then(function(ret){
                $scope.form = ret.data;
                
                $('#editModal').modal('show');
            });
            
            event.preventDefault();
        }
        
        $scope.listen = function(filename){
            var sound = new Audio('/assets/audios/'+filename);
            sound.play();
        }
        
        $scope.submit = function(event){
            $http({
                url:location.href,
                method:'POST',
                data:{role:$scope.form.role,id:$scope.form.id}
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

<script>
    $('document').ready(function(){
        $('.timepicker').datetimepicker({
            locale: 'ru',
            format: 'DD-MM-YYYY HH:mm'
        });
    });
</script>

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu'.DS.'lessons'.DS.'testing_check') ?>
        <style>
            .question_block{
                margin-bottom:20px;
            }
            .question_block .question{
                font-size: 16px;
                font-weight:bolder;
            }
            .question_block .answer{
                padding-left:20px;
                height: 30px;
                border-left:3px solid transparent;
            }
            .question_block .correct{
                border-left:3px solid green;
            }
            .question_block .wrong{
                border-left:3px solid red;
            }
        </style>
        
        <style>
            .question_block[data-type="2"]{
                
            }
            
            .question_block[data-type="2"] .answer{
                text-align: center;
                padding:0px;
                border:2px solid #ddd;
                
            }
            .question_block[data-type="2"] .correct{
                border:2px solid green;
            }
            .question_block[data-type="2"] .wrong{
                border:2px solid red;
            }
            
            .question_block[data-type="2"] .answer{
                width:100px;
                height: 100px;
                box-sizing: content-box;
                display:inline-block;
                margin-right: 20px;
            }
            .question_block[data-type="2"] .answer img{
                max-width: 100px;
                max-height: 100px;
            }
            
            
            .question_block{
                border-bottom:1px solid #ddd;
                padding-bottom: 20px;
            }
            .question_block .type_title{
                font-weight: bold;
                color: #FA7252;
            }            
            
            .question_block .result_answer{
                display:inline-block;
                vertical-align: middle;
                padding:2px 20px;
                text-align: center;
                border-radius: 7px;
                margin-left: 20px;
                border: 2px solid #337ab7;
            }
            
            .question_block .result_answer.correct{
                border:2px solid green;
            }
            .question_block .result_answer.wrong{
                border:2px solid red;
            }
        </style>
        
        <div class="content">
            
            <?php foreach($this->m->data as $item){ ?>
                <div class="question_block" data-type="<?=$item->type?>">
                    <?php if($item->type == 1){ ?>                    
                        <div class="type_title">Выберите Правильный ответ</div>
                        <div class="question">
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){ ?>
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_wrong?> <?=$status_correct?>"><?=$answer->text?></div>
                        <?php } ?>
                    <?php }else if($item->type == 2){ ?>
                        <div class="type_title">Выберите Изображение</div>
                            
                        <div class="question">
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>            
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_correct?> <?=$status_wrong?>">
                                <img src="/assets/images/<?=$answer->filename?>">
                            </div>
                        <?php } ?>
                    <?php }else if($item->type == 3){ ?>
                        <div class="type_title">Пропущенное слово</div>
                        <div class="question">
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>            
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_wrong?> <?=$status_correct?>"><?=$answer->text?></div>                            
                        <?php } ?>
                    <?php }else if($item->type == 4){ ?>
                        <div class="type_title">Написать перевод</div>
                        <div class="question">
                            <?=$item->value?>
                            
                            <div class="result_answer <?=$item->status?>"><?=$item->result_answer?></div>
                            
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_wrong?> <?=$status_correct?>"><?=$answer->text?></div>
                        <?php } ?>
                    <?php }else if($item->type == 5){ ?>
                        <div class="type_title">Прослушать и выбрать</div>
                        <div class="question">
                            <div ng-click="listen('<?=$item->audio?>')" class="btn btn-primary">Прослушать</div>
                            
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_wrong?> <?=$status_correct?>"><?=$answer->text?> <?=$item->correct?> <?=$item->result_answer?> <?=$answer->id?></div>
                        <?php } ?>
                    <?php }else if($item->type == 6){ ?>
                        <div class="type_title">Прослушать и написать</div>
                        <div class="question">
                            <div ng-click="listen('<?=$item->audio?>')" class="btn btn-primary">Прослушать </div>
                            <div class="result_answer <?=$item->status?>"><?=$item->result_answer?></div>
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){ ?>
                            <?php 
                                $status_correct = ($item->correct == $answer->id?'correct':'');
                                $status_wrong = ($item->result_answer == $answer->id && $item->status == 'wrong'?'wrong':'');
                            ?>
                            <div class="answer <?=$status_wrong?> <?=$status_correct?>"><?=$answer->text?></div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>