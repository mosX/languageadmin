<div id="top_menu">
    <?= $this->module('header') ?>

    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Список Предметы</div>
            <div class="buttons_wrapper">
                <a href="" class="svg_pipe">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
            </div>
        </div>

        <div class="filter_overlay"></div>

        <form action='' class="search_wrapper">
            <script>
                $('document').ready(function () {
                    $('#search_input').click(function () {
                        $('#top_menu .filter_wrapper').css({'display': 'flex'});
                        $('.filter_overlay').css({'display': 'block'});
                    });

                    $('.filter_overlay').click(function () {
                        $('.filter_wrapper').css({'display': 'none'});
                        $('.filter_overlay').css({'display': 'none'});
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
                                <input type="text" class="datepicker form-control" value="date">
                            </div>

                            <div class="form-group">
                                <input type="text" placeholder="Тэги" name="tag" value="<?= $_GET['tag'] ?>" class="form-control">
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
                $(".datepicker").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    startDate: '01-01-1996',
                    firstDay: 1
                });
            </script>
            <div class='filter_inner'>
                <div class="input_block">
                    <input type="text" id="search_input" placeholder="Поиск и фильтр" name='search' value='<?= $_GET['search'] ?>'>
                </div>
                <label for="search_input" class='options_cnt'>2 опции</label>
                <label for="search_input" class='ico'>
                    <svg class="svg_search_icon"><use xlink:href="#common--filter-search"></use></svg>
                </label>
            </div>
        </form>

        <style>
            #top_menu .filter_inner{
                display:flex;
                align-items: center;
                width:100%;
            }
            #top_menu .filter_inner label.ico{
                width:20px;
                order:2;
                margin-right:5px;
            }
            #top_menu .options_cnt{
                display:none;
                color:#595d64;
                background: #dfebf8;
                border : 1px solid #9dc1e7;
                border-radius: 2px;
                padding:0px 5px;
                margin-right:15px;
                order: 1;
                cursor:pointer;
            }

            #top_menu .filter_inner .input_block{
                flex:1;
                order:3;            
            }
            #top_menu .filter_inner .svg_search_icon{
                left:0px;
                top:0px;
                vertical-align: middle;

                position:relative;
            }
        </style>

        <div class="actions">          
            <a href="" data-toggle="modal" data-target="#addModal" class="button add_deal">+ ДОБАВИТЬ ПРЕДМЕТ</a>
        </div>
    </div>
</div>

<script>
    app.controller('addModalCtrl', ['$scope', '$http', function ($scope, $http) {
            $scope.form = {};

            $scope.submit = function (event) {
                $http({
                    method: 'POST',
                    url: location.href,
                    data: $scope.form
                }).then(function (ret) {
                    console.log(ret.data);
                    if (ret.data.status == 'success') {
                        location.href = location.href;
                    } else {
                        console.log('ERROR');
                    }
                });

                event.preventDefault();
            }
        }]);
</script>

<div ng-controller="addModalCtrl" class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Добавить предмет</strong></p></h4>
            </div>

            <div class="modal-body">
                <form class="form" action="" method="POST" ng-submit="submit($event)">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Название</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.name">
                                <div class="error name_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" class="btn btn-primary" value="Сохранить">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    app.controller('editModalCtrl', ['$scope', '$http', function ($scope, $http) {
            $scope.form = {};
            
            $scope.$on('editData', function (event, ret){
                console.log(ret.data); // Данные, которые нам прислали

                $scope.form  = ret.data;
            });

            $scope.submit = function (event) {
                $http({
                    method: 'POST',
                    url: location.href,
                    data: $scope.form
                }).then(function (ret) {
                    console.log(ret.data);
                    if (ret.data.status == 'success') {
                        location.href = location.href;
                    } else {
                        console.log('ERROR');
                    }
                });

                event.preventDefault();
            }
        }]);
</script>

<div ng-controller="editModalCtrl" class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title font-header"><p><strong>Редактировать предмет</strong></p></h4>
            </div>

            <div class="modal-body">
                <form class="form" action="" method="POST" ng-submit="submit($event)">
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Название</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="" ng-model="form.name">
                                <div class="error name_error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="submit" class="btn btn-primary" value="Сохранить">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('deleteModalCtrl', ['$scope', '$http', function ($scope, $http) {
        $scope.id = '';

        $scope.$on('delete', function (event, id){
            console.log(id); // Данные, которые нам прислали
            $scope.id = id;
            $('#deleteModal').modal('show');
        });
        
        $scope.submit = function(event){
            
            $http({
                url:'/tasktable/delete_lesson/?id='+$scope.id,
                method:'GET'
            }).then(function(ret){
                if(ret.data.status == 'success'){
                    location.href = location.href;
                }else{
                    
                }
            });
            event.preventDefault();
        }
    }]);
</script>
<div class="modal fade" ng-controller="deleteModalCtrl" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Удалить контакт</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action='' method='POST' ng-submit="submit($event)">
                <div class="modal-body">
                    <p>Вы действительно хотите удалить предмет?</p>

                    <p>Все данные, как-либо связанные с ним, будут удалены. Восстановить удалённые данные будет невозможно.</p>
                </div>
                <div class="modal-footer">
                    <input type='hidden' name='id' value=''>
                    <input type="submit" class="btn btn-secondary" value='Подтвердить'>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>