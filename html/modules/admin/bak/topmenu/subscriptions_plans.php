<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>ТАРИФНЫЕ ПЛАНЫ</div>
        </div>

        <div class="filter_overlay"></div>

        <form action='' class="search_wrapper">
            <script>
                $('document').ready(function(){
                    $('#search_input').click(function(){
                        $('#top_menu .filter_wrapper').css({'display':'flex'});
                        $('.filter_overlay').css({'display':'block'});
                    });

                    $('.filter_overlay').click(function(){
                        $('.filter_wrapper').css({'display':'none'});
                        $('.filter_overlay').css({'display':'none'});
                    });
                });
            </script>

            <div class="filter_wrapper">
                <div class="sidebar">
                    <ul>
                        <li><a href="">Полный список</a></li>
                        <li><a href="">Контакты без задач</a></li>
                        <li><a href="">Контакты с просроченным</a></li>
                        <li><a href="">Без сделок</a></li>
                        <li><a href="">Удаленные</a></li>
                    </ul>
                </div>
                <div class="filter">
                    <div class="row">
                        <div class="col-sm-4">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="text" placeholder="Название" name="name" value="<?=$_GET['name']?>" class="form-control">        
                                    </div>
                                </div>                            
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Добавлено</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="С" name="date_from" value="<?=$_GET['date_from']?>" class="datepicker form-control">        
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="По" name="date_to" value="<?=$_GET['date_to']?>" class="datepicker form-control">        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Принять">
                                <input type="button" class="btn btn-secondary reset_filter" value="Сбросить">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $('document').ready(function(){
                    $( ".datepicker" ).datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        startDate:'01-01-1996',
                        firstDay: 1
                    });
                });
            </script>
            <div class='filter_inner'>
                <div class="input_block">
                    <input type="text" id="search_input" placeholder="Поиск и фильтр" name='search' value='<?=$_GET['search']?>'>
                </div>
                <label for="search_input" class='options_cnt'>2 опции</label>
                <label for="search_input" class='ico'>
                    <svg class="svg_search_icon"><use xlink:href="#common--filter-search"></use></svg>
                </label>
            </div>        
        </form>

        <div class="actions">
            <div data-toggle="modal" data-target="#addPlanModal" class="button add_deal">+ НОВЫЙ ПЛАН</div>
        </div>
    </div>
</div>

<script>
    app.controller('addPlanModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){        
        $scope.form = {};

        $scope.submit = function(event){
            $http({
                url:location.href,
                method:'POST',
                data:$scope.form
            }).then(function(ret){                
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    console.log('error',ret.data.message);
                }                    
            });

            event.preventDefault();
        }
    }]);
</script>

<style>
    #addPlanModal .preview img{
        max-width:100px;
        max-height:100px;
    }
</style>

<div ng-controller="addPlanModalCtrl" class="modal fade" id="addPlanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Тарифный План</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Название</label>
                                    </div>
                                    
                                    <div class="col-sm-8">                                        
                                        <input type="text" class="form-control" name="name" ng-model="form.name">                                        
                                    </div>
                                </div>                                
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Цена</label>
                                    </div>
                                    
                                    <div class="col-sm-8">                                        
                                        <input type="text" class="form-control" name="price" ng-model="form.price">
                                    </div>
                                </div>                                
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Описание</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <textarea class="form-control" ng-model="form.description"></textarea>
                                    </div>
                                </div>                                
                            </div>
                            
                        </div>
                    </div>
                    <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('plansEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.$on('editData',function(e,ret){
            $('#plansEditModal').modal('show');
            ret.data.price /= 100;
            $scope.form = ret.data;
        });
        
        $scope.submit = function(event){
            $http({
                url:location.href,                
                method:"POST",                
                data:$scope.form,
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    console.log('error');
                }
            });
            event.preventDefault();
        }
    }]);
</script>

<div ng-controller="plansEditModalCtrl" class="modal fade" id="plansEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Тарифный План</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="form-group">
                        <div class="row">                            
                            <div class="col-sm-4">
                                <label>Название</label>
                            </div>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" ng-model="form.name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">                            
                            <div class="col-sm-4">
                                <label>Цена</label>
                            </div>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" ng-model="form.price">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">                            
                            <div class="col-sm-4">
                                <label>Описание</label>
                            </div>
                            <div class="col-sm-8">
                                <!--<input class="form-control" type="text" ng-model="form.description">-->
                                <textarea class="form-control" ng-model="form.description"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="id" value="" ng-model="form.id">
                    <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>