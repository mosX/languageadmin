<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyChQwAXEXRThQkqgC-xW18anW640loh6IA&sensor=false&libraries=places&v=3"></script>
<script>
    app.controller('userinfoCtrl', function($scope,$http){
        $scope.user = {};
        $scope.params = {};
        $scope.types = {};        
        $scope.addLineStatus = {1:false,2:false,3:false};
        
        $scope.updateUser = function(event){
            console.log($scope.user);
            
            $http({
                url:'/userinfo/update/',
                method:'POST',
                data:$scope.user
            }).then(function(ret){                
                if(ret.data.status == 'success'){
                    $scope.user = $scope.initUserPackage(ret.data.user);
                }else{
                    console.log('ERROR');
                }
            });
            
            event.preventDefault();
        }
        
        $scope.showViewStatistic = function(event){
            console.log('PANEL');
            if(parseInt($('#views_panel').css('right')) < 0){                
                $http({
                    url:'/contacts/getviewshistory/',
                    method:'GET',

                }).then(function(ret){
                    $('#views_panel').animate({"right":'400px'},600);
                    //console.log(ret.data);
                    $scope.viewhistory = ret.data;
                    console.log($scope.viewhistory);
                });
            }else{
                $('#views_panel').animate({"right":'-600px'},600);
            }
            
            event.preventDefault();
        }
        
        $scope.showDialog = function(event,url){
            $('#dialogModal .iframe').html("<iframe src='"+url+"' style='width:100%; height:500px; border:none;'></iframe>");
            $('#dialogModal').modal('show');
            
            event.preventDefault();            
        }
        
        $scope.initUserPackage = function(data){
            $scope.types[1] = data.phoneTypes;
            $scope.types[2] = data.emailTypes;
            $scope.types[3] = data.messangerTypes;
            
            if(data.contacts){
                //добавиляем пустые поля.
                data.contacts[1].push({value:'',type:'1'});
                data.contacts[2].push({value:'',type:'1'});
                data.contacts[3].push({value:'',type:'1'});
            }
            
            return data;
        }
        
        $scope.$on('getUser', function(event,data) {    //принимаем данные пользователя от родительского контроллера
            $scope.user = $scope.initUserPackage(data);
        });
        
        $scope.showList = function(event){
            $(event.target).next('.list').css({"display":"block"});
         
            event.preventDefault();
        }
        
        $scope.selectListElement = function(event,item,key){
            var parent = $(event.target).closest('.form_label');
            $('.list li',parent).removeClass('selected');
            $(event.target).addClass('selected');
            $('button',parent).text($(event.target).text());

            $('.list',parent).css({'display':'none'});                            
        }
        
        $scope.hideList = function(event){  //прячем список с типами
            $(event.target).closest('ul').css({display:'none'});
        }
        
        $scope.showAddBtn = function(event,group){            
            $scope.addLineStatus[group] = true;            
        }
        
        $scope.addNewBlank = function(group){
            $scope.user.contacts[group].push({value:'',type:'1'});           
        }
    });
</script>

<div style="display:none;background: red; position:fixed; z-index:99; left:0px;top:0px; width:100%; height:100%;opacity:0" class="userinfo_overflow"></div>

<div class="modal fade" id="dialogModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style='top:100px;width:1200px'>
        <div class="modal-content" style=" min-height: 300px; padding:0px;background:white; border: 2px solid black">
            <div class="modal-body" style='padding:0px'>
                <button class="close" data-dismiss="modal">×</button>
                <div class='iframe'></div>
            </div>
        </div>
    </div>
</div>

