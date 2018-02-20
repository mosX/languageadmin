<script>
    app.controller('pageCtrl', ['$scope', '$http', function ($scope, $http) {

    }]);
</script>            

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?=$this->m->module('topmenu' . DS . 'tasktable' . DS . 'main') ?>

        <div class="content" ng-controller="calendarCtrl">
                <script>
                    app.controller('calendarCtrl',['$scope','$rootScope','$http',function($scope,$rootScope,$http){                            
                        $scope.parent = '#calendar';
                        $scope.width = '500px';
                        $scope.height = '500px';
                        
                        $scope.table_data = <?=$this->m->data ? json_encode($this->m->data): []?>;
                        
                        
                        $scope.prev = {};    //данные за предыдущий месяц
                        $scope.current = {};    //данные за текущий месяц
                        $scope.next = {};    //данные за следующий месяц
                        
                        $scope.current_date = '';
                        $scope.calendar_elements = [];
                        
                        $scope.reservedDates = <?=$this->m->data ? json_encode($this->m->data) : []?>;
                        
                        
                        $scope.month_array = ['January','February','March','April','May','June',"July",'August','September','October','November','December'];
                        $scope.short_tags_array = ['Mon','Tue','Wed','Thu','Fri','Sat',"Sun"];
                        
                        var d = new Date(<?= time() * 1000 ?>);                        
                        $scope.month = d.getMonth()+1;
                        $scope.year = d.getYear()+1900;
                        
                        
                        /*$scope.clear = function(){
                            $('.c_box .c_dates',$scope.parent).empty();
                        },*/
    
                        $scope.initForm = function(event,day){
                            console.log('init Form');
                            
                            $rootScope.$broadcast('setDate',{day:day,month:$scope.month,year:$scope.year});
                            event.preventDefault();
                        }
                        
                        $scope.loadData = function(event,day){
                            $http({
                                url:'/tasktable/getdata/?date='+$scope.year+'-'+$scope.month+'-'+day,
                                method:'GET'
                            }).then(function(ret){
                                console.log(ret.data);
                                $scope.table_data = ret.data;
                            });
                            event.preventDefault();
                        }
    
                        $scope.checkReservatedDays = function(year, month, day){
                            var d;
                            
                            for(var key in $scope.reservedDates){
                                d = new Date($scope.reservedDates[key].start_timestamp*1000);
                                
                                if(day == d.getDate() && month == d.getMonth()+1 && year == d.getYear()+1900){
                                    return 'reserved';    
                                    //return true;
                                }
                            }

                            return;
                        },
    
                        $scope.checkHoliday = function(year,month,day){
                            var d = new Date(year,month,day);
                            var dayOfWeek = d.getDay();

                            if(dayOfWeek == 0 || dayOfWeek == 1){
                                return 'holiday';
                            }

                            return '';
                        },
                                
                        $scope.checkToday = function(year,month,day){
                            var date = new Date();

                            var m = date.getMonth()+1;
                            var y = date.getYear()+1900;
                            var d = date.getDate()+1;

                            if(year == y && month == m && day == d){
                                return 'today';
                            }
                            return '';
                        }
    
                        $scope.addDays = function(){
                            var k=0;
                            var grey = false;
                            

                            for(var i=0;i<6; i++ ){
                                if(i==0){   //первая неделя с частью предыдущего месяца
                                    if(this.current.first_day_of_week == 1){    //если первый день это понедельник то первоя строка это все прошлый месяц
                                        for(var j=$scope.prev.total_days-6;j<=$scope.prev.total_days;j++){
                                            $scope.calendar_elements.push({'act':'prev','day':j});
                                        }
                                    }else{
                                        var dayOfWeek = $scope.current.first_day_of_week;

                                        for(var j=$scope.prev.total_days-dayOfWeek+2;j<=$scope.prev.total_days;j++){                                            
                                            $scope.calendar_elements.push({'act':'prev','day':j});
                                        }
                                        
                                        for(var j=dayOfWeek ; j <= 7;j++){
                                            $scope.calendar_elements.push({'act':'current','day':(++k),'today':$scope.checkToday($scope.year,$scope.month,k+1),'holiday':$scope.checkHoliday($scope.year,$scope.month,k+1),'reservated':$scope.checkReservatedDays($scope.year,$scope.month,k+1)});
                                        }
                                    }
                                    
                                    continue;
                                }else{
                                    for(var j=0;j<7;j++){
                                        $scope.calendar_elements.push({'act':(grey?'next':"current"),'day':(++k),'today':$scope.checkToday($scope.year,$scope.month,k+1),'holiday':$scope.checkHoliday($scope.year,$scope.month,k+1),'reservated':$scope.checkReservatedDays($scope.year,$scope.month,k+1)});
                                        //console.log(k ,$scope.current.total_days);
                                        
                                        if(k == $scope.current.total_days){   //определяем следующий месяц                                            
                                            grey = true;
                                            k = 0;
                                        }
                                    }
                                }
                            }
                            console.log($scope.calendar_elements);
                        }

                        $scope.render = function(){
                            $('#calendar .c_box').css({'width':'500px','height':'500px'});
                            $scope.current.total_days = new Date($scope.year,$scope.month+1,0).getDate();   //получаем количество дней

                            $scope.current.first_day_of_week = new Date($scope.year,$scope.month,1).getDay();
                            $scope.current.first_day_of_week = $scope.current.first_day_of_week == 0?7:$scope.current.first_day_of_week;

                            $scope.prev.total_days = new Date($scope.year,$scope.month,0).getDate();

                            $scope.current_date = $scope.month_array[$scope.month]+' '+$scope.year;

                            $scope.addDays();
                        }

                        $scope.render();
                    }]);
                </script>
                <style>
                    .c_day{
                        float:left;
                        width:14.28%;
                        height:16%;
                    }
                    .c_dates{
                        
                        display:block;
                    }
                    .c_dates .date_tags{
                        width:100%;
                    }
                    .c_day_name{
                        width:14.28%;
                        float:left;
                    }
                    .days_content{
                        position:relative;
                        display:block;
                        width:100%;
                        height: 100%;
                    }
                    .days_content .c_day{
                        position: relative;
                        display:table;
                        
                    }
                    .days_content .c_day .cell{
                        display:table-cell;
                        vertical-align: middle;
                    }
                    .days_content .add_btn{
                        display:none;
                        position:absolute;
                        right:5px;
                        top:5px;
                        color: #3498db;
                    }
                    .days_content .c_day:hover .add_btn{
                        display:block;
                    }
                </style>
                
                <div id="calendar" >                    
                    <div class="c_box online">
                        <div class='header'>
                            <div class="prev_button"></div><div class="current_date">{{current_date}}</div><div class="next_button"></div>
                        </div>
                        <div class='c_dates'>
                            <div class='date_tags'>
                                <div ng-repeat="item in short_tags_array" class='c_day_name'>{{item}}</div>
                            </div>
                            
                            <div class="days_content">
                                <div  ng-repeat="item in calendar_elements" class='c_day {{item.act}} {{item.today}} {{item.holiday}} {{item.reserved}}'>
                                    <div ng-click="loadData($event,item.day)" class="cell">{{item.day}}</div>
                                    <div ng-click="initForm($event,item.day)" class="add_btn"><span class="glyphicon glyphicon-plus-sign"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table_holder">                    
                    <style>
                        .color_block{
                            width:40px;
                            height:40px;
                        }
                    </style>
                    <table class='table'>
                        <tr>
                            <th style="width:37px;"></th>
                            <th style="width:350px;">Название</th>
                            <th>Предмет</th>
                            <th>Цвет</th>                        
                            <th>Время</th>
                            
                            <th style="width:100px"></th>
                        </tr>
                        
                        <tr data-id="{{item.id}}" ng-repeat="item in table_data track by $index">
                            <td>

                            </td>   
                            <td class="username_td">
                                {{item.message}}
                            </td>

                            <td>
                                {{item.lessons_name}}
                            </td>

                            <td>
                                <div class="color_block" style="background:#{{item.color}}"></div>
                            </td>

                            <td>
                                {{item.start|date:"HH:mm"}} {{item.end|date:"HH:mm"}}
                                <div style="height: 40px; width:40px;" href="" class="glyphicon glyphicon-remove-sign"></div>
                            </td>

                            <td>
                                
                                <a ng-click="editModal($event,item.id)" class="edit_tags_ico" href=""></a>
                                <a ng-click='showBlockModal($event,item.id)' class="del_user_ico" href=""></a>
                            </td>
                        </tr>

                      
                    </table>
                </div>
        </div>
    </div>
</div>
