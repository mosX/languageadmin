<!DOCTYPE html>
<html dir="ltr" lang="en">
    <head>
        <?= $this->header() ?>
        <?= $this->css() ?>
        <?= $this->js() ?>        
    </head>

    <body ng-app="app">
        <script>
            app.directive('scroller',function(){
                var speed = 100;

                var updateScroller = function(scope){
                    var block_height = $(scope.inner).height();
                    var scroller_height = $(scope.scroll).height();

                    var coef = block_height / scroller_height;

                    $('.scroller_inner',scope.scroll).css({'height':(scroller_height/coef)+'px'});

                    //определяем позицию топ
                    var top = parseInt($(scope.inner).css('top'))*-1;

                    $('.scroller_inner',scope.scroll).css({'top':(top/coef)+'px'});
                };

                var mouseSlideEvents = function(scope){
                    scope.move = false;
                    scope.current_y = null;

                    $(scope.parent).mousedown(function(event){                            
                        scope.move = true;
                        scope.current_y = event.pageY;
                    });

                    $('body').mouseup(function(){
                        scope.move = false;
                    });
                    console.log($(scope.inner));
                    $(scope.parent).mouseup(function(event){
                        scope.move = false;
                        event.preventDefault();
                    });

                    $(scope.inner).mousemove(function(event){
                        if(!scope.move) return;

                        scope.move_y = event.pageY;

                        if(scope.move_y == scope.current_y) return;

                        var diff = Math.abs(scope.move_y - scope.current_y);
                        var position = parseInt($(scope.inner).css('top'));

                        if(scope.move_y > scope.current_y){
                            var size = $(scope.inner).height() - $(scope.parent).height();
                            if(Math.abs(position-diff*2) > size ){    //минус видимая область єкрана
                                $(scope.inner).css({'top':'-'+size+'px'});
                            }else{
                                $(scope.inner).css({'top':(position - diff*2)+'px'});
                            }
                        }else{
                            if(position+(diff*2) > 0){
                                $(scope.inner).css({'top':'0px'});
                            }else{
                                $(scope.inner).css({'top':(position + diff*2)+'px'});                
                            }
                        }

                       scope.current_y = scope.move_y;

                       updateScroller(scope);
                    });                                                
                }

                var phoneSlideEvents = function(scope){
                    scope.move = false;
                    scope.current_y = null;

                    $(scope.parent)[0].addEventListener("touchstart", function(event){                            
                        for(var key in event.touches){
                            scope.move = true;
                            scope.current_y = event.touches[key].pageY;
                            break;
                        }
                    }, false);

                    $(scope.parent)[0].addEventListener("touchend", function(event){
                        scope.move = false;
                    }, false);

                    $(scope.parent)[0].addEventListener("touchmove", function(event){
                        if(!scope.move) return;

                        for(var key in event.touches){
                            scope.move_y = event.touches[key].pageY;
                            break;
                        }

                        if(scope.move_y == scope.current_y) return;

                        var diff = Math.abs(scope.move_y - scope.current_y);
                        var position = parseInt($(scope.inner).css('top'));

                        if(scope.move_y > scope.current_y){                                

                            if(position+(diff*2) > 0){
                                $(scope.inner).css({'top':'0px'});
                            }else{
                                $(scope.inner).css({'top':(position + diff*2)+'px'});
                            }
                        }else{
                            var size = $(scope.inner).height() - $(scope.parent).height();
                            if(Math.abs(position-diff*2) > size ){    //минус видимая область єкрана
                                $(scope.inner).css({'top':'-'+size+'px'});
                            }else{
                                $(scope.inner).css({'top':(position - diff*2)+'px'});
                            }
                        }

                        scope.current_y = scope.move_y;

                        updateScroller(scope);
                    }, false);
                }

                var linkFunction = function(scope, element, attributes){
                    scope.scroll = element;
                    scope.parent = $(attributes['parent']);
                    scope.inner = $(attributes['inner']);

                    mouseSlideEvents(scope);
                    phoneSlideEvents(scope);

                    //$(scope.parent)[0].addEventListener("DOMMouseScroll", function(e){})
                    $(scope.parent).mouseenter(function(){
                        $(scope.scroll).css({'opacity':'1'});
                        updateScroller(scope);
                    });

                    $(scope.parent).mouseleave(function(){
                        $(scope.scroll).css({'opacity':'0'});
                        updateScroller(scope);
                    });

                    $(scope.parent)[0].addEventListener("mousewheel", function(e){
                        var top = parseInt($(scope.inner).css('top'));

                        if(e.deltaY < 0){
                            if(top+speed > 0){
                                $(scope.inner).css({'top':'0px'});
                            }else{
                                $(scope.inner).css({'top':(top+speed)+'px'});    
                            }                
                        }else{
                            var size = $(scope.inner).height() - $(scope.parent).height();
                            if(Math.abs(top-speed) > size ){    //минус видимая область єкрана
                                $(scope.inner).css({'top':'-'+size+'px'});
                            }else{
                                $(scope.inner).css({'top':(top-speed)+'px'});
                            }
                        }

                        updateScroller(scope);
                        e.preventDefault();
                        e.stopPropagation();
                    }, false);

                    $(scope.parent)[0].addEventListener("DOMMouseScroll", function(e){
                        var top = parseInt($(scope.inner).css('top'));

                        if(e.detail < 0){
                            if(top+speed > 0){
                                $(scope.inner).css({'top':'0px'});
                            }else{
                                $(scope.inner).css({'top':(top+speed)+'px'});    
                            }                
                        }else{
                            var size = $(scope.inner).height() - $(scope.parent).height();
                            if(Math.abs(top-speed) > size ){    //минус видимая область єкрана
                                $(scope.inner).css({'top':'-'+size+'px'});
                            }else{
                                $(scope.inner).css({'top':(top-speed)+'px'});
                            }                
                        }

                        updateScroller(scope);
                        e.preventDefault();
                        e.stopPropagation();
                    });
                };

                return {
                    restrict: 'A',
                    scope: {
                        scroller: '='
                    },                        
                    template: '<div class="scroller_inner"></div>',
                    link: linkFunction
                };
            });
        </script>
        
        <script>
        //FILTER
        $('document').ready(function(){
            //проверяем сколько фильтров было использовано        
            var rules = 0;
            $('input[type=text],select','#top_menu form').each(function(){
                if($(this).val() != 0){
                    rules ++;
                }
            });

            if(rules > 0){
                $('#top_menu .options_cnt').text(rules+' опции');
                $('#top_menu .options_cnt').css({'display':'block'});
            }

            $('.reset_filter').click(function(){
                $('#top_menu form input[type=text]').val('');
                $('#top_menu form select option:selected').prop("selected", false);                

                return false;
            });
        });
        </script>
        
        <?=$this->module('svg')?>
        <?=$this->module('sidebar') ?>
        <?php //$this->module('sidebar') ?>
        
        <?=$this->maincontent?>       
    </body>
</html>