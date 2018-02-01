var app = angular.module("app",[]);

app.factory('$userinfo', ['$http', function($http){
    var msgs = [];
    var $obj = {};

    $obj.user = {};
    
    $('.userinfo_overflow').click(function(){
        $(this).css({'display':'none'});
        $('.right_sidebar').addClass('hiden').removeClass('shown');
        $('#views_panel').css({"right":'-600px'});
    });

    $obj.init = function(id,callback){   //при нажатие на иконку        
        if($('.right_sidebar').attr('class').indexOf('shown') != -1 && $('.right_sidebar').attr('data-id') == id){
            
            
            $('.right_sidebar').addClass('hiden').removeClass('shown');                
            
            return;
        }        

        $http({
            method: 'GET',
            url:'/userinfo/data/?id='+id
        }).then(function(res){
            $('.right_sidebar').addClass('shown').removeClass('hiden');
            $('.userinfo_overflow').css({'display':'block'});
            //scope.user = res.data;
            callback(res.data);                
        });                        
    }

    return $obj;        
}]);
  
 $('document').ready(function(){
     $('.table tr').hover(function(){
         $('.hover_block .ico',this).css({'display':'inline-block'});
     },function(){
         $('.hover_block .ico',this).css({'display':'none'});
     });
     
    /*$('.actions_panel .add_task').click(function(){
        var id = $(this).closest('.tr').attr('data-id');
        $('#taskModal form input[name=id]').val(id);

        $('#taskModal').modal('show');
        return false;
    });*/

    /*$('.actions_panel .del_user').click(function(){
        var id = $(this).closest('.tr').attr('data-id');
        var name = $('.username',$(this).closest('.tr')).text();

        $('#confirmModal form input[name=id]').val(id);
        $('#confirmModal span.username').text(name);

        $('#confirmModal').modal('show');
        return false;
    });
    $('.actions_panel .edit_tags').click(function(){
        var id = $(this).closest('.tr').attr('data-id');

        $('#tagsModal form input[name=id]').val(id);
        $('#tagsModal').modal('show');

        return false;
    });*/

    $('.action_panel_triger').change(function(){
        var parent = $(this).closest('.tr');
        if(parent.length == 0){
            var parent = $(this).closest('tr');
        }
        
        if ($(this)[0].checked) {
            $('.actions_panel', parent).css({'display': 'flex'});
            $('.td:first,td:first',parent).addClass('actions_style');
        } else {
            $('.actions_panel', parent).css({'display': 'none'});
            $('.td:first,td:first', parent).removeClass('actions_style');
        }
    });
});

$('document').ready(function(){       //RIGHT SIDEBAR
    $('.right_sidebar_btn').click(function(){
        $('.right_sidebar').addClass('shown').removeClass('hiden');
        return false;
    });

    $('.right_sidebar .cancel').click(function () {
        $('.right_sidebar').removeClass('shown').addClass('hiden');
    });
});



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

$('document').ready(function(){
    $('.modal_action').click(function(){
        var url = $(this).attr('href');

        $('#actionFrameModal .iframe').html("<iframe src='"+url+"' style='width:100%; height:100%; border:none;'></iframe>");;
        $('#actionFrameModal').modal('show');
        
        return false;
    });
});