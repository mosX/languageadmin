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
            .question_block .selected{
                border-left:3px solid red;
            }
            .question_block .match{
                border-left:3px solid blue;
            }
        </style>
        <div class="content">
            
            <?php foreach($this->m->data as $item){ ?>
                <div class="question_block">
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
                </div>
            <?php } ?>
        </div>
    </div>
</div>