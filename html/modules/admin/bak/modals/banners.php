<script>
    app.controller('editBannerModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.success = 'reload';
        
        $scope.editFormData = function(id){
            $http({
                //url:'/collections/banner_data/?id='+id,
                url:'/banners/banner_data/?id='+id,
                method:'GET',
            }).then(function(ret){
                /*$scope.$broadcast('editData', {
                    data: ret.data
                });                */
                $scope.form = {};
                $scope.form.id = ret.data.id;
                $scope.form.name = ret.data.name;
                $scope.form.url = ret.data.url;
                $scope.form.filename = ret.data.filename;
                $scope.form.collections = ret.data.collections;
                $scope.form.resizes = ret.data.resizes;

                //console.log('!!!!!!',ret.data.resizes[1].filename,ret.data.resizes[2].filename,ret.data.resizes[3].filename);


                $scope.getCollections();

                $('#editModal #banner_id_input').val($scope.form.banner_id);
                $('#editBannerModal').modal('show');
            });
            //event.preventDefault();
        }
        
        $scope.getCollections = function(callback){
            $http({
                url:'/banners/getcollectionlist/',
                type:'GET'
            }).then(function(ret){
                $scope.collections = ret.data;
            });
        };
        
        $rootScope.$on('showEditBannerModal',function(e,id){
            console.log('showEditBannermodal');
            
            $scope.editFormData(id);
            
            $scope.success = 'event';
        });
            
        /*$scope.$on('editData',function(e,ret){
            $('#editModal').modal('show');
            //$scope.form = ret.data;
            $scope.form = {};
            $scope.form.id = ret.data.id;
            $scope.form.name = ret.data.name;
            $scope.form.url = ret.data.url;
            $scope.form.filename = ret.data.filename;
            $scope.form.collections = ret.data.collections;
            $scope.form.resizes = ret.data.resizes;
            
            //console.log('!!!!!!',ret.data.resizes[1].filename,ret.data.resizes[2].filename,ret.data.resizes[3].filename);
            
            
            $scope.getCollectionList();
            
            $('#editModal #banner_id_input').val($scope.form.banner_id);
            
            console.log($scope.form);
        });*/
        
        $scope.addCollection = function(){
            //console.log($scope.form.collections.length);
            $scope.form.collections.push({collection_id: "0" , position:0});
        }
        
        $scope.submit = function(event){
            $scope.form.banner_id = $('#editBannerModal #banner_id_input').val();
            $scope.form.resizes = {};
            $('#editBannerModal .resize_type').each(function(){
                $scope.form.resizes[$(this).attr('data-type')] = {};
                $scope.form.resizes[$(this).attr('data-type')].id = $('input[name=id]',this).val();
                $scope.form.resizes[$(this).attr('data-type')].filename = $('input[name=filename]',this).val();
                $scope.form.resizes[$(this).attr('data-type')].type = $(this).attr('data-type');
            });
            
            $http({
                //url:location.href,
                url:'/banners/',
                method:'POST',
                data:$scope.form
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

<style>
    #editBannerModal .preview{
        border: 1px solid #ddd;
        min-height: 100px;
    }
    
    #editBannerModal .preview img{
        max-width:100%;
        max-height:100px;
    }
    
    #editBannerModal .modal-dialog{
        width:800px;
    }
</style>

<div ng-controller="editBannerModalCtrl" class="modal fade" id="editBannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Баннер</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                     <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <input type="" class="form-control" name="name" value="" ng-model="form.name" >
                                        <div style="color:#666; font-size: 12px;">Идентификатор баннера. Для внутреннего пользования. </div>
                                    </div>
                                </div>                                
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Link</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <input type="" class="form-control" name="url" value="" ng-model="form.url" >
                                    </div>
                                </div>                                
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Коллекции</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class='form-group' ng-repeat="item in form.collections track by $index">
                                            <div class="form-group">
                                                <div class='row'>
                                                    <div class='col-sm-8'>
                                                        <select class='form-control' ng-model='form.collections[$index].collection_id'>
                                                            <option ng-repeat="item2 in collections" value="{{item2.id}}">{{item2.name}}</option>
                                                        </select>
                                                    </div>
                                                    <div class='col-sm-4'>
                                                        <div ng-if="form.collections.length-2 < $index " ng-click='addCollection()'>more</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div ng-if="form.collections[$index].collection_id > 0" class="col-sm-8">
                                                        <input type="text" class="form-control" placeholder="Позиция" ng-model="form.collections[$index].position">
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group resize_type" data-type="1">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Размер 1</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/editbanner/?type=1"></iframe>
                                        </div>
                                        <input type="hidden" name="filename" value="{{form.resizes[1].filename}}">
                                        <input type="hidden" name="id" value="{{form.resizes[1].id}}">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:block" src="{{'<?=$this->config->assets_url?>/banners/'+form.resizes[1].filename}}">
                                        </div>
                                    </div>                                    
                                </div>                                
                            </div>
                            
                            <div class="form-group resize_type" data-type="2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Размер 2</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/editbanner/?type=2"></iframe>
                                        </div>
                                        <input type="hidden" name="filename" value="{{form.resizes[2].filename}}">
                                        <input type="hidden" name="id" value="{{form.resizes[2].id}}">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:block" src="{{'<?=$this->config->assets_url?>/banners/'+form.resizes[2].filename}}">
                                        </div>
                                    </div>                                    
                                </div>                                
                            </div>
                            
                            <div class="form-group resize_type" data-type="3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Размер 3</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/editbanner/?type=3"></iframe>
                                        </div>
                                        
                                        <input type="hidden" name="filename" value="{{form.resizes[3].filename}}">
                                        <input type="hidden" name="id" value="{{form.resizes[3].id}}">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:block" src="{{'<?=$this->config->assets_url?>/banners/'+form.resizes[3].filename}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function editSuccess(filepath,filename,type){
                            
                            console.log(filepath,filename,type);
                            $('#editBannerModal .resize_type[data-type='+type+'] .preview img').attr('src',filepath).css({'display':'block'});
                            $('#editBannerModal .resize_type[data-type='+type+'] input[name=filename]').val(filename);
                        }
                        
                        function editError(error){
                            console.log('Editting error');
                        }
                    </script>
                   
                    <input type="hidden" name="banner_id" value="0" id="banner_id_input">
                    <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('addBannerModalCtrl', ['$scope','$rootScope','$http',function($scope,$rootScope,$http){
        $scope.form = {collections:[0],positions:[]};        
        $scope.success = 'reload';
        
        $rootScope.$on('showAddBannerModal',function(e){
            $('#addBannerModal').modal('show');
            $scope.success = 'event';
        });

        $scope.submit = function(event){
            $scope.form.id = $('#banner_id_input').val();
            $scope.form.resizes = {};
            
            $('#addBannerModal .resize_type').each(function(){
                //console.log($(this).attr('data-type'));
                $scope.form.resizes[$(this).attr('data-type')] = $('input[name=filename]',this).val();
            });
            console.log($scope.form.resizes);
            
            //console.log($scope.form);
            $http({
                url:'/banners/banner_edit/',
                method:'POST',
                data:$scope.form
            }).then(function(ret){
                //console.log(ret.data);
                if(ret.data.status == 'success'){
                    if($scope.success == 'reload'){
                        location.href = location.href;
                    }else if($scope.success == 'event'){
                        $('#addBannerModal').modal('hide');
                        console.log('send SUCCESS');
                        console.log({id:ret.data.id,filename:ret.data.filename});
                        $rootScope.$emit('addBannerSuccess',{id:ret.data.id,filename:ret.data.filename});
                    }
                }else{
                    console.log('error',ret.data.message);
                }
            });

            event.preventDefault();
        }
        
        $scope.addCollection = function(){
            console.log('add Collection');
            //$scope.form.collections.push($scope.form.collections.length);
            $scope.form.collections[$scope.form.collections.length] = "0";
            console.log($scope.form.collections.length);
        }
    }]);
</script>

<style>    
    #addBannerModal .preview{
        border: 1px solid #ddd;
        min-height: 100px;
    }
    
    #addBannerModal .preview img{
        max-width:100%;
        max-height:100px;
    }
    #addBannerModal .modal-dialog{
        width:800px;
    }
    
