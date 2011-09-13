<?php
/*
Plugin Name: Yandex Maps
Plugin URI: 
Description: This plugin shows a Yandex Map on the location space of every item.
Version: 1.0
Author: OSClass & pman
Author URI: 
Plugin update URI: 
*/

    function yandex_maps_location() {
        $item = osc_item() ;
        require 'map.php' ;
    }

    //adding api-key field in parameters
    function yandex_map_call_after_install() {
        $fields              = array() ;
        $fields["s_section"] = 'plugin-yandex_maps' ;
        $fields["s_name"]    = 'yandex_maps_key' ;
        $fields["e_type"]    = 'STRING' ;

        $conn = getConnection() ; 
        $conn->autocommit(true) ;
        Preference::newInstance()->insert($fields) ;
    }

    //remooving api-key
    function yanex_map_call_after_uninstall() {
        $conn = getConnection() ; 
        $conn->autocommit(true) ;
        Preference::newInstance()->delete( array("s_section" => "plugin-yandex_maps", "s_name" => "yandex_maps_key") ) ;
    }

    //HELPER
    function osc_yandex_map_key() {
        return osc_get_preference('yandex_maps_key', 'plugin-yandex_maps') ;
    }    

    //admin page
    function yandex_map_admin() {
        osc_admin_render_plugin('yandex_maps/admin.php') ;
    }

    function insert_geo_location($catId, $itemId) {
        $aItem = Item::newInstance()->findByPrimaryKey($itemId);
        $sAddress = (isset($aItem['s_address']) ? $aItem['s_address'] : '');
        $sRegion = (isset($aItem['s_region']) ? $aItem['s_region'] : '');
        $sCity = (isset($aItem['s_city']) ? $aItem['s_city'] : '');
        $address = sprintf('%s, %s %s', $sAddress, $sRegion, $sCity);
        $response = osc_file_get_contents(sprintf('http://maps.google.com/maps/geo?q=%s&output=json&sensor=false', urlencode($address)));
        $jsonResponse = json_decode($response);
        if (isset($jsonResponse->Placemark) && count($jsonResponse->Placemark[0]) > 0) {
            $coord = $jsonResponse->Placemark[0]->Point->coordinates;
            ItemLocation::newInstance()->update (array('d_coord_lat' => $coord[1]
                                                      ,'d_coord_long' => $coord[0])
                                                ,array('fk_i_item_id' => $itemId));
        }
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'yandex_map_call_after_install') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'yanex_map_call_after_uninstall') ;

    osc_add_hook('location', 'yandex_maps_location') ;
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'yandex_map_admin') ;
    osc_add_hook('item_form_post', 'insert_geo_location') ;
    osc_add_hook('item_edit_post', 'insert_geo_location') ;

?>