<div ng-controller="userinfoCtrl">
    <style>
        #views_panel{
            z-index:99;
            position:fixed;
            top:0px;
            right:-600px;
            width:600px;
            height:100%;
            background: white;
            border-left:3px solid #ccc;
        }
    </style>
    <div id='views_panel'>
        <table class="table" style="max-width:600px;min-width:auto;">
            <tr>
                <th style="width:100px;">Канал</th>
                <th style="width:100px;">Начало</th>                
                <th style="width:100px;">Конец</th>
                <th style="width:100px;">Продолжительность</th>
                <th style="width:100px;">АйПи</th>
                <th style="width:100px;">Статус</th>
            </tr>
            <tr ng-repeat="item in viewhistory">
                <td>{{item.name}}</td>
                <td>{{item.start | date : "MMM dd hh:mm"}}</td>
                <td>{{item.end | date : "MMM dd hh:mm"}}</td>
                <td>{{(item.start-item.end)/1000}}</td>
                <td>{{item.ip}}</td>
                <td>{{item.status == 1?"Просматривает":"завершено"}}</td>                
            </tr>
        </table>
    </div>

    <div class="right_sidebar" data-id='{{user.id}}'>
        <button ng-click='showViewStatistic($event)'>Просмотры</button>
        <div class="form-group">
            <div class="row">                
                <div class="col-sm-4">
                    ID
                </div>
                <div class="col-sm-8">
                    {{user.id}}
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <div class="row">                
                <div class="col-sm-4">
                    Full name
                </div>
                <div class="col-sm-8">{{user.fullname}}</div>
            </div>
        </div>
        
        <form class="form-groups" action="" method="">
            <!--PHONE-->
            
            <div ng-repeat="el in [1,2,3]" style="margin-bottom:20px;">
                <div ng-repeat="(key,item) in user.contacts[el]" class="form_group_container" data-id="{{el}}">
                    <div class="form_label">
                        <button ng-click="showList($event)" type="button">{{types[el][item.type]}}</button>
                        <ul class="list" ng-mouseleave="hideList($event)">
                            <li ng-click="selectListElement($event,type,key)" ng-repeat="type in types[el]" data-id="{{key}}" class="<?=$key==$contact->type?'selected':''?>">{{type}}</li>
                        </ul>
                    </div>

                    <div class="form_value">
                        <div ng-show="addLineStatus[el] && $last" class='add_line_btn' style="display:block" ng-click="addNewBlank(el)">
                            <div class='ico'></div>
                        </div>

                        <input ng-keyup="showAddBtn($event,el)" type="text" ng-model="item.value" value="{{item.value}}" placeholder="...">
                        <input type="hidden" value="{{item.id}}" name="id">
                    </div>
                </div>
            </div>

        <script>
            app.controller('userinfoMapCtrl', function($scope,$http){
                $scope.user = {};
                $scope.map = {};
                $scope.geocoder = new google.maps.Geocoder();
                $scope.autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
                $scope.latlng = {};
                
                $scope.$on('getUser', function(event,data) {    //принимаем данные пользователя от родительского контроллера
                    $scope.user = data;
                    console.log($scope.user);

                    $scope.initMap();    
                });
                
                $scope.initMap = function(){
                    var lat = $scope.user.lat,lng = $scope.user.lng;

                    if($('input[name=lat]').val() && $('input[name=lng]').val()){
                        lat = $('input[name=lat]').val();
                        lng = $('input[name=lng]').val();
                    }

                    $scope.latlng = new google.maps.LatLng(lat, lng);

                    var myOptions = {
                        zoom: 13,
                        draggable: true,
                        zoomControl: true,
                        scrollwheel: false,
                        disableDoubleClickZoom: true,
                        center: $scope.latlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    $scope.map = new google.maps.Map(document.getElementById("map"), myOptions);

                    var marker = new google.maps.Marker({   //устанавливаем маркер
                      map: $scope.map,
                      position: $scope.latlng
                    });
                }
                
                 $scope.geocoderLocation = function(){
                    $('#map').css({'display':'block'});
                    console.log($scope.map);
                    $scope.map.setCenter($scope.latlng);

                    $scope.geocoder.geocode({'location': $scope.latlng}, function(results, status){
                        var params = {
                            postal_code:'',
                            street:'',
                            city:'',
                            country:'',
                            apartment:'',
                        };
                        if (status === google.maps.GeocoderStatus.OK) {
                          if (results[1]) {

                            var marker = new google.maps.Marker({
                              position: $scope.latlng,
                              map: $scope.map
                            });

                            $('#suggestion').val(results[0].formatted_address);
                            $('.suggestion_block').css({'display':'flex'});

                            $('input[name=place_id]').val(results[0].place_id);
                            for(var key in results){
                                for(var key2 in results[key].address_components){   //бегаем по компонениам
                                    //бегаем по типу компонента
                                    for(var key3 in results[key].address_components[key2].types){

                                        if(results[key].address_components[key2].types[key3] == 'postal_code' && params.postal_code.length == 0){
                                            $('#postal_code').val(results[key].address_components[key2].long_name);
                                            params.postal_code = results[key].address_components[key2].long_name;
                                        }

                                        if(results[key].address_components[key2].types[key3] == 'route' && params.street.length == 0){
                                            params.street = results[key].address_components[key2].long_name;
                                            $('input[name=street]').val(params.street);
                                        }

                                        if(results[key].address_components[key2].types[key3] == 'locality' && params.city.length == 0){
                                            params.city = results[key].address_components[key2].long_name;
                                            $('input[name=city]').val(params.city);
                                        }

                                        if(results[key].address_components[key2].types[key3] == 'country' && params.country.length == 0){
                                            params.country = results[key].address_components[key2].long_name;
                                            $('input[name=country]').val(params.country);
                                        }

                                        if(results[key].address_components[key2].types[key3] == 'street_number' && params.apartment.length == 0){
                                            params.apartment = results[key].address_components[key2].long_name;
                                            $('input[name=apartment]').val(params.apartment);
                                        }
                                    }
                                }
                            }
                          } else {
                            window.alert('No results found');
                          }
                        } else {
                          window.alert('Geocoder failed due to: ' + status);
                        }
                    });
                }
                
                $scope.geocodeAddress = function(geocoder, resultsMap){
                    var address = document.getElementById('address').value; //получаем значение

                    $scope.geocoder.geocode({'address': address}, function(results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {

                          $scope.latlng = results[0].geometry.location;
                          console.log($scope.latlng.lat(),$scope.latlng.lng());
                          $('input[name=lat]').val($scope.latlng.lat());
                          $('input[name=lng]').val($scope.latlng.lng());

                          $scope.geocoderLocation();
                        } else {
                          alert('Geocode was not successful for the following reason: ' + status);
                        }
                    });
                }

                $('#address').change(function(){
                    console.log(map);
                    $scope.geocodeAddress($scope.geocoder, $scope.map);
                });
                
                $scope.autocomplete.addListener('place_changed', function(){
                    $scope.latlng = new google.maps.LatLng($scope.autocomplete.getPlace().geometry.location.lat(),$scope.autocomplete.getPlace().geometry.location.lng());        
                    
                    $scope.user.lat = $scope.latlng.lat();
                    $scope.user.lng = $scope.latlng.lng();
                    $scope.$digest();
                    $scope.geocoderLocation();
                });
            });
        </script>
        
        <div ng-controller="userinfoMapCtrl">
            <div class="form_group_container">
                <div class="form_label">
                    Адресс
                </div>
                <div class="form_value">
                    <input id="address" type="text" value="{{user.address}}" placeholder="...">
                </div>
            </div>
            <div class="form_group_container suggestion_block" style="display:none;" >
                <div class="form_label">

                </div>
                <div class="form_value">
                    <input id="suggestion" type="text" value="" placeholder="..." onClick="$('#address').val($('#suggestion').val())">
                </div>
            </div>
            
            <div  style="width:100%; height:300px; display:no2ne" id="map"></div>
        </div>
        
        <div class="form-group">
            <div class="row">                
                <div class="col-sm-12">
                    <button ng-click="updateUser($event)" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>ID:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.id}}
                        / <a href="/cabinet/?user_id={{user.id}}">CRM</a>
                        / <a href="/deals/all/real/?email={{user.id}}">Deals</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Документы:</label>
                    </div>
                    <div class="col-sm-7">
                        <a href="/managment/documents/?user_id={{user.id}}">Проверить</a>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Имя:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.firstname}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Фамилия:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.lastname}}
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.fathersname">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Отчество:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.fathersname}}
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.pin_code">
                <div class="row">
                    <div class="col-sm-5">
                        <label>PIN код:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.pin_code}}
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.upass">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Пароль:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.upass_decrypted}}

                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.passport">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="label">Серия:</div>
                    </div>
                    <div class="col-sm-7">
                        {{user.passport}}                    
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.inn">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="label">Инн:</div>
                    </div>
                    <div class="col-sm-7">
                        <?=$this->m->rows->inn?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Страна:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.country}} ({{user.timezone}})
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Телефон:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.phone_prefix}} {{user.phone_area ? '('+user.phone_area+'}':''}} {{user.phone}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Дата Рождения:</label>
                    </div>
                    <div class="col-sm-7">                    
                        {{user.birthday|date:'dd-MM-yyyy'}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Статус:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.status}}
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.robot">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Робот:</label>
                    </div>
                    <div class="col-sm-7">
                        Включен
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Заходил:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.last_login|date:'dd-MM-yyyy HH:mm:ss'}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Последний АйПи:</label>
                    </div>
                    <div class="col-sm-7">
                        <span title="{{user.ip_country| uppercase}}" alt="{{user.ip_country| uppercase}}" style="margin:0;" class="flag {{user.ip_country}}"></span>
                        {{user.last_ip}} <div class="history" ng-click="showDialog($event,'/analize/history/?id={{user.id}}')"></div>
                        <div class="activity" ng-click="$userinfo.showDialog($event,'/analize/activity/?id={{user.id}}')"></div>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.partner">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Партнер:</label>
                    </div>
                    <div class="col-sm-7">                    
                        {{user.partners}}
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="user.afftracker">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Afftrack:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.afftracker}}                    
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Регистрация:</label>
                    </div>
                    <div class="col-sm-7">
                        {{user.date|date:'dd-MM-yyyy HH:mm:ss'}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Bad Auth:</label>
                    </div>
                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-sm-6">
                                <input class="form-control" onClick="$(this).select();" type="text" value="{{user.bad_auth}}">
                            </div>
                            <div class="col-sm-6">
                                <input style="width:100%;" class="btn btn-primary" type="button" value="SAVE" onClick="Userinfo.badauthSet($(this).prev('input'),'id TODO');">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div ng-repeat="item in user.accounts" ng-show="user.accounts">
                <div class="form-group" ng-show="item.mode == 1">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Баланс:</label>
                        </div>
                        <div class="col-sm-7">
                            {{item.balance | currency}} <span ng-show="item.bonus" style="color:red">({{item.bonus}})</span> (<b>{{item.id}} {{item.currency}}</b>)
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.mode == 0">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>ФанБаланс:</label>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-5">
                                    <span>{{item.balance | currency}}</span>
                                </div>
                                <div class="col-sm-7">
                                    <input class="btn btn-primary" ng-show="item.balance < 50000" type="button" value="Пополнить" ng-click="depositFunBalance($event,$index)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.mode == 4">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>ИнвестБаланс:</label>
                        </div>
                        <div class="col-sm-7">
                            <span>{{item.balance | currency}}</span> (<b>{{item.id}} {{item.currency}}</b>)
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.deposit_sum > 0">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Сумма Депозитов:</label>
                        </div>
                        <div class="col-sm-7">
                            {{item.deposit_sum | currency}}
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.withdraw_sum > 0">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Сумма Выводов:</label>
                        </div>
                        <div class="col-sm-7">
                            <span style="color:red">{{item.withdraw_sum | currency}}</span>
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.mode == 1 || item.mode == 4">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>MaxBalance:</label>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input class="form-control" onClick="$(this).select();" type="text" ng-init="params.maxbalance=(item.maxbalance | currency)" ng-model="params.maxbalance">
                                </div>
                                <div class="col-sm-6">
                                    <input style="width:100%" class="btn btn-primary" type="button" value="SAVE" ng-click="maxbalanceSet($event,$index)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Gain:</label>
                        </div>
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input class="form-control" onClick="$(this).select();" type="text" value="{{item.gain}}">
                                </div>
                                <div class="col-sm-6">
                                    <input style="width:100%;" class="btn btn-primary" type="button" value="SAVE" ng-click="gainSet($event,$index,'real')">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="item.mode == 1 || item.mode == 4">
                    <div class="row">
                        <div class="col-sm-5">
                            <label>Autogain ({{item.autogain_count}}):</label>
                        </div>
                        <div class="col-sm-7">
                            <div style="display:inline-block; cursor:pointer" ng-click="changeAutogain(event,$index)" class="{{item.autogain == 0?'enableuser':'disableuser'}}"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>Сменить пароль:</label>
                    </div>
                    <div class="col-sm-7">
                        <div class="password" ng-click="showDialog($event,'/users/password/?id={{user.id}}')"></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-5">
                        <label>BadAuth:</label>
                    </div>
                    <div class="col-sm-7">
                        <div style="display:inline-block; cursor:pointer" ng-click='changeStatus($event)' class="{{user.bad_auth >= 5?'enableuser':'disableuser'}} "></div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-default" value="Сохранить">
                <input type="button" class="btn btn-danger cancel" value="Отменить">
            </div>
        </form>
    </div>
</div>