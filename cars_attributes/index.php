<?php
/*
Plugin Name: Cars attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store cars attributes such as model, year, brand, color, accessories, and so on.
Version: 2.1.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: cars_plugin
Plugin update URI: http://www.osclass.org/files/plugins/cars_attributes/update.php
*/

    // Adds some plugin-specific search conditions
    function cars_search_conditions($params) {
        // we need conditions and search tables (only if we're using our custom tables)
        $has_conditions = false;
        foreach($params as $key => $value) {

            // We may want to  have param-specific searches 
            switch($key) {
                case 'type':
                    if($value!='') {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_vehicle_type_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;

                case 'make':
                    if($value!='') {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_i_make_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;

                case 'model':
                    if($value!='') {
                        Search::newInstance()->addConditions(sprintf("%st_item_car_attr.fk_i_model_id = %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                    }
                    break;

                case 'transmission':
                    if($value=='AUTO' || $value=='MANUAL') {
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
        // Insert here the code you want to execute after the plugin's install
        // for example you might want to create a table or modify some values

        // In this case we'll create a table to store the Example attributes
        $conn = getConnection() ;
        $conn->autocommit(false) ;
        try {
            $pathStruct    = osc_plugin_resource('cars_attributes/struct.sql');
            $sqlStruct     = file_get_contents($pathStruct);
            $pathBasicData = osc_plugin_resource('cars_attributes/basic_data.sql');
            $sqlBasicData  =file_get_contents($pathBasicData);
            $conn->osc_dbImportSQL($sqlStruct);
            $conn->osc_dbImportSQL($sqlBasicData);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function cars_call_after_uninstall() {
        // Insert here the code you want to execute after the plugin's uninstall
        // for example you might want to drop/remove a table or modify some values

        // In this case we'll remove the table we created to store Example attributes
        $conn = getConnection() ;
        $conn->autocommit(false);
        try {
            $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'cars_plugin'", DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_item_car_attr', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_item_car_model_attr', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_item_car_make_attr', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_item_car_vehicle_type_attr', DB_TABLE_PREFIX);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function cars_form($catId = '') {
        $conn = getConnection() ;
        // We received the categoryID
        if($catId == '') {
            return false;
        }
        
        // We check if the category is the same as our plugin
        if(osc_is_this_category('cars_plugin', $catId)) {
            $makes = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_make_attr ORDER BY s_name ASC', DB_TABLE_PREFIX);
            $data  = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_vehicle_type_attr', DB_TABLE_PREFIX);
            $car_types = array();
            foreach($data as $d) {
                $car_types[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
            }
            unset($data);
            $models = array();
            if(Session::newInstance()->_getForm('pc_make') != '') {
                $models = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_model_attr WHERE `fk_i_make_id` = %d ORDER BY s_name ASC', DB_TABLE_PREFIX, Session::newInstance()->_getForm('pc_make') );
            }
            require_once 'item_edit.php';
        }
    }

    function cars_search_form($catId = null) {
        // We received the categoryID
        if($catId == null) {
            return false;
        }
        
        // We check if the category is the same as our plugin
        foreach($catId as $id) {
            if(osc_is_this_category('cars_plugin', $id)) {
                include_once 'search_form.php';
                break;
            }
        }
    }

    function cars_form_post($catId = null, $item_id = null) {
        $conn = getConnection() ;
        // We received the categoryID and the Item ID
        if($catId == null) {
            return false;
        }
        
        // We check if the category is the same as our plugin
        if(osc_is_this_category('cars_plugin', $catId) && $item_id!=null) {
            $make   = (Params::getParam("make") == '')  ? DB_CONST_NULL : Params::getParam("make");
            $model  = (Params::getParam("model") == '') ? DB_CONST_NULL : Params::getParam("model");
            $type   = (Params::getParam("car_type") == '') ? 1 : Params::getParam("car_type");
            // Insert the data in our plugin's table
            $conn->osc_dbExec("INSERT INTO %st_item_car_attr (fk_i_item_id, i_year, i_doors, i_seats, i_mileage, i_engine_size, i_num_airbags, e_transmission, e_fuel, e_seller, b_warranty, b_new, i_power, e_power_unit, i_gears, fk_i_make_id, fk_i_model_id, fk_vehicle_type_id) VALUES (%d, %s, %d, %d, %s, %s, %d, '%s', '%s', '%s', %d, %d, %s, '%s', %d, %s, %s, %s)",
                                            DB_TABLE_PREFIX,
                                            $item_id,
                                            (Params::getParam("year") == '') ? DB_CONST_NULL : Params::getParam("year"),
                                            Params::getParam("doors"),
                                            Params::getParam("seats"),
                                            (Params::getParam("mileage") == '') ? DB_CONST_NULL : Params::getParam("mileage"),
                                            (Params::getParam("engine_size") == '') ? DB_CONST_NULL : Params::getParam("engine_size"),
                                            Params::getParam("num_airbags"),
                                            Params::getParam("transmission"),
                                            Params::getParam("fuel"),
                                            Params::getParam("seller"),
                                            (Params::getParam("warranty")!='') ? 1 : 0,
                                            (Params::getParam("new")!='') ? 1 : 0,
                                            (Params::getParam("power") == '') ? DB_CONST_NULL : Params::getParam("power"),
                                            Params::getParam("power_unit"),
                                            Params::getParam("gears"),
                                            $make,
                                            $model,
                                            $type
                              );
        }
    }

    // Self-explanatory
    function cars_item_detail() {
        $conn = getConnection() ;
        if(osc_is_this_category('cars_plugin', osc_item_category_id())) {
            $detail   = $conn->osc_dbFetchResult("SELECT * FROM %st_item_car_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
            $make     = $conn->osc_dbFetchResult('SELECT * FROM %st_item_car_make_attr WHERE pk_i_id = %d', DB_TABLE_PREFIX, $detail['fk_i_make_id']);
            $model    = $conn->osc_dbFetchResult('SELECT * FROM %st_item_car_model_attr WHERE pk_i_id = %d', DB_TABLE_PREFIX, $detail['fk_i_model_id']);
            $car_type = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_vehicle_type_attr WHERE pk_i_id = %d', DB_TABLE_PREFIX, $detail['fk_vehicle_type_id']);
            $detail['s_make']  = $make['s_name'];
            $detail['s_model'] = $model['s_name'];
            $detail['locale']  = array();
            foreach ($car_type as $c) {
                $detail['locale'][$c['fk_c_locale_code']]['s_car_type'] = $c['s_name'];
            }
            require_once 'item_detail.php';
        }
    }


    // Self-explanatory
    function cars_item_edit($catId = null, $item_id = null) {
        $conn = getConnection() ;
        if(osc_is_this_category('cars_plugin', $catId)) {
            $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_car_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);

            $makes = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_make_attr ORDER BY s_name ASC', DB_TABLE_PREFIX);
            $models = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_model_attr WHERE `fk_i_make_id` = %d ORDER BY s_name ASC', DB_TABLE_PREFIX, $detail['fk_i_make_id']);
            $data = $conn->osc_dbFetchResults('SELECT * FROM %st_item_car_vehicle_type_attr', DB_TABLE_PREFIX);
            $car_types = array();
            foreach($data as $d) {
                $car_types[$d['fk_c_locale_code']][$d['pk_i_id']] = $d['s_name'];
            }
            unset($data);
            require_once 'item_edit.php';
        }
    }

    function cars_item_edit_post($catId = null, $item_id = null) {
        // We received the categoryID and the Item ID
        if($catId == null) {
            return false;
        }

        // We check if the category is the same as our plugin
        if(osc_is_this_category('cars_plugin', $catId)) {
            $conn = getConnection() ;
            // Insert the data in our plugin's table
            $conn->osc_dbExec("REPLACE INTO %st_item_car_attr (fk_i_item_id, i_year, i_doors, i_seats, i_mileage, i_engine_size, i_num_airbags, e_transmission, e_fuel, e_seller, b_warranty, b_new, i_power, e_power_unit, i_gears, fk_i_make_id, fk_i_model_id, fk_vehicle_type_id) VALUES (%d, %s, %d, %d, %s, %s, %d, '%s', '%s', '%s', %d, %d, %s, '%s', %d, %s, %s, %d)",
                        DB_TABLE_PREFIX,
                        $item_id,
                        (Params::getParam("year") == '') ? DB_CONST_NULL : Params::getParam("year"),
                        Params::getParam("doors"),
                        Params::getParam("seats"),
                        (Params::getParam("mileage") == '') ? DB_CONST_NULL : Params::getParam("mileage"),
                        (Params::getParam("engine_size") == '') ? DB_CONST_NULL : Params::getParam("engine_size"),
                        Params::getParam("num_airbags"),
                        Params::getParam("transmission"),
                        Params::getParam("fuel"),
                        Params::getParam("seller"),
                        (Params::getParam("warranty")!='') ? 1 : 0,
                        (Params::getParam("new")!='') ? 1 : 0,
                        (Params::getParam("power") == '') ? DB_CONST_NULL : Params::getParam("power"),
                        Params::getParam("power_unit"),
                        Params::getParam("gears"),
                        (Params::getParam("make") == '') ? DB_CONST_NULL : Params::getParam("make"),
                        (Params::getParam("model") == '') ? DB_CONST_NULL : Params::getParam("model"),
                        Params::getParam("car_type")
                );
        }
    }

    function cars_admin_menu() {
        echo '<h3><a href="#">Cars plugin</a></h3>
        <ul> 
            <li><a href="'.osc_admin_configure_plugin_url("cars_attributes/index.php").'">&raquo; '.__('Configure plugin', 'cars_attributes').'</a></li>
            <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=makes">&raquo; '.__('Manage makes', 'cars_attributes').'</a></li>
            <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=models">&raquo; '.__('Manage models', 'cars_attributes').'</a></li>
            <li><a href="'.osc_admin_render_plugin_url("cars_attributes/conf.php").'?section=types">&raquo; '.__('Manage vehicle types', 'cars_attributes').'</a></li>
        </ul>';
    }

    function cars_delete_locale($locale) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_item_car_vehicle_type_attr WHERE fk_c_locale_code = '" . $locale . "'", DB_TABLE_PREFIX);
    }

    function cars_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_item_car_attr WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
    }

    function cars_admin_configuration() {
        // Standard configuration page for plugin which extend item's attributes
        osc_plugin_configure_view(osc_plugin_path(__FILE__));
    }

    function cars_pre_item_post() {
        $warranty = (Params::getParam("warranty")!='') ? 1 : 0 ;
        $new      = (Params::getParam("new")!='') ? 1 : 0 ;

        Session::newInstance()->_setForm('pc_year' ,Params::getParam("year") );
        Session::newInstance()->_setForm('pc_doors' ,Params::getParam("doors") );
        Session::newInstance()->_setForm('pc_seats' ,Params::getParam("seats") );
        Session::newInstance()->_setForm('pc_mileage' ,Params::getParam("mileage") );
        Session::newInstance()->_setForm('pc_engine_size' ,Params::getParam("engine_size") );
        Session::newInstance()->_setForm('pc_num_airbags' ,Params::getParam("num_airbags") );
        Session::newInstance()->_setForm('pc_transmission' ,Params::getParam("transmission") );
        Session::newInstance()->_setForm('pc_fuel' ,Params::getParam("fuel") );
        Session::newInstance()->_setForm('pc_seller' ,Params::getParam("seller") );
        Session::newInstance()->_setForm('pc_warranty' , $warranty );
        Session::newInstance()->_setForm('pc_new' , $new );
        Session::newInstance()->_setForm('pc_power' ,Params::getParam("power") );
        Session::newInstance()->_setForm('pc_power_unit' ,Params::getParam("power_unit") );
        Session::newInstance()->_setForm('pc_gears' ,Params::getParam("gears") );
        Session::newInstance()->_setForm('pc_make' ,Params::getParam("make") );
        Session::newInstance()->_setForm('pc_model' ,Params::getParam("model") );
        Session::newInstance()->_setForm('pc_car_type' ,Params::getParam("car_type") );
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

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'cars_call_after_install');
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'cars_admin_configuration');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'cars_call_after_uninstall');

    // When publishing an item we show an extra form with more attributes
    osc_add_hook('item_form', 'cars_form');
    // To add that new information to our custom table
    osc_add_hook('item_form_post', 'cars_form_post');

    // When searching, display an extra form with our plugin's fields
    osc_add_hook('search_form', 'cars_search_form');
    // When searching, add some conditions
    osc_add_hook('search_conditions', 'cars_search_conditions');

    // Show an item special attributes
    osc_add_hook('item_detail', 'cars_item_detail');

    // Edit an item special attributes
    osc_add_hook('item_edit', 'cars_item_edit');
    // Edit an item special attributes POST
    osc_add_hook('item_edit_post', 'cars_item_edit_post');

    //
    osc_add_hook('admin_menu', 'cars_admin_menu');

    //Delete locale
    osc_add_hook('delete_locale', 'cars_delete_locale');
    //Delete item
    osc_add_hook('delete_item', 'cars_delete_item');

    // previous to insert item
    osc_add_hook('pre_item_post', 'cars_pre_item_post') ;
?>