    function Calendar(obj){        
        this.parent = obj.parent;
        this.width = obj.width?obj.width:'500px';
        this.height = obj.height?obj.height:'500px';
        this.reservedDates = obj.reservedDates;
        
        $('#calendar .c_box').css({'width':this.width,'height':this.height});
                
        this.month_array = new Array('January','February','March','April','May','June',"July",'August','September','October','November','December');
        this.short_tags_array = new Array('Mon','Tue','Wed','Thu','Fri','Sat',"Sun");
        
        if(obj.startDate){
            var d = new Date(obj.startDate);
        }else{
            var d = new Date();
        }
        this.month = d.getMonth();
        this.year = d.getYear()+1900;
        
        this.render();        
        
        this.actions();
    }
    
    Calendar.prototype = {
        prev:{},
        current:{},
        next:{},
        width:0,height:0,
        clear:function(){
            $('.c_box .c_dates',this.parent).empty();
        },
        
        render:function(){
            this.clear();
            //получаем количество дней
            this.current.total_days = new Date(this.year,this.month+1,0).getDate();

            this.current.first_day_of_week = new Date(this.year,this.month,1).getDay();
            this.current.first_day_of_week = this.current.first_day_of_week == 0?7:this.current.first_day_of_week;

            this.prev.total_days = new Date(this.year,this.month,0).getDate();

            this.setCurrentDate();

            this.addDateTags();
            this.addDays();
        },
        getFilledDatas:function(callback){            
            var self = this;
            
            $.ajax({
                url:'/manage/filled/?date='+self.year+'-'+(self.month+1)+'-01',
                type:'GET',
                success:function(msg){
                    self.reservedDates = JSON.parse(msg);
                    
                    callback();
                    
                }
            });
        },
        nextMonth:function(){
            var self = this;
            var d = new Date(this.year,this.month+1);
            this.month = d.getMonth();
            this.year = d.getYear()+1900;
            
            this.getFilledDatas(function(){self.render();});
        },
        
        prevMonth:function(){
            var self = this;
            var d = new Date(this.year,this.month-1);
            this.month = d.getMonth();
            this.year = d.getYear()+1900;
            
            this.getFilledDatas(function(){self.render();});
        },
        
        actions:function(){
            var self = this;
            
            $(this.parent).on('click','.prev_button,.c_day.prev',function(){
                self.prevMonth();
            });
            
            $(this.parent).on('click','.next_button,.c_day.next',function(){
                self.nextMonth();
            });
            
            $(this.parent).on('click','.c_day.current',function(){
                var day = $(this).text();
                
                if($('.c_box.online').length > 0){
                    location.href = '/tasks/?year='+self.year+'&month='+(self.month+1)+'&day='+day;
                }
            });
        },
        
        setCurrentDate:function(){
            $('.c_box .current_date').text(this.month_array[this.month]+' '+this.year);
        },
        addDateTags:function(){
            var html = "<div class='c_row date_tags'>";
            
            for(var key in this.short_tags_array){
                html += "<div class='c_day_name'>"+this.short_tags_array[key]+"</div>";
            }
            html += '</div>';
            $('.c_box .c_dates',this.parent).append(html);
        },
        checkHoliday:function(year,month,day){
            var d = new Date(year,month,day);
            var dayOfWeek = d.getDay();
            
            if(dayOfWeek == 6 || dayOfWeek == 0){
                return 'holiday';
            }
            
            return '';
        },
        checkReservatedDays:function(year, month, day){
            var d;
            
            for(var key in this.reservedDates){
                d = new Date(this.reservedDates[key]*1000);
                
                if(day == d.getDate() && month == d.getMonth() && year == d.getYear()+1900){
                    return 'reserved';    
                }
            }
            /*if(day == 23){
                return 'reserved';
            }*/
            return '';
        },
        checkToday:function(year,month,day){
            var date = new Date();

            var m = date.getMonth();
            var y = date.getYear()+1900;
            var d = date.getDate();
            
            if(year == y && month == m && day == d){
                return 'today';
            }
            return '';
        },
        addDays:function(){
            var k=0;
            var grey = false;
            
            for(var i=0;i<6; i++ ){
                if(i==0){   //первая неделя с частью предыдущего месяца
                    
                    var html = "<div class='c_row'>";
                    if(this.current.first_day_of_week == 1){    //если первый день это понедельник то первоя строка это все прошлый месяц
                        for(var j=this.prev.total_days-6;j<=this.prev.total_days;j++){
                            html += "<div class='c_day prev'>"+j+"</div>";
                        }
                    }else{
                        //console.log(this.current.first_day_of_week);
                        var dayOfWeek = this.current.first_day_of_week;
                        
                        for(var j=this.prev.total_days-dayOfWeek+2;j<=this.prev.total_days;j++){                            
                            html += "<div class='c_day prev'>"+j+"</div>";
                        }
                        //console.log(dayOfWeek);
                        for(var j=dayOfWeek ; j <= 7;j++){
                            
                            html += "<div class='c_day current "+this.checkToday(this.year,this.month,k+1)+" "+this.checkHoliday(this.year,this.month,k+1)+" "+this.checkReservatedDays(this.year,this.month,k+1)+"'>"+(++k)+"</div>";
                        }
                    }
                    
                    html +="</div>";
                    $('.c_box .c_dates',this.parent).append(html);
                    continue;
                }else{
                    var html = "<div class='c_row'>"                    
                    
                    for(var j=0;j<7;j++){                   
                        html += "<div class='c_day "+(grey?'next':"current "+this.checkToday(this.year,this.month,k+1)+" "+this.checkHoliday(this.year,this.month,k+1) +' '+this.checkReservatedDays(this.year,this.month,k+1))+"'>"+(++k)+"</div>";

                        if(k == this.current.total_days){   //определяем следующий месяц
                            grey = true;
                            k = 0;
                        }
                    }

                    html +="</div>";
                    $('.c_box .c_dates',this.parent).append(html);
                }
            }
        }
    }