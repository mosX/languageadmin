<script>
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http){        
        $scope.ws = document.getElementById("canvas");
        $scope.ctx = $scope.ws.getContext("2d");
        $scope.width = $scope.ws.width;
        $scope.height = $scope.ws.height;
        
        $scope.data = JSON.parse('<?=$this->m->json?>');
        $scope.total = <?=$this->m->total?>;
        $scope.blocks = <?=count($this->m->report)?>;
        
        $scope.diagram = {};
        
        $scope.diagram.x = 10;
        $scope.diagram.y = 10;
        $scope.diagram.width = $scope.width-10-10;
        $scope.diagram.height = $scope.height-10-10;
        
        //задний фон
        $scope.ctx.save();
            $scope.ctx.beginPath();
                $scope.ctx.fillStyle = '#ddd';
                $scope.ctx.rect(0, 0, $scope.width, $scope.height);
                $scope.ctx.fill();
            $scope.ctx.closePath();
        $scope.ctx.restore();
        
        //фон диаграмы
        $scope.ctx.save();
            $scope.ctx.beginPath();
                $scope.ctx.fillStyle = 'red';
                $scope.ctx.rect($scope.diagram.x, $scope.diagram.y, $scope.diagram.width, $scope.diagram.height);
                $scope.ctx.fill();
            $scope.ctx.closePath();
        $scope.ctx.restore();
        
        
        //расчитываем сколько ширины выделать на блоки
        $scope.diagram.element_width = $scope.diagram.width / $scope.blocks;
        //теперь расчитываем ширину блока ... 80% от того что есть
        $scope.diagram.block_width = $scope.diagram.element_width/100 * 80;

        var i=0;
        for(var key in $scope.data){
            var height = $scope.diagram.height/100 * $scope.data[key].percents;
            var x = ($scope.diagram.x + ($scope.diagram.element_width - $scope.diagram.block_width)/2) + $scope.diagram.element_width*i;
            var y = $scope.diagram.height - height + $scope.diagram.y;
            $scope.ctx.save();
                $scope.ctx.beginPath();
                    $scope.ctx.fillStyle = 'green';
                    $scope.ctx.rect(x, y, $scope.diagram.block_width, height);
                    $scope.ctx.fill();
                $scope.ctx.closePath();
            $scope.ctx.restore();
            i++;
        }
    }]);
</script>

<div ng-controller="pageCtrl">
    <div id="page_wrapper">
        <?= $this->m->module('topmenu'.DS.'analitics_top_menu') ?>
        
        <div class="content">
            <canvas style='width:500px; height:300px; outline: 1px solid red;' id='canvas'></canvas>
        </div>
    </div>
</div>