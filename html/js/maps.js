 $('document').ready(function(){
    var map;
    var geocoder = new google.maps.Geocoder();
    //var infowindow = new google.maps.InfoWindow;
    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));

    var latlng;
    
    function initMap(){
        var lat = -33.8688,lng = 151.2195;
        
        if($('input[name=lat]').val() && $('input[name=lng]').val()){
            lat = $('input[name=lat]').val();
            lng = $('input[name=lng]').val();
        }
        
        var latlng = new google.maps.LatLng(lat, lng);
        
        var myOptions = {
            zoom: 13,
            draggable: true,
            zoomControl: true,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map"), myOptions);
        
        var marker = new google.maps.Marker({   //устанавливаем маркер
          map: map,
          position: latlng
        });        
    }

    function geocoderLocation(){
        $('#map').css({'display':'block'});
        map.setCenter(latlng);
        
        geocoder.geocode({'location': latlng}, function(results, status){
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
                  position: latlng,
                  map: map
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
    function geocodeAddress(geocoder, resultsMap){
        var address = document.getElementById('address').value; //получаем значение

        geocoder.geocode({'address': address}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {


            //console.log(results[0].geometry.location);

            latlng = results[0].geometry.location;
            console.log(latlng.lat(),latlng.lng());
            $('input[name=lat]').val(latlng.lat());
            $('input[name=lng]').val(latlng.lng());

            //geometry 
            //ROOFTOP - точный адрес с почтовым адресом
            //RANGE_INTERPOLATED приблизительная точка
            //все остальное тоже приблизительный результат

            //partial_match указывает на частичное совпадение или на опечатки в адресе

            /*
             * 
                street_address – указывает точный почтовый адрес.
                route – указывает шоссе с названием (например, "US 101").
                intersection – указывает крупные перекрестки, как правило, пересечения двух крупных дорог.
                political – указывает политическую единицу. Чаще всего такой тип используется для обозначения некоторых административных объектов.
                country – указывает государственную политическую единицу и обычно представляет собой тип наивысшего порядка, который возвращается геокодировщиком.
                administrative_area_level_1 – указывает гражданскую единицу первого порядка ниже уровня страны. В США такими административными уровнями являются штаты. Эти административные уровни используются не во всех странах.
                administrative_area_level_2 – указывает гражданскую единицу второго порядка ниже уровня страны. В США такими административными уровнями являются округи. Эти административные уровни используются не во всех странах.
                administrative_area_level_3 – указывает гражданскую единицу третьего порядка ниже уровня страны. Такой тип представляет меньшее административное подразделение. Эти административные уровни используются не во всех странах.
                administrative_area_level_4 – указывает гражданскую единицу четвертого порядка ниже уровня страны. Такой тип представляет меньшее административное подразделение. Эти административные уровни используются не во всех странах.
                administrative_area_level_5 – указывает гражданскую единицу пятого порядка ниже уровня страны. Такой тип представляет меньшее административное подразделение. Эти административные уровни используются не во всех странах.
                colloquial_area – указывает общепринятое альтернативное название единицы.
                locality – указывает политическую единицу в составе города.
                ward – указывает определенный тип округа в Японии, чтобы установить различие между несколькими частями населенного пункта в японском адресе.
                sublocality – указывает гражданскую единицу первого порядка ниже уровня населенного пункта. Для некоторых местоположений возможно предоставление одного из дополнительных типов: от sublocality_level_1 до sublocality_level_5. Каждый уровень ниже населенного пункта является гражданской единицей. Большее значение указывает меньшую географическую область.
                neighborhood – указывает именованный район.
                premise – указывает именованное местоположение, обычно одно или несколько зданий с общепринятым названием.
                subpremise – указывает единицу первого порядка ниже именованного местоположения, обычно одно здание в границах комплекса зданий с общепринятым названием.
                postal_code – указывает почтовый индекс в том виде, в котором он используется в стране для обработки почты.
                natural_feature – указывает важный природный объект.
                airport – указывает аэропорт.
                park – указывает парк с названием.
                point_of_interest – указывает достопримечательность с названием. Как правило, такие достопримечательности являются важными местными единицами, которые не подходят для других категорий, например, небоскреб "Эмпайр-стейт-билдинг" или статуя Свободы.

             */

            geocoderLocation();
            /*resultsMap.setCenter(latlng);
            var marker = new google.maps.Marker({   //устанавливаем маркер
              map: resultsMap,
              position: results[0].geometry.location
            });*/
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }

    //autocomplete.bindTo('bounds', map);   //показывает автозапалнение относительно границ отображаемой части карты
    autocomplete.addListener('place_changed', function(){
        //map.setCenter(new google.maps.LatLng(autocomplete.getPlace().geometry.location.lat(),autocomplete.getPlace().geometry.location.lng()));
        //setCoords(autocomplete.getPlace().geometry.location.lat(),autocomplete.getPlace().geometry.location.lng());

        latlng = new google.maps.LatLng(autocomplete.getPlace().geometry.location.lat(),autocomplete.getPlace().geometry.location.lng());
        //console.log(latlng.lat(),latlng.lng());
        geocoderLocation();
    });
    
    initMap();

    //загружаем карту там где указано значение... TODO что бы не создавало объект каждый раз а лишь меняло параметры

    $('#address').change(function(){
        console.log(map);
        geocodeAddress(geocoder, map);
    });

    /*google.maps.event.addListener(map, "dragend", function(event){
        setCoords(map.getCenter().lat(),map.getCenter().lng());
    });*/



});