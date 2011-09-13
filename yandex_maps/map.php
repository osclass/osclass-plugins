<script src="http://api-maps.yandex.ru/1.1/index.xml?key=<?php echo osc_yandex_map_key() ; ?>" type="text/javascript"></script>
<div style="width: 100%; float:left; margin:20px 0 10px 0px">
    <div id="itemMap" style="width: 100%; height: 300px; width: 600px;"></div>
</div>
<script type="text/javascript"> 
<?php
    $addr = array();
    if( ($item['s_zip'] != '') && ($item['s_zip'] != null) ) { $addr[] = $item['s_zip'] ; }
    if( ($item['s_country'] != '') && ($item['s_country'] != null) ) { $addr[] = $item['s_country'] ; }
    if( ($item['s_region'] != '') && ($item['s_region'] != null) ) { $addr[] = $item['s_region'] ; }
    if( ($item['s_city'] != '') && ($item['s_city'] != null) ) { $addr[] = $item['s_city'] ; }
    if( ($item['s_address'] != '') && ($item['s_address'] != null) ) { $addr[] = $item['s_address'] ; }
    $address = implode(", ", $addr) ;
?>        

    YMaps.jQuery(function () {
        // Создание экземпляра карты и его привязка к созданному контейнеру
        map = new YMaps.Map(YMaps.jQuery("#itemMap")[0]) ;

        // Результат поиска
        var geoResultfound = false ;

        // Установка для карты ее центра и масштаба
        map.setCenter(new YMaps.GeoPoint(28.738031,60.713432), 10) ;

        // Добавление элементов управления
        map.addControl(new YMaps.TypeControl()) ;

        map.addControl(new YMaps.Zoom()) ;

        function showAddress (value) {
            // Удаление предыдущего результата поиска
            if (geoResultfound) {
                map.removeOverlay(geoResult) ;
            }

            // Запуск процесса геокодирования
            var geocoder = new YMaps.Geocoder(value, {results: 1, boundedBy: map.getBounds()}) ;

            // Создание обработчика для успешного завершения геокодирования
            YMaps.Events.observe(geocoder, geocoder.Events.Load, function () {
                // Если объект был найден, то добавляем его на карту
                // и центрируем карту по области обзора найденного объекта
                if (this.length()) {
                    geoResult = this.get(0);
                    map.addOverlay(geoResult);
                    map.setBounds(geoResult.getBounds());
                    geoResultfound=true;
                } else {
                    console.log('<?php __("Nothing found", "yandex_maps") ; ?>');
                }
            });

            // Процесс геокодирования завершен неудачно
            YMaps.Events.observe(geocoder, geocoder.Events.Fault, function (geocoder, error) {
                console.log('<?php _e("Error occured", "yandex_maps") ; ?>: ' + error);
            })
        }
        
        showAddress('<?php echo $address; ?>') ;
     });
</script>