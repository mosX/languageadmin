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
        angular.module("app").requires.push('dndLists');
        
        app.controller('pageCtrl', ['$scope','$http',function($scope,$http){
            $scope.yesterday = <?=json_encode($this->m->data->yesterday)?> || [];
            $scope.today = <?=json_encode($this->m->data->today)?> || [];
            $scope.tomorrow = <?=json_encode($this->m->data->tomorrow)?> || [];
            $scope.deleted = [];
                        
            $scope.moved = function(list,index){
                list.splice(index, 1);                
            }
            
            $scope.inserted = function(index,type){
                console.log('Inserted',index,type);
                
                console.log($scope.deleted);
                var id = 0;
                switch(type){
                    case 'yesterday': id = $scope.yesterday[index].id; break;
                    case 'today': id = $scope.today[index].id; break;
                    case 'tomorrow': id = $scope.tomorrow[index].id; break;
                    case 'deleted': id = $scope.deleted[index].id; break;
                }
                
                $http({
                    url:'/tasks/move/'+type+'/?id='+id,
                    method:'GET',                    
                }).then(function(ret){
                    console.log(ret.data);
                });
            }
            
            $scope.formatDate = function(date){
                date *= 1000;
                //console.log(date);
                var current_date = new Date();
                
                current_date.setHours(0);
                current_date.setMinutes(0);
                current_date.setSeconds(0);
                                
                var result =(date - current_date.getTime()) / 1000 / 60/60;
                var date_value = '';
                var d = new Date(date);
                
                var hours = d.getHours();
                hours = hours < 10 ? '0'+hours : hours;
                var minutes = d.getMinutes();
                minutes = minutes < 10 ? '0'+minutes : minutes;
                var seconds = d.getSeconds();
                seconds = seconds < 10 ? '0'+seconds : seconds;

                if(result > 0 && result < 24){
                    date_value = 'Сегодня';
                }else if(Math.abs(result) < 24 && result < 0){
                    date_value = 'Вчера';
                }else if(result > 24 && result < 48){
                    date_value = 'Завтра';
                }else{                    
                    var year = d.getYear()+1900;
                    
                    var month = d.getMonth()+1;
                    month = month < 10? '0'+month : month;
                    var day = d.getDate();
                    day = day < 10? '0'+day : day;
                            
                    date_value = year + '-'+month+'-'+day;
                }
                
                return date_value+ ' ' + hours+':'+minutes+':'+seconds
            }
        }]);
    </script>
    <div class='pipeline'>
        <div class='pipeline_head'>
            <div class='pipeline_row'>                
                <div class='pipeline_cell' style='width:100%;'>
                    <div class='pipeline_title' style="border-color:#f37575">ПРОСРОЧЕННЫЕ ЗАДАЧИ</div>
                </div>
                
                <div class='pipeline_cell' style='width:100%;'>
                    <div class='pipeline_title'>ЗАДАЧИ НА СЕГОДНЯ</div>
                </div>
                
                <div class='pipeline_cell' style='width:100%;'>
                    <div class='pipeline_title'>ЗАДАЧИ НА ЗАВТРА</div>
                </div>
            </div>
        </div>
        <style>
            .pipeline_row .dndDraggingSource {   /*елемент который перетягиваем*/
                display: none;
            }
            .pipeline_row .dndPlaceholder {      /*пустое место**/
                background-color: #ddd;
                display: block;
                min-height: 42px;
            }
        </style>
        
        <div class='pipeline_body'>
            <div class='pipeline_row'>
                <ul dnd-list="yesterday" dnd-inserted="inserted(index,'yesterday')"  class='pipeline_cell expired'>
                    <li ng-repeat="item in yesterday" ng-show="yesterday" class="task_item"
                        dnd-draggable="item"
                        dnd-effect-allowed="move"
                        dnd-moved="moved(yesterday,$index)"                                               
                        >
                        <div class="icon_follow"></div>
                        <div class='task_header'>
                            <div class='left'>{{formatDate(item.date)}}, <b>{{item.partnername}}</b></div>
                            <div class='right'>в <a href=''>{{item.username}}</a></div>
                        </div>
                        <div class='task_body'>Связаться с клиентом: {{item.comment}}</div>
                    </li>                    
                </ul>
                
                <ul dnd-list="today" dnd-inserted="inserted(index,'today')" class='pipeline_cell'>
                    <li ng-repeat="item in today" class="task_item"
                        dnd-draggable="item"
                        dnd-moved="today.splice($index, 1)"
                        dnd-selected="selected = item"
                        dnd-effect-allowed="move">
                        <div class="icon_follow"></div>
                        <div class='task_header'>
                            <div class='left'>{{formatDate(item.date)}}, <b>{{item.partnername}}</b></div>
                            <div class='right'>в <a href=''>{{item.username}}</a></div>
                        </div>
                        <div class='task_body'>Связаться с клиентом: {{item.comment}}</div>
                    </li>
                </ul>
                
                <ul dnd-list="tomorrow" dnd-inserted="inserted(index,'tomorrow')" class='pipeline_cell'>
                    <li ng-repeat="item in tomorrow" class="task_item"
                        dnd-draggable="item"
                        dnd-moved="tomorrow.splice($index, 1)"
                        dnd-selected="selected = item"
                        dnd-dragstart="start(item,'tomorrow')"
                        dnd-dragend="end(item,'tomorrow')"
                        dnd-effect-allowed="move">
                        <div class="icon_follow"></div>
                        <div class='task_header'>
                            <div class='left'>{{formatDate(item.date)}}, <b>{{item.partnername}}</b></div>
                            <div class='right'>в <a href=''>{{item.username}}</a></div>
                        </div>
                        <div class='task_body'>Связаться с клиентом: {{item.comment}}</div>
                    </li>                                        
                </ul>
            </div>
            <style>
                #trashcan{
                    position:fixed;
                    bottom:0px;
                    left:0px;
                    height:70px;
                    background: none;
                    width:100%;
                    margin:0px;
                    padding:0px;
                }
                #trashcan:hover,#trashcan .dndPlaceholder{
                    background: rgba(200,0,0,0.2);
                    height:70px;
                    width:100%;
                }
            </style>
            <ul id="trashcan" dnd-list="deleted" dnd-inserted="inserted(index,'deleted')" class='pipeline_cell'></ul>            
        </div>
    </div>
</div>