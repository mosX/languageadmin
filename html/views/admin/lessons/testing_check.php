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
            .question_block[data-type="1"] .answer{
                padding-left:20px;
                height: 30px;
                border-left:3px solid transparent;
            }
            .question_block[data-type="1"] .correct{
                border-left:3px solid green;
            }
            .question_block[data-type="1"] .selected{
                border-left:3px solid red;
            }
            .question_block[data-type="1"] .match{
                border-left:3px solid blue;
            }
        </style>
        
        <style>
            .question_block[data-type="2"]{
                
            }
            
            .question_block[data-type="2"] .answer{
                border:2px solid transparent;
            }
            .question_block[data-type="2"] .correct{
                border:2px solid green;
            }
            .question_block[data-type="2"] .selected{
                border:2px solid red;
            }
            .question_block[data-type="2"] .match{
                border:2px solid blue;
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
        </style>
        
        <div class="content">
            <?php foreach($this->m->data as $item){ ?>
                <div class="question_block" data-type="<?=$item->type?>">
                    <?php if($item->type == 1){ ?>
                        <div class="question">
                            
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>            
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match"><?=$answer->text?></div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><?=$answer->text?></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><?=$answer->text?></div>
                            <?php }else{ ?>
                                <div class="answer"><?=$answer->text?></div>
                            <?php } ?>
                        <?php } ?>
                    <?php }else if($item->type == 2){ ?>
                        <div class="question">
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>            
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match">
                                    <img src="/assets/images/<?=$answer->filename?>">
                                </div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><img src="/assets/images/<?=$answer->filename?>"></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><img src="/assets/images/<?=$answer->filename?>"></div>
                            <?php }else{ ?>
                                <div class="answer"><img src="/assets/images/<?=$answer->filename?>"></div>
                            <?php } ?>
                        <?php } ?>
                    <?php }else if($item->type == 3){ ?>
                        <div class="question">
                            
                            <?=$item->value?> 
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>            
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match"><?=$answer->text?></div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><?=$answer->text?></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><?=$answer->text?></div>
                            <?php }else{ ?>
                                <div class="answer"><?=$answer->text?></div>
                            <?php } ?>
                        <?php } ?>
                    <?php }else if($item->type == 4){ ?>
                        <div class="question">
                            <?=$item->value?>
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match"><?=$answer->text?></div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><?=$answer->text?></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><?=$answer->text?></div>
                            <?php }else{ ?>
                                <div class="answer"><?=$answer->text?></div>
                            <?php } ?>
                        <?php } ?>
                    <?php }else if($item->type == 5){ ?>
                        <div class="question">
                            <div ng-click="listen('<?=$item->audio?>')" class="btn btn-primary">Прослушать</div>
                            <?=$item->result_answer?>
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match"><?=$answer->text?></div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><?=$answer->text?></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><?=$answer->text?></div>
                            <?php }else{ ?>
                                <div class="answer"><?=$answer->text?></div>
                            <?php } ?>
                        <?php } ?>
                    <?php }else if($item->type == 6){ ?>
                        <div class="question">
                            <div ng-click="listen('<?=$item->audio?>')" class="btn btn-primary">Прослушать </div>
                            <?=$item->result_answer?>
                            <?php if($item->time){ ?>
                                <span style="color: #222; font-size:12px;">(<?=$item->time?>)</span>
                            <?php } ?>
                        </div>
                        <?php foreach($item->answers as $answer){?>
                            <?php if($answer->correct && $answer->selected){ ?>
                                <div class="answer match"><?=$answer->text?></div>
                            <?php }else if($answer->correct){ ?>
                                <div class="answer correct"><?=$answer->text?></div>
                            <?php }else if($answer->selected){ ?>
                                <div class="answer selected"><?=$answer->text?></div>
                            <?php }else{ ?>
                                <div class="answer"><?=$answer->text?></div>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>