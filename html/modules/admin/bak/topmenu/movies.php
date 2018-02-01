<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Фильмы</div>
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
            
            <div data-toggle="modal" data-target="#addModal" class="button add_deal">+ НОВЫЙ ФИЛЬМ</div>
        </div>
    </div>
</div>

<script>
    app.controller('editModalCtrl', ['$scope','$http',function($scope,$http,$userinfo){
        $scope.$on('editData',function(e,ret){
            console.log('DATA');
            
            $('#editModal').modal('show');
            $scope.form = ret.data;
            console.log($scope.form);
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

<style>
    #editModal .modal-dialog{
        top:0px;
        width:1200px;
    }
</style>

<div ng-controller="editModalCtrl" class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать Фильм</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                       
                        <div class="col-sm-6">
                            <h3>Общие данные</h3>
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
                                        <label>Оригинальное Название</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="original_name" ng-model="form.original_name">
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
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Год производства</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.start_year">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Дата Релиза</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.release_date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Продолжительность</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.duration">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Позиция</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.position">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>IMDB</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.imdb">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Жанр</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group" ng-repeat="item in form.genres track by $index">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <SELECT ng-model="form.genres[$index]" class="form-control">
                                                        <option value="0">Жанры</option>
                                                        <?php foreach($this->genres_list as $item){ ?>
                                                            <option value="<?=$item->id?>"><?=$item->name?></option>
                                                        <?php } ?>
                                                    </SELECT>
                                                </div>

                                                <!--<div class="col-sm-4">
                                                    <div ng-click="moreGenres()">more</div>
                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <h3>Добавить Актеров</h3>
                            <div class="form-group" ng-repeat="item in form.actors track by $index">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.actors[$index].id">
                                            <option value="0">Актеры</option>
                                            <?php foreach($this->people_list as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div ng-if="form.actors.length-1 == $index" ng-click="addActor()">more</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <h3>Добавить Страны</h3>
                            <div class="form-group" ng-repeat="item in form.countries track by $index">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.countries[$index].id">
                                            <option value="0">Страны</option>
                                            <?php foreach($this->country_list as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name_ru?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div ng-if="form.countries.length-1 == $index" ng-click="addCountry()">more</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12">
                            <div class="row">
                                <h3>Добавить постер</h3>
                                <div class="col-sm-8">
                                    <div class="uploadFileBtn">Загрузить
                                        <iframe id="hiddenIframeUpload" src="/vod/addposter/"></iframe>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="poster_preview" style="width:200px; height: 100px; margin-bottom:20px;">
                                        <img src="" style="display:none;max-width:100px; max-height:100px;">
                                    </div>
                                    <input type="hidden" name="filename" value="">
                                </div>

                                <script>
                                    function addSuccess(filename,filepath){
                                        $('#addModal .poster_preview img').attr('src',filepath).css({'display':'block'});
                                        $('#addModal input[name=filename]').val(filename);
                                        //console.log('filename: ',$('#addModal input[name=filename]').val());
                                    }
                                    function addError(error){

                                    }
                                </script>
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
    app.controller('addModalCtrl', ['$scope','$http',function($scope,$http){
        $scope.form = {genres:["0"],type:"0",actors:["0"],countries:["0"]};
        
        $scope.submit = function(event){
            //console.log($scope.form);
            $scope.form.filename =   $('#addModal input[name=filename]').val();
            
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
        
        $scope.addActor = function(){
            $scope.form.actors.push("0");
        }
        $scope.addCountry = function(){
            $scope.form.countries.push("0");
        }
        
        $scope.moreGenres = function(){
            console.log($scope.form.genres.length);
            $scope.form.genres[$scope.form.genres.length] = "0";
            //$scope.form.genres[$scope.form.genres.length] = "0";
        }
    }]);
</script>

<style>
    #addModal .modal-dialog{
        top:0px;
        width:1200px;
    }
</style>

<div ng-controller="addModalCtrl" class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить VOD</strong></p></h4>
            </div>

            <div class="modal-body">
                <form action="" method="POST" ng-submit="submit($event)">
                    <div class="row">
                       
                        <div class="col-sm-6">
                            <h3>Общие данные</h3>
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
                                        <label>Оригинальное Название</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="original_name" ng-model="form.original_name">
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
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Год производства</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.start_year">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Дата Релиза</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.release_date">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Продолжительность</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.duration">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Позиция</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.position">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>IMDB</label>
                                    </div>
                                    
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" ng-model="form.imdb">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label>Жанр</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group" ng-repeat="item in form.genres track by $index">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <SELECT ng-model="form.genres[$index]" class="form-control">
                                                        <option value="0">Жанры</option>
                                                        <?php foreach($this->genres_list as $item){ ?>
                                                            <option value="<?=$item->id?>"><?=$item->name?></option>
                                                        <?php } ?>
                                                    </SELECT>
                                                </div>

                                                <!--<div class="col-sm-4">
                                                    <div ng-click="moreGenres()">more</div>
                                                </div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <h3>Добавить Актеров</h3>
                            <div class="form-group" ng-repeat="item in form.actors track by $index">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.actors[$index]">
                                            <option value="0">Актеры</option>
                                            <?php foreach($this->people_list as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div ng-if="form.actors.length-1 == $index" ng-click="addActor()">more</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-3">
                            <h3>Добавить Страны</h3>
                            <div class="form-group" ng-repeat="item in form.countries track by $index">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <select class="form-control" ng-model="form.countries[$index]">
                                            <option value="0">Страны</option>
                                            <?php foreach($this->country_list as $item){ ?>
                                                <option value="<?=$item->id?>"><?=$item->name_ru?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <div ng-if="form.countries.length-1 == $index" ng-click="addCountry()">more</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12">
                            <div class="row">
                                <h3>Добавить постер</h3>
                                <div class="col-sm-8">
                                    <div class="uploadFileBtn">Загрузить
                                        <iframe id="hiddenIframeUpload" src="/vod/addposter/"></iframe>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="poster_preview" style="width:200px; height: 100px; margin-bottom:20px;">
                                        <img src="" style="display:none;max-width:100px; max-height:100px;">
                                    </div>
                                    <input type="hidden" name="filename" value="">
                                </div>

                                <script>
                                    function addSuccess(filename,filepath){
                                        $('#addModal .poster_preview img').attr('src',filepath).css({'display':'block'});
                                        $('#addModal input[name=filename]').val(filename);
                                        //console.log('filename: ',$('#addModal input[name=filename]').val());
                                    }
                                    function addError(error){

                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                    <input value="Применить" class="btn btn-primary" style="margin-bottom:15px;" type="submit">
                </form>
            </div>
        </div>
    </div>
</div>
