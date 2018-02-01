<script>
    angular.module("app").requires.push('dndLists');
    app.controller('pageCtrl', ['$scope','$http',function($scope,$http,$userinfo){
            $scope.list = JSON.parse('<?=$this->m->json?>');
            
            $scope.channels = [];
            $scope.deleted = [];
            $scope.selected = null;
            
            
            for(var key in $scope.list){
                if($scope.list[key].status == 1){
                    $scope.channels.push($scope.list[key]);
                }else{
                    $scope.deleted.push($scope.list[key]);
                }
            }
            
            
        /*console.log('START PAGE CONTROLLER');
        $scope.list = [{name:'first'},{name:'second'},{name:'third'},{name:'fourth'},{name:'fifth'}];
        $scope.selected = null;
        
        $scope.list2 = [{name:'111'},{name:'222'},{name:'333'},{name:'444'},{name:'555'}];
        */
       
       $scope.save = function(){
           console.log('SAVE');
           console.log($scope.channels);
           
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
               //object[key].number = parseInt(key)+1;
               //object[i].filename = $scope.channels[key].filename;
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
       }
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
        #trash_box{
            position:fixed;
            top:100px;
            right: 30px;
            height: 80%;
            overflow: auto;
            width:400px;
            
        }
        #deleted_list{                        
            height:100%;
            float:right;
            width:100%;
            margin-left:0px;
            padding-left:0px;
        }
        #deleted_list li{
            padding-top:10px;
            padding-left:10px;
            height: 60px;
            background: white;
            border-bottom:1px solid #ddd;
        }
        #deleted_list li:last-child{
            border:none;
        }
        .logo_block{
            display:inline-block;
            vertical-align: middle;
            min-width:100px;
            text-align: center;
        }
    </style>

    <div class="content">
        <h3>Активные</h3>
        <ul dnd-list="channels" id="channels_list">
            <li ng-repeat="item in channels"
                dnd-draggable="item"
                dnd-moved="channels.splice($index, 1)"
                dnd-selected="selected = item"
                dnd-effect-allowed="move"                
                >
                {{$index+1}} <div class="logo_block"><img src="<?=$this->m->config->assets_url?>/{{item.filename}}"></div> {{item.name}}
            </li>
        </ul>
        <div id="trash_box">
            <h3>Удаленные</h3>
            <ul dnd-list="deleted" id="deleted_list">
                <li ng-repeat="item in deleted"
                    dnd-draggable="item"
                    dnd-moved="deleted.splice($index, 1)"
                    dnd-selected="selected = item"
                    dnd-effect-allowed="move"
                    >
                    {{$index+1}} <div ng-show="item.filename" class="logo_block"><img src="<?=$this->m->config->assets_url?>/{{item.filename}}"></div> {{item.name}}
                </li>               
            </ul>
        </div>
        <div class="clearfix"></div>
        <button ng-click="save()" class="btn btn-primary">Сохранить порядок</button>
      
        <!--<ul dnd-list="list">
            <li ng-repeat="item in list"
                dnd-draggable="item"
                dnd-moved="list.splice($index, 1)"
                dnd-selected="selected = item"
                dnd-effect-allowed="move"                
                >{{item.name}}</li>
        </ul>
        
        <ul dnd-list="list2">
            <li ng-repeat="item in list2"
                dnd-draggable="item"
                dnd-moved="list2.splice($index, 1)"
                dnd-selected="selected = item"
                dnd-effect-allowed="move"                
                >{{item.name}}</li>
        </ul>
        
        <ul dnd-list="[]">
            <li>Trash</li>
                
        </ul>-->        
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
