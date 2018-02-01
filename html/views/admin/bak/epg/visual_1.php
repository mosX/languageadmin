<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){        
        $scope.ws = document.getElementById("canvas");
        $scope.ws.width = 500;
        $scope.ws.height = 700;
        $scope.ctx = $scope.ws.getContext("2d");
        $scope.width = $scope.ws.width;
        $scope.height = $scope.ws.height;
        
        $scope.timeline = [];
        
        $scope.epg = <?=json_encode($this->m->data)?>;
        
        //устанавливаем диапазон времени
        var start_date = new Date();
        start_date.setDate(4);
        
        //console.log(start_date);
        var end_date = new Date();
        end_date.setHours(23);
        console.log(start_date, end_date);
        
        var hours  = (end_date.getTime() - start_date.getTime())/1000 / 60 / 60 ;
        for(var i=0,j=start_date.getHours();i <= hours;i++,j++){
            //console.log(j);
            if(j == 24) j =0;
        }
        
        var d = {};
        var start,end;
        for(var key in $scope.epg){
            d = new Date($scope.epg[key].start*1000);
            start = d.getMinutes() + d.getHours() * 60;
            
            d = new Date($scope.epg[key].stop*1000);
            end = d.getMinutes() + d.getHours() * 60;            
            
            $scope.timeline.push({start:start,end:end,title:$scope.epg[key].title});
        }
           
        //вертикальная линия определяющая отрезок времени
        $scope.ctx.save();
            $scope.ctx.beginPath();
                $scope.ctx.lineWidth = 1;
                $scope.ctx.strokeStyle = 'blue';
                $scope.ctx.moveTo(30,50);
                $scope.ctx.lineTo(30,$scope.height-50);
                
                $scope.ctx.stroke();
            $scope.ctx.closePath();
        $scope.ctx.restore();
        
        //рисуем полосочки разделяя каждый час отдельно... с расчета на сутки часа
        var length  = Math.sqrt( Math.pow(30-30,2) + Math.pow($scope.height-50-50,2));
        var step = 600 / 24;
        
        var hours = 24;
        for(var i=length,j=0; i >= 0;i-=step,j++){
            //console.log(i);
            $scope.ctx.save();
                $scope.ctx.beginPath();
                    $scope.ctx.lineWidth = 1;
                    $scope.ctx.strokeStyle = 'black';
                    $scope.ctx.moveTo(25,i+50);
                    $scope.ctx.lineTo(40,i+50);

                    $scope.ctx.stroke();
                $scope.ctx.closePath();
                
                $scope.ctx.font="12px Georgia";
                var text = (j < 10? '0'+j:j)+':00';
                $scope.ctx.fillText(text,40,i+50+5);

            $scope.ctx.restore();            
        }
        
        //делим длинну на минуты
        step = length / 1440;
        var r,g,b;
        for(var key in $scope.timeline){
            
            r = Math.floor(Math.random()*255);
            g = Math.floor(Math.random()*255);
            b = Math.floor(Math.random()*255);
            
            var start = $scope.height - 50  - $scope.timeline[key].start*step;
            var end = $scope.height - 50  - $scope.timeline[key].end*step;
            
            $scope.ctx.save();
                $scope.ctx.beginPath();
                    $scope.ctx.lineWidth = 5;
                    $scope.ctx.strokeStyle = 'rgba('+r+','+g+','+b+',1)';
                    $scope.ctx.moveTo(100+(key*10*0),start);
                    $scope.ctx.lineTo(100+(key*10*0),end);

                    $scope.ctx.stroke();
                $scope.ctx.closePath();
                
                var length  = Math.sqrt( Math.pow(start-end,2) + Math.pow(0,2));
                
                //console.log(length);
                
                $scope.ctx.font="12px Georgia";
                var text = $scope.timeline[key].title;
                var y = ((start - end) / length ) + (length/2);
                $scope.ctx.fillText(text,100+10,end + y + 4);                
            $scope.ctx.restore();
        }
    }]);
</script>

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu'.DS.'analitics_top_menu') ?>
        
        <div class="content">
            <canvas style='width:500px; height:700px; outline: 1px solid red;' id='canvas'></canvas>
        </div>
    </div>
</div>