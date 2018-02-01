 $('document').ready(function () {
    $('.actions_panel .add_task').click(function(){
        var id = $(this).closest('.tr').attr('data-id');
        $('#taskModal form input[name=id]').val(id);

        $('#taskModal').modal('show');
        return false;
    });

    $('.actions_panel .del_user').click(function(){
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
    });

    $('.action_panel_triger').change(function(){
        var parent = $(this).closest('.tr');
        if ($(this)[0].checked) {
            $('.actions_panel', parent).css({'display': 'flex'});
            $('.td:first', parent).addClass('actions_style');
        } else {
            $('.actions_panel', parent).css({'display': 'none'});
            $('.td:first', parent).removeClass('actions_style');
        }
    });
});

$('document').ready(function () {       //RIGHT SIDEBAR
    
    $('.right_sidebar_btn').click(function(){
        $('.right_sidebar').addClass('shown').removeClass('hiden');
        return false;
    });

    $('.right_sidebar .cancel').click(function () {
        $('.right_sidebar').removeClass('shown').addClass('hiden');
    });
});

//TAGS in ADD CONTACT AND DETAILS
$('document').ready(function(){
    $('.add_tag').click(function(){
        $(this).css({'display':'none'});
        $('.add_tag_block').css({'display':'block'});
        return false;
    });
    $('.add_tag_block input[name=tag]').focus(function(e){
        $('.add_tag_block .tag_confirm').css({'display':'flex'});
    });

    $('.add_tag_block input').keyup(function(e){
        $('.add_tag_block .tag_confirm .tag').text($(this).val());
    });

    $('.add_tag_block .tag_confirm').click(function(e){
        if($(e.target).attr('class').indexOf('close') != -1){
            $('.add_tag_block .tag_confirm').css({'display':'none'});
            return false;
        }

        var name = $('.add_tag_block input').val();
        var user_id = $('.fields_block form input[name=user_id]').val();
        
        //добавляем тег в блок
        if(!user_id){   //если создаем нового пользователя то отправлять будем при сабмите пользователя
            $('.add_tag_block ul li:last-child').before('<li>'+name+'</li>');
            $('.add_tag_block input').val('');
            $('.add_tag_block .tag_confirm').css({'display':'none'});
        }else{
            $.ajax({
                url:'/tags/add/',
                type:'POST',
                data:{name:name,user_id:user_id},
                success:function(msg){                    
                    $('.add_tag_block ul li:last-child').before('<li>'+name+'</li>');
                    $('.add_tag_block input').val('');
                    $('.add_tag_block .tag_confirm').css({'display':'none'});
                }
            });
        }
    });
});


//FILTER
$('document').ready(function(){
    $('.reset_filter').click(function(){

        $('#top_menu form input[type=text]').val('');
        $('#top_menu form select option:selected').prop("selected", false);                

        return false;
    });
});