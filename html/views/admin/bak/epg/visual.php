<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){        
        var r,g,b;
        $scope.ws = document.getElementById("canvas");
        $scope.ws.width = $('#canvas').width();
        $scope.ws.height = $('#canvas').height();
        $scope.ctx = $scope.ws.getContext("2d");
        $scope.width = $scope.ws.width;
        $scope.height = $scope.ws.height;
        
        $scope.timeline = [];
        
        $scope.epg = <?=json_encode($this->m->data)?>;
            
        $scope.start_date = new Date(<?=strtotime($this->m->filter->date_from)*1000?>);
        $scope.end_date = new Date(<?=strtotime($this->m->filter->date_to)*1000?>);
                
        $scope.top_padding = 50;
        $scope.left_padding = 50;
        
        var hours  = ($scope.end_date.getTime() - $scope.start_date.getTime())/1000 / 60 / 60 ;       
        //в зависимости от количества часов устанавливае высоту Канваса
        $scope.height = $scope.ws.height = 30*hours;
        
        $('#canvas').height($scope.height+'px');
        
        $scope.getProgramTimeText = function(start,stop){
            start = new Date(start);
            stop = new Date(stop);
            
            var start_hours = start.getHours();
            start_hours = start_hours < 10 ? '0'+start_hours : start_hours;
            var start_minutes = start.getMinutes();
            start_minutes = start_minutes < 10 ? '0'+start_minutes : start_minutes;
            
            var stop_hours = stop.getHours();
            stop_hours = stop_hours < 10 ? '0'+stop_hours : stop_hours;
            var stop_minutes = stop.getMinutes();
            stop_minutes = stop_minutes < 10 ? '0'+stop_minutes : stop_minutes;
            
            return start_hours+':'+start_minutes + ' - '+ stop_hours+':'+stop_minutes;
        }
        
        //определяем степ
        var length  = Math.sqrt( Math.pow($scope.left_padding-$scope.left_padding,2) + Math.pow($scope.height-$scope.top_padding-$scope.top_padding,2));
        
        var step = length / hours / 60;
               
        //вертикальные полосы определяющие разные дни
        //берем начало и смотрим какой єто отрезок до конца дня        
        var temp_date = new Date($scope.start_date.getTime());
        var start_time,start_y,end_y;
        while(temp_date.getTime() < $scope.end_date.getTime()){
            start_time = new Date(temp_date.getTime());
            start_y = $scope.height - $scope.top_padding - ((start_time.getTime() - $scope.start_date.getTime() ) /1000/60) * step;

            temp_date.setHours(23);
            temp_date.setMinutes(59);
            temp_date.setSeconds(60);
            
            if(temp_date.getTime() > $scope.end_date.getTime()){
                temp_date = new Date($scope.end_date.getTime());
            }
            
            end_y = $scope.height - $scope.top_padding - ((temp_date.getTime() - $scope.start_date.getTime() ) /1000/60) * step;
                        
            $scope.ctx.save();
                $scope.ctx.beginPath();
                    $scope.ctx.lineWidth = 5;
                    $scope.ctx.strokeStyle = 'rgba('+Math.floor(Math.random()*255)+','+Math.floor(Math.random()*255)+','+Math.floor(Math.random()*255)+',1)';
                    $scope.ctx.moveTo($scope.left_padding,start_y);
                    $scope.ctx.lineTo($scope.left_padding,end_y);

                    $scope.ctx.stroke();
                $scope.ctx.closePath();
            $scope.ctx.restore();            
        }
       
        //рисуем полосочки разделяя каждый час отдельно... с расчета на сутки часа
        for(var i=length,j=$scope.start_date.getHours(); i >= 0;i-=step*60,j++){
            $scope.ctx.save();
                $scope.ctx.beginPath();
                    $scope.ctx.lineWidth = 1;
                    $scope.ctx.strokeStyle = 'black';
                    $scope.ctx.moveTo($scope.left_padding - 5,i+50);
                    $scope.ctx.lineTo($scope.left_padding + 5,i+50);

                    $scope.ctx.stroke();
                $scope.ctx.closePath();
                
                $scope.ctx.font="12px Georgia";
                var text = (j < 10? '0'+j:j)+':00';
                $scope.ctx.fillText(text,$scope.left_padding-40,i+50+4);

            $scope.ctx.restore();
            
            if(j == 24) j = 0;
        }
                
        for(var key in $scope.epg){
            var start_y = $scope.height - $scope.top_padding - (($scope.epg[key].start*1000 - $scope.start_date.getTime() ) /1000/60) * step;
            var end_y = $scope.height - $scope.top_padding - (($scope.epg[key].stop*1000 - $scope.start_date.getTime() ) /1000/60) * step;
            
            r = Math.floor(Math.random()*255);
            g = Math.floor(Math.random()*255);
            b = Math.floor(Math.random()*255);
                    
            $scope.ctx.save();
                /*$scope.ctx.beginPath();
                    $scope.ctx.lineWidth = 5;
                    $scope.ctx.strokeStyle = 'rgba('+r+','+g+','+b+',1)';
                    $scope.ctx.moveTo($scope.left_padding+30,start_y);
                    $scope.ctx.lineTo($scope.left_padding+30,end_y);

                    $scope.ctx.stroke();
                $scope.ctx.closePath();*/
                
                $scope.ctx.beginPath();                    
                    $scope.ctx.fillStyle = 'rgba('+r+','+g+','+b+',0.5)';
                    
                    $scope.ctx.rect($scope.left_padding,start_y,90,end_y-start_y);
                    $scope.ctx.fill(); 
                $scope.ctx.closePath();
                
                var length  = Math.sqrt( Math.pow(start_y-end_y,2) + Math.pow(0,2));
                
                //текст программы
                $scope.ctx.font="12px Georgia";
                $scope.ctx.fillStyle = 'rgba('+r+','+g+','+b+',1)';
                //$scope.ctx.fillStyle = 'rgba('+(r/2)+','+(g/2)+','+(b/2)+',1)';
                
                var text = $scope.epg[key].title;  
                var y = ((start_y - end_y) / length ) + (length/2);
                $scope.ctx.fillText(text,$scope.left_padding+100,end_y + y + 4);                
                
                //текст времени начала и конца
                $scope.ctx.font="12px Georgia";
                $scope.ctx.fillStyle = 'rgba('+(r/2.5)+','+(g/2.5)+','+(b/2.5)+',1)';
                
                var text = $scope.getProgramTimeText($scope.epg[key].start*1000,$scope.epg[key].stop*1000);
                $scope.ctx.fillText(text,$scope.left_padding+10,end_y + y + 4);                
            $scope.ctx.restore();
        }
    }]);
</script>

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu'.DS.'epg_visual') ?>
        
        <div class="content">
            <canvas style='width:800px; height:700px; outline: 1px solid red;' id='canvas'></canvas>
        </div>
    </div>
</div>