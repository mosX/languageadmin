<script>
    angular.module("app").requires.push('dndLists');
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.channels = JSON.parse('<?=$this->m->channels?>');
        $scope.personal = JSON.parse('<?=$this->m->personal?>');
        
        $scope.user_id = <?=(int)$this->_path[2]?>;
        
        //$scope.user_id = 8;
        $scope.deleted = [];
        
        $scope.save = function(){
            console.log($scope.deleted);
            var object = [] ;
            for(var key in $scope.personal){
               object.push($scope.personal[key].id);
            }
            console.log(object);
            $http({
               url:'/channels/hide/',
               method:'POST',
               data:{user_id:$scope.user_id , channels:object}
            }).then(function(ret){
               console.log(ret.data);
            });
        }

        /*$scope.channels = [];
        $scope.deleted = [];
        $scope.selected = null;
        

        for(var key in $scope.list){
            if($scope.list[key].status == 1){
                $scope.channels.push($scope.list[key]);
            }else{
                $scope.deleted.push($scope.list[key]);
            }
        }
        
       $scope.save = function(){
           var object = {};
           var i = 0;
           for(var key in $scope.channels){
               object[i] = {};
               
               object[i].number = parseInt(key)+1;
               object[i].status = 1;
               object[i].id = $scope.channels[key].id;
               i++;
           }
           
           for(var key in $scope.deleted){
               object[i] = {};               
               object[i].status = 0;
               object[i].id = $scope.deleted[key].id;
               object[i].name = $scope.deleted[key].name;
               i++;
           }
           
           $http({
               url:'/channels/sequence',
               method:'POST',
               data:object
           }).then(function(ret){
               console.log(ret.data);
           });
       }*/
    }]);
</script>
<div id="page_wrapper" ng-controller="pageCtrl">
    <?= $this->m->module('topmenu/channels_top_menu') ?>
    <style>
        #channels_list{
            display:inline-block;
            vertical-align: top;
            width:400px;
        }
        #channels_list li{            
            padding-top:10px;
            padding-left:10px;
            height: 60px;
            background: white;
            border-bottom:1px solid #ddd;
            
        }
        #channels_list[dnd-list] .dndDraggingSource {   /*елемент который перетягиваем*/
            display: none;
        }
        #channels_list[dnd-list] .dndPlaceholder {      /*пустое место**/
            background-color: #ddd;
            display: block;
            min-height: 42px;
        }
        #active_channels_box{
            position:fixed;
            top:100px;
            right: 30px;
            height: 80%;
            overflow: auto;
            width:400px;
            
        }
        #active_channels_box ul{                        
            height:100%;
            float:right;
            width:100%;
            margin-left:0px;
            padding-left:0px;
        }
        #active_channels_box ul li{
            padding-top:10px;
            padding-left:10px;
            height: 60px;
            background: white;
            border-bottom:1px solid #ddd;
        }
        #active_channels_box ul li:last-child{
            border:none;
        }
        .logo_block{
            display:inline-block;
            vertical-align: middle;
            min-width:100px;
            text-align: center;
        }
        
        #delete_block{
            width:150px;
            height: 150px;
            background: orange;
            position:fixed;
            left: 700px;
            top:100px;            
        }
        #delete_block ul{
            height: 100%;
            background: url('/html/images/trashcan.png');
            background-size:cover;
        }
        
    </style>

    <div class="content">
        <ul dnd-list="personal" id="channels_list">
            <li ng-repeat="item in personal"
                dnd-draggable="item"
                dnd-moved="personal.splice($index, 1)"
                dnd-selected="selected = item"
                dnd-effect-allowed="move"                
                >
                {{$index+1}} <div class="logo_block"><img src="<?=$this->m->config->assets_url?>/{{item.filename}}"></div> {{item.name}}
            </li>
        </ul>
        
        <div id="delete_block">
            <ul dnd-list="deleted"></ul>
        </div>
        
        <div id="active_channels_box">
            <ul dnd-list="deleted" id="deleted_list">
                <li ng-repeat="item in channels"
                    dnd-draggable="item"
                    dnd-selected="selected = item"
                    dnd-effect-allowed="move"                
                    >
                    {{$index+1}} <div ng-show="item.filename" class="logo_block"><img src="<?=$this->m->config->assets_url?>/{{item.filename}}"></div> {{item.name}}
                </li>               
            </ul>
        </div>
        <div class="clearfix"></div>
        <button ng-click="save()" class="btn btn-primary">Сохранить порядок</button>    
    </div>
</div>

<script>
    $( ".datepicker" ).datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        startDate:'01-01-1996',
        firstDay: 1
    });
</script>