</style>

<div ng-controller="addBannerModalCtrl" class="modal fade" id="addBannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Баннер</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <input type="" class="form-control" name="name" value="" ng-model="form.name" >
                                        <div style="color:#666; font-size: 12px;">Идентификатор баннера. Для внутреннего пользования. </div>
                                    </div>
                                </div>                                
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Link</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <input type="" class="form-control" name="url" value="" ng-model="form.url" >
                                    </div>
                                </div>                                
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Коллекции</label>
                                    </div>

                                    <div class="col-sm-8">
                                        <div class='form-group' ng-repeat="item in form.collections track by $index">
                                            <div class="form-group">
                                                <div class='row'>
                                                    <div class='col-sm-8'>
                                                        <select class='form-control' ng-model='form.collections[$index]' ng-change="">
                                                            <option ng-repeat="item2 in collections" value="{{item2.id}}">{{item2.name}}</option>
                                                        </select>
                                                    </div>
                                                    <div class='col-sm-4'>
                                                        <div ng-if="form.collections.length-2 < $index " ng-click='addCollection()'>more</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div ng-if="form.collections[$index] > 0" class="col-sm-8">
                                                        <input type="text" class="form-control" placeholder="Позиция {{form.collections[$index]}}" ng-model="form.positions[$index]">
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        
                        <div class="col-sm-6">
                            <div class="form-group resize_type" data-type="1">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Размер 1</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/addbanner/?type=1"></iframe>
                                        </div>
                                        <input type="hidden" name="filename" value="">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:none" src="">
                                        </div>
                                    </div>                                    
                                </div>                                
                            </div>
                            
                            <div class="form-group resize_type" data-type="2">
                                <div class="row">
                                    <div class="col-sm-6">
                                       <label>Размер 2</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/addbanner/?type=2"></iframe>
                                        </div>
                                        <input type="hidden" name="filename" value="">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:none" src="">
                                        </div>
                                    </div>                                    
                                </div>                                
                            </div>
                            
                            <div class="form-group resize_type" data-type="3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Размер 3</label>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="uploadFileBtn">Добавить
                                            <iframe id="hiddenIframeUpload" src="/banners/addbanner/?type=3"></iframe>
                                        </div>
                                        <input type="hidden" name="filename" value="">
                                    </div>
                                    
                                    <div class="col-sm-12">
                                        <div class="preview">
                                            <img style="display:none" src="">
                                        </div>
                                    </div>                                    
                                </div>                                
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="banner_id" value="0" id="banner_id_input">
                    <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                    
                    <script>
                        function addSuccess(filepath,filename,type){
                            $('#addBannerModal .resize_type[data-type='+type+'] .preview img').attr('src',filepath).css({'display':'block'});
                            $('#addBannerModal .resize_type[data-type='+type+'] input[name=filename]').val(filename);
                        }
                        function addError(error){
                            console.log('ERROR');
                        }
                    </script>
                   
                    <input type="hidden" name="id" value="" ng-model="logo_id">
                </form>
            </div>
        </div>
    </div>
</div>