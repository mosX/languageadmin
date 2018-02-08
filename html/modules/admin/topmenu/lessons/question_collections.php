<div id="top_menu">
    <?=$this->module('header')?>
    <div class="page_menu">
        <div class="preset">
            <div class='title_button'>Коллекция Вопросов</div>
            <div class="buttons_wrapper">
                <a href="/channels/sequence/" class="svg_pipe <?=$this->_action == 'sequence' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--pipe"></use></svg>
                </a>
                <a href="/channels/" class="svg_list <?=$this->_action == 'index' ? 'active':''?>">
                    <svg style="width:14px; height:16px;" ><use xlink:href="#common--list"></use></svg>
                </a>
            </div>
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
            <a data-toggle="modal" data-target="#addQuestionModal" class="button add_deal">+ Добавить Вопрос</a>
        </div>
    </div>
</div>

<?=$this->module('questions')?>