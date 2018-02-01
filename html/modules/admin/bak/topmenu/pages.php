<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>СТРАНИЦЫ</div>
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
            <a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>
            <!--<a data-toggle="modal" data-target="#addLogoModal" class="button add_deal">+ НОВЫЙ ЛОГОТИП</a>-->
            
            <!--<div data-toggle="modal" data-target="#addLogoModal" class="uploadFileBtn button add_deal">+ НОВЫЙ БАННЕР
                <iframe id="hiddenIframeUpload" src="{{'/cms/addbanner/'}}"></iframe>
            </div>-->
            
            <div data-toggle="modal" data-target="#addPageModal" class="button add_deal">+ НОВАЯ СТРАНИЦА</div>
        </div>
    </div>
</div>

<script>
    app.controller('pagesEditModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.$on('editData',function(e,ret){            
            $('#pagesEditModal').modal('show');
            $scope.form = ret.data;
        })
        
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

<div ng-controller="pagesEditModalCtrl" class="modal fade" id="pagesEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Изменить Страницу</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
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
                                <label>Описание</label>
                            </div>

                            <div class="col-sm-8">
                                <textarea ng-model="form.description" class="form-control"></textarea>
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

<script>
    app.controller('addPageModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){        
        $scope.form = {};
        $scope.form.type = "";

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
    #addBannerModal .preview img{
        max-width:100px;
        max-height:100px;
    }
</style>

<div ng-controller="addPageModalCtrl" class="modal fade" id="addPageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить Страницу</strong></p></h4>
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
                                        <label>Описание</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <textarea ng-model="form.description" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Шаблон</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <SELECT ng-model="form.type" class="form-control">
                                            <option value="">Список предопределенных шаблонов</option>
                                            <option value="1">Шаблон1</option>
                                            <option value="2">Шаблон2</option>
                                            <option value="3">Шаблон3</option>
                                            <option value="4">Шаблон4</option>
                                            <option value="5">Шаблон5</option>
                                        </SELECT>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if="form.type == 2">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Коллекция Баннеров</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.banner_collection">
                                            <?php foreach($this->collections[1] as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group" ng-if="form.type == 2 || form.type == 3">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Коллекция Фильмов</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.channel_collection">
                                            <?php foreach($this->collections[2] as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
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
