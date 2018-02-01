<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyChQwAXEXRThQkqgC-xW18anW640loh6IA&sensor=false&libraries=places&v=3"></script>

<div id="card_holder">
    <div id='top_menu' style='height: 60px;'>
        <?=$this->m->module('header')?>
    </div>
    <div id="card_fields">
        <div class="fields_block">
            <form action="" method="POST">
                <div class="form_top">
                    <div class="top_name_block">
                        <div class="card_name_holder">
                            <input type="text" value="" placeholder="Имя Фамилия" name="fullname" id="personal_name">
                        </div>
                    </div>
                    
                    <div class="tags_container">
                        <?php if(!$this->m->tags){ ?>
                            <a href="" class="add_tag">#тегировать</a>
                        <?php } ?>
                                                
                        <div class="add_tag_block" style="<?=$this->m->tags ? 'display:block':''?>">
                            <ul>
                                <?php if($this->m->tags){ ?>
                                    <?php foreach($this->m->tags as $item){ ?>
                                        <li><?=$item->name?></li>
                                    <?php } ?>
                                <?php } ?>
                                <li><input type="text" name="tag" value=""></li>
                            </ul>
                            <div class="tag_confirm">
                                <button type="button" class="close">&times;</button>
                                
                                <div class="tag"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card_tabs_wrapper">
                        <div class="item active">
                            <a href="">Основное</a>
                        </div>
                        <div class="item">
                            <a href="">Сделки</a>
                        </div>
                    </div>
                </div>
                
                <script>
                    $('document').ready(function(){
                        $('.form_groups').on('click','.form_group_container .form_label button',function(){
                            $(this).next('.list').css({"display":"block"});                            
                            return false;
                        })
                        $('.form_groups').on('click','.form_group_container .form_label .list li',function(){
                            
                            var parent = $(this).closest('.form_label');
                            $('.list li',parent).removeClass('selected');
                            $(this).addClass('selected');
                            $('button',parent).text($(this).text());
                            
                            $('.list',parent).css({'display':'none'});
                            return false;
                        });
                        $('.form_groups').on('mouseleave','.form_group_container .form_label .list',function(){
                            $(this).css({"display":"none"});
                        });                        
                    });
                </script>
                
                <script>
                    $('document').ready(function(){
                        $('.form_groups').on('keyup','.form_group_container[data-id=1] input',function(){
                            $('.form_group_container[data-id=1]:last .add_line_btn').css({'display':'block'});  
                        });
                        
                        $('.form_groups').on('click','.form_group_container[data-id=1] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=1]:first').clone();
                            
                            $('.form_group_container[data-id=1]:last').after(element);
                            $('.form_group_container[data-id=1] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                        
                        $('.form_groups').on('keyup','.form_group_container[data-id=2] input',function(){                            
                            $('.form_group_container[data-id=2]:last .add_line_btn').css({'display':'block'});  
                        });                        
                        $('.form_groups').on('click','.form_group_container[data-id=2] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=2]:first').clone();
                            $('.form_group_container[data-id=2]:last').after(element);
                            $('.form_group_container[data-id=2] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                        
                        $('.form_groups').on('keyup','.form_group_container[data-id=3] input',function(){
                            $('.form_group_container[data-id=3]:last .add_line_btn').css({'display':'block'});  
                        });
                        $('.form_groups').on('click','.form_group_container[data-id=3] .add_line_btn',function(){
                            var element = $('.form_group_container[data-id=3]:first').clone();
                            $('.form_group_container[data-id=3]:last').after(element);
                            $('.form_group_container[data-id=3] .add_line_btn').css({"display":"none"});
                            $('.form_label button',element).text($('.list li:first',element).text());
                            
                            $('.list li',element).removeClass('selected');
                            $('.list li:first',element).addClass('selected');
                            
                            $('input',element).val('');
                            $('.add_line_btn',element).css({'display':'block'});
                        });
                    });
                </script>
                <div class="form_groups">
                    <div class="form_group_container">
                        <div class="form_label">Отв-ный</div>

                        <div class="form_value">
                            <input type="text" value="<?=$this->m->_user->email?>" placeholder="...">
                        </div>
                    </div>
                    
                    <div class="form_group_container" data-id="1">
                        <div class="form_label">
                            <button type="button">Раб.тел.</button>
                            <ul class="list">
                                <?php foreach($this->m->config->phoneTypes as $key=>$item){ ?>
                                    <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                                <?php } ?>
                            </ul>
                        </div>
                        
                        
                        <div class="form_value">
                            <div class='add_line_btn'>
                                <div class='ico'></div>
                            </div>
                            <input type="text" value="" placeholder="...">
                        </div>
                    </div>
                    
                    <div class="form_group_container" data-id="2">
                        <div class="form_label">
                            <button type="button">Email раб.</button>
                            
                            <ul class="list">
                                <?php foreach($this->m->config->emailTypes as $key=>$item){ ?>
                                    <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                                <?php } ?>                                
                            </ul>
                        </div>
                        <div class="form_value">
                            <div class='add_line_btn'>
                                <div class='ico'></div>
                            </div>
                            <input type="text" value="" placeholder="...">
                        </div>
                    </div>
                    
                    <div class="form_group_container" data-id="3">
                        <div class="form_label">
                            
                            <button type="button">Skype</button>
                            
                            <ul class="list">
                                <?php foreach($this->m->config->messangerTypes as $key=>$item){ ?>
                                    <li data-id="<?=$key?>" class="<?=$key==1?'selected':''?>"><?=$item?></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="form_value">
                            <div class='add_line_btn'>
                                <div class='ico'></div>
                            </div>
                            <input type="text" value="" placeholder="...">
                        </div>
                    </div>
                    
                    <div class="form_group_container">
                        <div class="form_label">Адресс</div>

                        <div class="form_value">
                            <input id="address" type="text" value="" placeholder="...">
                        </div>
                    </div>
                    <div class="form_group_container suggestion_block" style="display:none;" >
                        <div class="form_label">
                            
                        </div>
                        <div class="form_value">
                            <input id="suggestion" type="text" value="" placeholder="..." onClick="$('#address').val($('#suggestion').val())">
                        </div>
                    </div>
                    
                    <div class="form_group_container">
                        <div class="form_label">
                            Почтовый Код
                        </div>
                        <div class="form_value">
                            <input id="postal_code" type="text" value="" placeholder="...">
                            
                            <input name="apartment" type="hidden">
                            <input name="street" type="hidden">
                            <input name="city" type="hidden">
                            <input name="country" type="hidden">
                            
                            <input name="lat" type="hidden">
                            <input name="lng" type="hidden">
                            <input name="place_id" type="hidden">
                        </div>
                    </div>
                                        
                    <div style="width:100%; height:300px; display:none" id="map"></div>
                </div>
            </form>
                        
        </div>
        <script>
            $('document').ready(function(){
                $('#card_fields form input').keyup(function(){
                    $('.buttons_block').css({'display':'flex'});
                });
            });
        </script>
        
        <script>
            $('document').ready(function(){
                $('.buttons_block .save').click(function(){                    

                    //получать контакты
                    var phones = $('.form_group_container[data-id=1]');
                    var emails = $('.form_group_container[data-id=2]');
                    var messangers = $('.form_group_container[data-id=3]');
                            
                    var data = {
                        fullname : $('#personal_name').val(),
                        address : $('.fields_block form #address').val(),
                        apartment : $('.fields_block form input[name=apartment]').val(),
                        street : $('.fields_block form input[name=street]').val(),
                        city : $('.fields_block form input[name=city]').val(),
                        country : $('.fields_block form input[name=country]').val(),
                        postal_code : $('.fields_block form #postal_code').val(),
                        place_id : $('.fields_block form input[name=place_id]').val(),
                        
                        lat : $('.fields_block form input[name=lat]').val(),
                        lng : $('.fields_block form input[name=lng]').val(),
                        
                        phone_types:[],
                        phones:[],
                        email_types:[],
                        emails:[],
                        messanger_types:[],
                        messangers:[],
                        messages:[],
                        messages_date:[],
                        tags:[],
                    }
                    
                    phones.each(function(){
                        data.phone_types.push($('.list li.selected',this).attr('data-id'));
                        data.phones.push($('.form_value input',this).val());                        
                    });
                    
                    emails.each(function(){
                        data.email_types.push($('.list li.selected',this).attr('data-id'));
                        data.emails.push($('.form_value input',this).val());                        
                    });
                    messangers.each(function(){
                        data.messanger_types.push($('.list li.selected',this).attr('data-id'));
                        data.messangers.push($('.form_value input',this).val());                        
                    });
                    
                    $('.note_item_wrapper').each(function(){
                        data.messages.push($('.message',this).text());
                        data.messages_date.push($('.header',this).attr('data-time'));                        
                    });
                    
                    //получаем все тэги
                    $('.tags_container .add_tag_block ul li').each(function(){
                        var name = $(this).text();
                        if(name)data.tags.push(name);
                    });
                    
                    $.ajax({
                        url:'/contacts/add/',
                        type:'POST',
                        data:data,
                        
                        success:function(msg){
                            console.log(msg);
                        }
                    });
                    return false;
                });
            });
        </script>
        <div class='buttons_block'>
            <a href='' class='btn btn-primary save'>Сохранить</a>
            <a href='' class='btn btn-default cancel'>Отменить</a>
        </div>
    </div>
    
    <script>
        $('document').ready(function(){
            $('.notes_block form').submit(function(){
                var message = $('.notes_block form input[name=message]').val();
                
                $.ajax({
                    url:'/notes/note_item/',
                    type:'POST',
                    data:{message:message},
                    success:function(msg){
                        console.log(msg);
                        $('.notes_wrapper .notes_inner').append(msg);
                    }
                });
                                
                return false;
            });
        
            $('.notes_block .notes_actions .cancel').click(function(){
                $('.notes_block .notes_actions').css({"display":'none'});
                return false;
            });
            
            $('.notes_wrapper .notes_block .input_block input').keyup(function(){                
                $('.notes_block .notes_actions').css({"display":'block'});
                return false;
            });
        });
    </script>
    
    <div class="notes_wrapper">
        <div class="notes_inner">
            
        </div>
        <div class="notes_block">
            <form action="">
                <div class='message_block'>
                    <div class='note_type_list'>
                        <div class='current'>Примечание: </div>
                    </div>
                    <div class='input_block'>
                        <input type="text" placeholder='' name="message">
                    </div>
                </div>
                <div class='notes_actions'>                
                    <input type='submit' class='btn btn-primary save' value='Добавить'>
                    <input type='button' class='btn btn-default cancel' value='Отмена'>
                </div>
            </form>
        </div>
        
    </div>
</div>