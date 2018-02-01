<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('tasks_top_menu') ?>
    
    <div class="right_sidebar">
        <form action="" method="">
            <div class="form-group">
                <select class="form-control">
                    <option>Сегодня</option>
                </select>
            </div>

            <div class="form-group">
                <input type="text" placehold="value" class="form-control">
            </div>
            <div class="form-group">
                <select class="form-control">
                    <option>Сегодня</option>
                </select>
            </div>

            <div class="form-group">
                <textarea class="form-control" placeholder="Добавить коментарий"></textarea>  
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-default" value="Сохранить">
                <input type="button" class="btn btn-danger cancel" value="Отменить">
            </div>
        </form>
    </div>
    
    <script>
        angular.module("app").requires.push('dndLists');
        
        app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
            $scope.today = 
        }]);
    </script>
    
    <style>
        .pipeline{
            display:table;
            
            table-layout: fixed;
            width:calc(100% - 15px);
            margin:auto;
        }
        .pipeline .pipeline_head,.pipeline .pipeline_body{
            display:table-header-group;
        }
        .pipeline .pipeline_head .pipeline_cell{            
            padding: 10px;
        }
        .pipeline .pipeline_body .pipeline_cell{
            padding: 10px;
        }
        .pipeline .pipeline_body .pipeline_cell.expired .task_item{
            border-color: #f37575;
            color: #f37575;
        }
        .pipeline .pipeline_body .pipeline_cell.expired .task_header .left ,.pipeline .pipeline_body .pipeline_cell.expired a{            
            color: #f37575;
        }
        .pipeline .pipeline_head .pipeline_cell .pipeline_title{
            text-align: center;            
            font-size:14px;
            font-weight:bolder;
            border-bottom: 2px solid #676E79;
            padding-bottom:15px;
        }
        .pipeline_row{
            display:table-row;
        }
        .pipeline_cell{
            display:table-cell;
        }
        
        .task_item{
            position:relative;
            padding:15px 10px 15px 35px;
            width:100%;
            min-height: 80px;
            background: #F1F2F4;
            border-top:3px solid #749E42;
            border-bottom:2px solid #D2D3D6;
            border-left:1px solid #E2E3E6;
            border-right:1px solid #E2E3E6;
            margin-bottom:10px; 
        }
        .task_item .task_header{
            margin-bottom:5px;
        }
        .task_item .task_header .left{
            font-size:12px;
            color: #676e79;
            display:inline-block;
            vertical-align: top;
        }
        .task_item .task_header .right{
            font-size:12px;
            float:right;
        }
        .task_item .task_header .right a{
            color: #676e79;
            font-weight:bolder; 
        }
        
        .task_item .icon_follow{
            position:absolute;
            top:15px;
            left:11px;
            width:17px;
            height:17px;
            background-position:0 -215px;
            background-image: url('/html/images/sprite2.png');
        }
    </style>
    <script>
        /*$('document').ready(function(){
            
            $('.task_item').draggable();    
            console.log($('.task_item'));
        });*/
    </script>
    <div class='pipeline'>
        <div class='pipeline_head'>
            <div class='pipeline_row'>
                <?php if($this->m->data->yesterday){ ?>
                    <div class='pipeline_cell' style='width:100%;'>
                        <div class='pipeline_title' style="border-color:#f37575">ПРОСРОЧЕННЫЕ ЗАДАЧИ</div>
                    </div>
                <?php } ?>
                <div class='pipeline_cell' style='width:100%;'>
                    <div class='pipeline_title'>ЗАДАЧИ НА СЕГОДНЯ</div>
                </div>
                
                <div class='pipeline_cell' style='width:100%;'>
                    <div class='pipeline_title'>ЗАДАЧИ НА ЗАВТРА</div>
                </div>
            </div>
        </div>
        <div class='pipeline_body'>
            <div class='pipeline_row'>
                <div class='pipeline_cell expired'>
                    <?php if($this->m->data->yesterday){ ?>
                        <?php foreach($this->m->data->yesterday as $item){ ?>
                            <div class="task_item" >
                                <div class="icon_follow"></div>
                                <div class='task_header'>
                                    <div class='left'>Вчера <?=date("H:i",strtotime($item->date))?>, <b><?=$item->partnername?></b></div>
                                    <div class='right'>в <a href=''><?=$item->username?></a></div>
                                </div>
                                <div class='task_body'>Связаться с клиентом: <?=$item->comment?></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                
                <div class='pipeline_cell'>
                    <?php if($this->m->data->today){ ?>
                        <?php foreach($this->m->data->today as $item){ ?>
                            <div class="task_item" >
                                <div class="icon_follow"></div>
                                <div class='task_header'>
                                    <div class='left'>Завтра <?=date("H:i",strtotime($item->date))?>, <b><?=$item->partnername?></b></div>
                                    <div class='right'>в <a href=''><?=$item->username?></a></div>
                                </div>
                                <div class='task_body'>Связаться с клиентом: <?=$item->comment?></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                
                <div class='pipeline_cell'>
                    <?php if($this->m->data->tomorrow){ ?>
                        <?php foreach($this->m->data->tomorrow as $item){ ?>
                            <div class="task_item">
                                <div class="icon_follow"></div>
                                <div class='task_header'>
                                    <div class='left'>Завтра <?=date("H:i",strtotime($item->date))?>, <b><?=$item->partnername?></b></div>
                                    <div class='right'>в <a href=''><?=$item->username?></a></div>
                                </div>
                                <div class='task_body'>Связаться с клиентом: <?=$item->comment?></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>