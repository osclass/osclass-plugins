<?php
/*
Plugin Name: Cars attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store cars attributes such as model, year, brand, color, accessories, and so on.
Version: 3.0.4
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: cars_plugin
Plugin update URI: cars-attributes
*/
    require_once 'ModelCars.php' ;

    // Adds some plugin-specific search conditions
    function cars_search_conditions($params) {
        // we need conditions and search tables (only if we're using our custom tables)
        $has_conditions = false ;
        foreach($params as $key => $value) {
            // we may want to have param-specific searches
            switch($key) {
                case 'type':
                    if( is_numeric($value) ) {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_vehicle_type_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                break;
                case 'make':
                    if( is_numeric($value) ) {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_i_make_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                break;
                case 'model':
                    if( is_numeric($value) ) {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_i_model_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                break;
                case 'transmission':
                    if( $value == 'AUTO' || $value == 'MANUAL' ) {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.e_transmission = '%s'", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                break;
                default:
                break;
            }
        }

        // Only if we have some values at the params we add our table and link with the ID of the item.
        if($has_conditions) {
            Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_car_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_car_attr", DB_TABLE_PREFIX));
        }
    }

    function cars_call_after_install() {
        // create a table to store the cars attributes
        ModelCars::newInstance()->import('cars_attributes/struct.sql');
        ModelCars::newInstance()->import('cars_attributes/basic_data.sql');
    }

    function cars_call_after_uninstall() {
        // remove the table we created to store cars attributes
        ModelCars::newInstance()->uninstall();
    }

    function cars_form($catID = '') {
        // We received the categoryID
        if($catID == '') {
            return false;
        }
        
        // check if the category is the same as our plugin
        if( osc_is_this_category('cars_plugin', $catID) ) {
            $makes = ModelCars::newInstance()->getCarMakes();
            $data  = ModelCars::newInstance()->getVehiclesType();
            $car_types = array();
            foreach($data as $d) {
                $car_types[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
            }
            unset($data);
            $models = array();
            if(Session::newInstance()->_getForm('pc_make') != '') {
                $models = ModelCars::newInstance()->getCarModels(Session::newInstance()->_getForm('pc_make'));
            }
            require_once 'item_edit.php';
        }
    }

    function cars_search_form($catID = null) {
        // we received the categoryID
        if($catID == null) {
            return false;
        }
        
        // we check if the category is the same as our plugin
        foreach($catID as $id) {
            if( osc_is_this_category('cars_plugin', $id) ) {
                include_once 'search_form.php';
                break;
            }
        }
    }

    function cars_form_post($item) {
        $catID = isset($item['fk_i_category_id'])?$item['fk_i_category_id']:null;
        $itemID = isset($item['pk_i_id'])?$item['pk_i_id']:null;
        // we received the categoryID and the Item ID
        if($catID == null) {
            return false;
        }
        
        // We check if the category is the same as our plugin
        if( osc_is_this_category('cars_plugin', $catID) && $itemID != null ) {
            $arrayInsert = _getCarParameters();
            // Insert the data in our plugin's table
            ModelCars::newInstance()->insertCarAttr($arrayInsert, $itemID);
        }
    }

    // self-explanatory
    function cars_item_detail() {
        if( osc_is_this_category('cars_plugin', osc_item_category_id()) ) {
            $detail   = ModelCars::newInstance()->getCarAttr(osc_item_id()) ;

            if( count($detail) == 0 ) {
                return ;
            }

            $make     = ModelCars::newInstance()->getCarMakeById( $detail['fk_i_make_id'] );
            $model    = ModelCars::newInstance()->getCarModelById( $detail['fk_i_model_id'] );
            $car_type = ModelCars::newInstance()->getVehicleTypeById($detail['fk_vehicle_type_id']);

            $detail['s_make'] = '' ;
            if( array_key_exists('s_name', $make) ) {
                $detail['s_make']  = $make['s_name'];
            }
            $detail['s_model'] = '' ;
            if( array_key_exists('s_name', $model) ) {
                $detail['s_model']  = $model['s_name'];
            }
            $detail['locale']  = array() ;
            foreach($car_type as $c) {
                $detail['locale'][$c['fk_c_locale_code']]['s_car_type'] = $c['s_name'] ;
            }

            require_once 'item_detail.php' ;
        }
    }

    // Self-explanatory
    function cars_item_edit($catID = null, $itemID = null) {
        if(osc_is_this_category('cars_plugin', $catID)) {
            $detail = ModelCars::newInstance()->getCarAttr($itemID);
            $makes  = ModelCars::newInstance()->getCarMakes();
            $models = array() ;
            if( array_key_exists('fk_i_make_id', $detail) ) {
                $models = ModelCars::newInstance()->getCarModels( $detail['fk_i_make_id'] );
            }
            $data   = ModelCars::newInstance()->getVehiclesType();
            
            $car_types = array();
            foreach($data as $d) {
                $car_types[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
            }
            unset($data);
            require_once 'item_edit.php';
        }
    }

    function cars_item_edit_post($item) {
        $catID = isset($item['fk_i_category_id'])?$item['fk_i_category_id']:null;
        $itemID = isset($item['pk_i_id'])?$item['pk_i_id']:null;
        // We received the categoryID and the Item ID
        if($catID == null) {
            return false;
        }

        // We check if the category is the same as our plugin
        if( osc_is_this_category('cars_plugin', $catID) ) {
            $arrayUpdate = _getCarParameters();
            ModelCars::newInstance()->updateCarAttr($arrayUpdate, $itemID);
        }
    }

    function cars_admin_menu() {
        if(osc_version()<320) {
            echo '<h3><a href="#">Cars plugin</a></h3>
            <ul>
                <li><a href="'.osc_admin_configure_plugin_url("cars_attributes/index.php").'">&raquo; '.__('Configure plugin', 'cars_attributes').'</a></li>
                <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=makes">&raquo; '.__('Manage makes', 'cars_attributes').'</a></li>
                <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=models">&raquo; '.__('Manage models', 'cars_attributes').'</a></li>
                <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=types">&raquo; '.__('Manage vehicle types', 'cars_attributes').'</a></li>
            </ul>';
        } else {
            osc_add_admin_submenu_divider('plugins', __('Cars plugin', 'cars_attributes'), 'cars_attributes_divider', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Configure plugin', 'cars_attributes'), osc_admin_configure_plugin_url("cars_attributes/index.php"), 'cars_attributes_settings', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Manage makes', 'cars_attributes'), osc_route_admin_url('cars-admin-conf', array('section' => 'makes')), 'cars_attributes_makes', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Manage models', 'cars_attributes'), osc_route_admin_url('cars-admin-conf', array('section' => 'models')), 'cars_attributes_models', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Manage vehicle types', 'cars_attributes'), osc_route_admin_url('cars-admin-conf', array('section' => 'types')), 'cars_attributes_types', 'administrator');
        }
    }

    function cars_delete_locale($locale) {
        ModelCars::newInstance()->deleteLocale($locale);
    }

    function cars_delete_item($item_id) {
        ModelCars::newInstance()->deleteCarAttr($item_id);
    }

    function cars_admin_configuration() {
        // standard configuration page for plugin which extend item's attributes
        osc_plugin_configure_view(osc_plugin_path(__FILE__));
    }

    function cars_pre_item_post() {
        $warranty = (Params::getParam("warranty") != '') ? 1 : 0 ;
        $new      = (Params::getParam("new") != '') ? 1 : 0 ;

        Session::newInstance()->_setForm('pc_year', Params::getParam("year"));
        Session::newInstance()->_setForm('pc_doors', Params::getParam("doors"));
        Session::newInstance()->_setForm('pc_seats', Params::getParam("seats"));
        Session::newInstance()->_setForm('pc_mileage', Params::getParam("mileage"));
        Session::newInstance()->_setForm('pc_engine_size', Params::getParam("engine_size"));
        Session::newInstance()->_setForm('pc_num_airbags', Params::getParam("num_airbags"));
        Session::newInstance()->_setForm('pc_transmission', Params::getParam("transmission"));
        Session::newInstance()->_setForm('pc_fuel', Params::getParam("fuel"));
        Session::newInstance()->_setForm('pc_seller', Params::getParam("seller"));
        Session::newInstance()->_setForm('pc_warranty', $warranty);
        Session::newInstance()->_setForm('pc_new', $new);
        Session::newInstance()->_setForm('pc_power', Params::getParam("power"));
        Session::newInstance()->_setForm('pc_power_unit', Params::getParam("power_unit"));
        Session::newInstance()->_setForm('pc_gears', Params::getParam("gears"));
        Session::newInstance()->_setForm('pc_make', Params::getParam("make"));
        Session::newInstance()->_setForm('pc_model', Params::getParam("model"));
        Session::newInstance()->_setForm('pc_car_type', Params::getParam("car_type"));
        // keep values on session
        Session::newInstance()->_keepForm('pc_year');
        Session::newInstance()->_keepForm('pc_doors');
        Session::newInstance()->_keepForm('pc_seats');
        Session::newInstance()->_keepForm('pc_mileage');
        Session::newInstance()->_keepForm('pc_engine_size');
        Session::newInstance()->_keepForm('pc_num_airbags');
        Session::newInstance()->_keepForm('pc_transmission');
        Session::newInstance()->_keepForm('pc_fuel');
        Session::newInstance()->_keepForm('pc_seller');
        Session::newInstance()->_keepForm('pc_warranty');
        Session::newInstance()->_keepForm('pc_new');
        Session::newInstance()->_keepForm('pc_power');
        Session::newInstance()->_keepForm('pc_power_unit');
        Session::newInstance()->_keepForm('pc_gears');
        Session::newInstance()->_keepForm('pc_make');
        Session::newInstance()->_keepForm('pc_model');
        Session::newInstance()->_keepForm('pc_car_type');
    }
    
    function _getCarParameters() {
        $make     = (Params::getParam("make") == '') ? null : Params::getParam("make");
        $model    = (Params::getParam("model") == '') ? null : Params::getParam("model");
        $type     = (Params::getParam("car_type") == '') ? 1 : Params::getParam("car_type");
        $power    = (Params::getParam("power") == '') ? null : Params::getParam("power");
        $mileage  = (Params::getParam("mileage") == '') ? null : Params::getParam("mileage");
        $e_size   = (Params::getParam("engine_size") == '') ? null : Params::getParam("engine_size");
        $year     = (Params::getParam("year") == '') ? null : Params::getParam("year");
        $warranty = (Params::getParam("warranty")!='') ? 1 : 0;
        $new      = (Params::getParam("new")!='') ? 1 : 0;
        
        $array = array(
            'doors'         => Params::getParam("doors"),
            'seats'         => Params::getParam("seats"),
            'year'          => $year,
            'mileage'       => $mileage,
            'engine_size'   => $e_size,
            'num_airbags'   => Params::getParam("num_airbags"),
            'transmission'  => Params::getParam("transmission"),
            'fuel'          => Params::getParam("fuel"),
            'seller'        => Params::getParam("seller"),
            'warranty'      => $warranty,
            'new'           => $new,
            'power'         => $power,
            'power_unit'    => Params::getParam("power_unit"),
            'gears'         => Params::getParam("gears"),
            'make'          => $make,
            'model'         => $model,
            'type'          => $type
        );

        return $array;
    }

    if(osc_version()>=320) {
        osc_add_route('cars-admin-conf', 'cars-conf/(.+)', 'cars-conf/{section}', osc_plugin_folder(__FILE__).'admin/conf.php');
    }
    
    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'cars_call_after_install');
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . "_configure", 'cars_admin_configuration');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'cars_call_after_uninstall');

    // When publishing an item we show an extra form with more attributes
    osc_add_hook('item_form', 'cars_form');
    // To add that new information to our custom table
    osc_add_hook('posted_item', 'cars_form_post');

    // When searching, display an extra form with our plugin's fields
    osc_add_hook('search_form', 'cars_search_form');
    // When searching, add some conditions
    osc_add_hook('search_conditions', 'cars_search_conditions');

    // show an item special attributes
    osc_add_hook('item_detail', 'cars_item_detail');

    // edit an item special attributes
    osc_add_hook('item_edit', 'cars_item_edit');
    // edit an item special attributes POST
    osc_add_hook('edited_item', 'cars_item_edit_post');

    if(osc_version()<320) {
        osc_add_hook('admin_menu', 'cars_admin_menu');
    } else {
        osc_add_hook('admin_menu_init', 'cars_admin_menu');
    }

    // delete locale
    osc_add_hook('delete_locale', 'cars_delete_locale');
    //delete item
    osc_add_hook('delete_item', 'cars_delete_item');
    // previous to insert item
    osc_add_hook('pre_item_post', 'cars_pre_item_post') ;

?>
