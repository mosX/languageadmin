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
                        $scope.reservedDates = []
                                                
                        $scope.month_array = ['January','February','March','April','May','June',"July",'August','September','October','November','December'];
                        $scope.short_tags_array = ['Mon','Tue','Wed','Thu','Fri','Sat',"Sun"];
                        
                        var d = new Date(<?= time() * 1000 ?>);
                        $scope.month = d.getMonth();
                        $scope.year = d.getYear()+1900;
                        $scope.day = d.getDate();
                        
                        $scope.initForm = function(event,day){
                            $rootScope.$broadcast('setDate',{day:day,month:$scope.month,year:$scope.year});
                            event.preventDefault();
                        }
                        
                        $scope.loadData = function(event,index){
                            $scope.day = $scope.calendar_elements[index].day;
                            
                            for(var key in $scope.calendar_elements){
                                if(key == index){
                                    $scope.calendar_elements[key].active = true;
                                }else{
                                    $scope.calendar_elements[key].active = false;
                                }
                            }
                            
                            if($scope.calendar_elements[index].month != $scope.month){
                                $scope.month = $scope.calendar_elements[index].month;
                                $scope.year = $scope.calendar_elements[index].year;
                                $scope.render();
                            }
                            
                            //проверяем какой єто месяц
                            $http({
                                url:'/tasktable/getdata/?date='+$scope.year+'-'+($scope.month+1)+'-'+$scope.day,
                                method:'GET'
                            }).then(function(ret){
                                console.log(ret.data);
                                $scope.table_data = ret.data;
                            });
                            event.preventDefault();
                        }
                        
                        $scope.reInit = function(){
                            $scope.current_date = $scope.month_array[$scope.month]+' '+$scope.year;   
                        }
                        
                        $scope.getFilledDatas = function(){
                            $http({
                                url:'/tasktable/filled/?date='+$scope.year+'-'+($scope.month+1)+'-'+$scope.day,
                                type:'GET'                                
                            }).then(function(ret){
                                console.log(ret.data);
                                //$scope.reservedDates = <?=$this->filled_data ? json_encode($this->filled_data) : []?>;
                                $scope.reservedDates = ret.data;
                                
                                for(var key in $scope.calendar_elements){
                                    $scope.calendar_elements[key].reservated = $scope.checkReservatedDays($scope.year,$scope.month,$scope.calendar_elements[key].day);
                                }
                            });
                        }
                        
                        $scope.nextMonth = function(){
                            console.log('NEXT MONTH');
                            //var self = this;
                            var d = new Date($scope.year,$scope.month+1);
                            $scope.month = d.getMonth();
                            $scope.year = d.getYear()+1900;

                            $scope.render();
                            
                            //this.getFilledDatas(function(){self.render();});
                        }

                        $scope.prevMonth = function(){
                            //var self = this;
                            var d = new Date($scope.year,$scope.month-1);
                            $scope.month = d.getMonth();
                            $scope.year = d.getYear()+1900;
                            
                            $scope.render();

                            //this.getFilledDatas(function(){self.render();});
                        }
    
                        $scope.checkReservatedDays = function(year, month, day){
                            var d;
                            
                            for(var key in $scope.reservedDates){
                                d = new Date($scope.reservedDates[key]*1000);
                                
                                if(day == d.getDate() && month == d.getMonth() && year == d.getYear()+1900){
                                    return true;
                                }
                            }

                            return false;
                        },
    
                        $scope.checkHoliday = function(year,month,day){
                            var d = new Date(year,month,day);
                            var dayOfWeek = d.getDay();

                            if(dayOfWeek == 0 || dayOfWeek == 1){
                                return 'holiday';
                            }

                            return '';
                        },
                                
                        $scope.checkActive = function(year,month,day){
                            var date = new Date();

                            var m = date.getMonth();
                            var y = date.getYear()+1900;
                            var d = date.getDate();
                            
                            if(year == y && month == m && day == d){
                                return true;
                            }
                            return false;
                        }
    
                        $scope.addDays = function(){
                            var k=0;
                            var grey = false;
                            $scope.calendar_elements = [];
                            
                            
                            var date = new Date($scope.year,$scope.month-1);
                            var prev_month = date.getMonth();
                            var prev_year = date.getYear()+1900;
                            date = new Date($scope.year,$scope.month+1);
                            var next_month = date.getMonth();
                            var next_year = date.getYear()+1900;
                            
                            console.log(prev_month,$scope.month,next_month);
                            
                            for(var i=0;i<6; i++ ){
                                if(i==0){   //первая неделя с частью предыдущего месяца
                                    if(this.current.first_day_of_week == 1){    //если первый день это понедельник то первоя строка это все прошлый месяц
                                        for(var j=$scope.prev.total_days-6;j<=$scope.prev.total_days;j++){
                                            $scope.calendar_elements.push({'act':'prev','day':j,month:prev_month,year:prev_year});
                                        }
                                    }else{
                                        var dayOfWeek = $scope.current.first_day_of_week;

                                        for(var j=$scope.prev.total_days-dayOfWeek+2;j<=$scope.prev.total_days;j++){                                            
                                            $scope.calendar_elements.push({'act':'prev',day:j,month:prev_month,year:prev_year});
                                        }
                                        
                                        for(var j=dayOfWeek ; j <= 7;j++){
                                            $scope.calendar_elements.push({'act':'current',day:(++k),month:$scope.month,year:$scope.year,'active':$scope.checkActive($scope.year,$scope.month,k),'holiday':$scope.checkHoliday($scope.year,$scope.month,k+1)});
                                        }
                                    }
                                    
                                    continue;
                                }else{
                                    for(var j=0;j<7;j++){
                                        if(grey){   //следующий месяц
                                            $scope.calendar_elements.push({'act':'next',day:(++k),month:next_month,year:next_year,'active':$scope.checkActive($scope.year,$scope.month,k),'holiday':$scope.checkHoliday($scope.year,$scope.month,k+1)});
                                        }else{  //текущий месяц
                                            $scope.calendar_elements.push({'act':"current",day:(++k),month:$scope.month,year:$scope.year,'active':$scope.checkActive($scope.year,$scope.month,k),'holiday':$scope.checkHoliday($scope.year,$scope.month,k+1)});
                                        }
                                                                                
                                        if(k == $scope.current.total_days){   //определяем следующий месяц                                            
                                            grey = true;
                                            k = 0;
                                        }
                                    }
                                }
                            }
                            //console.log($scope.calendar_elements);
                        }

                        $scope.render = function(){
                            $scope.getFilledDatas();
                                                        
                            $('#calendar .c_box').css({'width':'500px','height':'500px'});
                            $scope.current.total_days = new Date($scope.year,$scope.month+1,0).getDate();   //получаем количество дней

                            $scope.current.first_day_of_week = new Date($scope.year,$scope.month,1).getDay();
                            $scope.current.first_day_of_week = $scope.current.first_day_of_week == 0?7:$scope.current.first_day_of_week;

                            $scope.prev.total_days = new Date($scope.year,$scope.month,0).getDate();

                            $scope.current_date = $scope.month_array[$scope.month]+' '+$scope.year;

                            $scope.addDays();
                        }
                        
                        $scope.addException = function(event,id){
                            //http://tasktable/tasks/clear_permanent/5/?date=2018-02-27
                            $http({
                                url:'/tasktable/clear_permanent/'+id+'/?date='+$scope.year+'-'+$scope.month+'-'+$scope.day,
                                method:'GET'
                            }).then(function(ret){
                                console.log(ret.data);
                            });
                            
                            event.preventDefault();
                        }

                        $scope.render();
                        
                        
                        
                        $scope.editForm = function(event,id){
                            console.log(id);
                            $http({
                                url:'/tasktable/edit_data/?id='+id,
                                method:'GET',
                            }).then(function(ret){
                                console.log(ret.data);
                                $scope.$broadcast('editData', {
                                    data: ret.data
                                });
                                $('#editModal').modal('show');
                            });
                            event.preventDefault();
                        }

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
                
                <div id="calendar">
                    <div class="c_box online">
                        <div class='header'>
                            <div ng-click="prevMonth()" class="prev_button"></div><div class="current_date">{{current_date}}</div><div ng-click="nextMonth()" class="next_button"></div>
                        </div>
                        <div class='c_dates'>
                            <div class='date_tags'>
                                <div ng-repeat="item in short_tags_array" class='c_day_name'>{{item}}</div>
                            </div>
                            
                            <div class="days_content">
                                <div  ng-repeat="item in calendar_elements" class='c_day {{item.act}} {{item.active ? "active":""}} {{item.holiday}} {{item.reservated?"reserved":""}}'>
                                    <div ng-click="loadData($event,$index)" class="cell">{{item.day}}</div>
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
                            <td></td>

                            <td class="username_td">{{item.message}}</td>
                                
                            <td>{{item.lessons_name}}</td>
                                
                            <td>
                                <div class="color_block" style="background:#{{item.color}}"></div>
                            </td>

                            <td>{{item.start*1000|date:"HH:mm"}} {{item.end*1000|date:"HH:mm"}}</td>
                                
                            <td>
                                <div ng-if="item.permanent == 1" style="font-size: 20px; cursor:pointer" ng-click="addException($event,item.id)" class="glyphicon glyphicon-remove-sign"></div>
                                <a ng-click="editForm($event,item.id)" class="edit_tags_ico" href=""></a>
                                <a ng-click='showBlockModal($event,item.id)' class="del_user_ico" href=""></a>
                            </td>
                        </tr>
                    </table>
                </div>
        </div>
    </div>
</div>
