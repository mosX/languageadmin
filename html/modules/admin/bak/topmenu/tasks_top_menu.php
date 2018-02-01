<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">        
            <div class="buttons_wrapper">
                <a href="/tasks/pipe/" class="svg_pipe <?=$this->_action =='pipe' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
                <a href="/tasks/" class="svg_list <?=$this->_action == 'index' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--list"></use></svg>
                </a>
            </div>
        </div>
        <style>
            .filter_overlay{
                display:none;   
                background: transparent;

                position:fixed;
                left:0px;
                top:0px;
                width:100%;
                height:100%;
                z-index:1;
            }
        </style>
        <style>
            #top_menu .tabs{
                border-left:1px solid #e8eaeb;
                padding:0 15px;
                display:flex;
                align-items: center;
                order: 2;
            }
            #top_menu .tabs a{
                text-decoration: none;
                font-weight:bolder;
                margin-right:15px;
                color: #bcc0c7;
            }
            #top_menu .tabs a:last-child{
                margin-right:0px;
            }
            #top_menu .tabs a.active,#top_menu .tabs a:hover{
                color: #000;
            }
        </style>

        <div class="filter_overlay"></div>
        <div class="tabs">
            <a href="/tasks/daily" class="<?=$this->_action == 'daily' ? 'active':''?>">ДЕНЬ</a>
            <a href="/tasks/weekly/" class="<?=$this->_action == 'weekly' ? 'active':''?>">НЕДЕЛЯ</a>
            <a href="/tasks/monthly" class="<?=$this->_action == 'monthly' ? 'active':''?>">МЕСЯЦ</a>
        </div>

        <div class="search_wrapper">

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
                            <form>
                                <div class="form-group">
                                    <input type="text" class="datepicker form-control" value="date">
                                </div>
                                <div class="form-group">
                                    <select class="form-control">
                                        <option>Этапы</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control">
                                        <option>Менеджеры</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control">
                                        <option>Задачи: Просрочены</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control">
                                        <option>Теги</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Принять">
                                    <input type="button" class="btn btn-secondary" value="Сбросить">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input_block">
                <input type="text" id="search_input" placeholder="Поиск и фильтр">
            </div>
            <label for="search_input">
                <svg class="svg_search_icon"><use xlink:href="#common--filter-search"></use></svg>
            </label>
        </div>
        <div class="actions">
            <a class="svg_settings">
                <svg><use xlink:href="#common--settings"></use></svg>
            </a>
            <a class="svg_controls">
                <svg><use xlink:href="#controls--button-more"></use></svg>
            </a>
            <a class="button add_deal" data-toggle="modal" data-target="#taskModal">+ НОВАЯ ЗАДАЧА</a>
        </div>
    </div>
</div>
    <script>
        app.controller('taskModalCtrl', ['$scope','$http',function($scope,$http){
                //$('#taskModal').modal('show');
                $scope.form = {};
                $scope.form.login = '';
                $scope.form.time = '0';

                $scope.checkLogins = function(){
                    if($scope.form.login.length > 1){                    
                        $http({
                            url:'/tasks/checklogin/?value='+$scope.form.login,
                        }).then(function(ret){                        
                            $scope.options = ret.data;
                        });
                    }
                }

                $scope.submit = function(event){                
                    $http({
                        url:'/tasks/add/',
                        method:'POST',
                        data:$scope.form
                    }).then(function(ret){
                        console.log(ret.data);
                        if(ret.data.status == 'success'){
                            location.href = location.href;
                        }
                    });

                    event.preventDefault();
                }
        }]);
    </script>
    <style>
        .options_block{
            position:relative;
        }
        #login_options{
            position:absolute;
            background: white;
            border: 1px solid #ddd;
            width: 250px;
            min-height: 100px;
            max-height: 150px;
            overflow: auto;
            top:33px;        
        }
        #login_options ul{
            margin:0px;
            padding:0px;
        }
        #login_options ul li{
            cursor:pointer;
            padding-left:20px;
        }
        #login_options ul li:hover{
            background:#f4f7fd;
        }
    </style>
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-hidden="true" ng-controller="taskModalCtrl">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить задачу</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action='' method='POST' ng-submit="submit($event)">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class='row'>
                                <div class='col-sm-8'>
                                    <input name='date' ng-model="form.date" type='text' placeholder='Выберите дату' class='datepicker form-control'>
                                </div>
                                <div class='col-sm-4'>
                                    <?php $time = strtotime(date('Y-m-d',time())) ?>
                                    <select class='form-control' name='time' ng-model="form.time">
                                        <?php for($i=0;$i < 86400;$i+=1800){ ?>
                                            <option value='<?=$i?>'><?=date("H:i",$time+$i)?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="options_block">
                                <input ng-keyup="checkLogins()" ng-model="form.login" class="form-control" type="text" name="login" placeholder="user">
                                <div id="login_options" ng-show="options && options.length > 1">
                                    <ul>
                                        <li ng-repeat="item in options">{{item.login}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea class="form-control" name='comment' ng-model="form.comment" placeholder="Добавить комментарий"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type='hidden' name='id' value=''>
                        <input type="submit" class="btn btn-secondary" value='Сохранить'>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
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