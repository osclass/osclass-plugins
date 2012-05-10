<?php
/*
Plugin Name: Real state attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store real estate attributes such as square feets, number of bathrooms, garage, and so on.
Version: 3.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: realestate_plugin
Plugin update URI: http://www.osclass.org/files/plugins/realestate_attributes/update.php
*/

require_once 'ModelRealEstate.php';
require_once 'helper.php';
// Adds some plugin-specific search conditions
function realestate_search_conditions($params = null) {

    // we need conditions and search tables (only if we're using our custom tables)
    if ($params!=null) {
        $has_conditions = false;
        foreach ($params as $key => $value) {
            if ($value != "") {
                // We may want to  have param-specific searches
                switch ($key) {
                    case 'numFloor':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.i_num_floors >= %d AND %st_item_house_attr.i_num_floors <= %d", DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'rooms':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.i_num_rooms >= %d AND %st_item_house_attr.i_num_rooms <= %d", DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'rooms_min':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.i_num_rooms >= %d", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'bathrooms':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.i_num_bathrooms >= %d AND %st_item_house_attr.i_num_bathrooms <= %d", DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'garages':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.i_num_garages >= %d AND %st_item_house_attr.i_num_garages <= %d", DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'year':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                            Search::newInstance()->addConditions(sprintf("(%st_item_house_attr.i_year = 0 || (%st_item_house_attr.i_year >= %d AND %st_item_house_attr.i_year <= %d))", DB_TABLE_PREFIX, DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'sq':
                        if (preg_match('|([0-9]+) - ([0-9]+)|', $value, $match)) {
                           Search::newInstance()->addConditions(sprintf("%st_item_house_attr.s_square_meters >= %d AND %st_item_house_attr.s_square_meters <= %d", DB_TABLE_PREFIX, $match[1], DB_TABLE_PREFIX, $match[2]));
                            $has_conditions = true;
                        }
                        break;
                    case 'heating':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_heating = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'airCondition':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_air_condition = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'elevator':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_elevator = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'terrace':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_terrace = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'parking':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_parking = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'furnished':
                        if ($value != "") {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_furnished = %d ", DB_TABLE_PREFIX, $value));
                            $has_conditions = true;
                        }
                        break;
                    case 'new':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_new = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'by_owner':
                        Search::newInstance()->addConditions(sprintf("%st_item_house_attr.b_by_owner = %d ", DB_TABLE_PREFIX, $value));
                        $has_conditions = true;
                        break;
                    case 'property_type':
                        if($value!='') {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.e_type = '%s' ", DB_TABLE_PREFIX, $value));
                            $has_conditions = true;
                        }
                        break;
                    case 'p_type':
                        if($value!='') {
                            Search::newInstance()->addConditions(sprintf("%st_item_house_attr.fk_i_property_type_id = %d ", DB_TABLE_PREFIX, $value));
                        }
                        $has_conditions = true;
                        break;
                    default:
                        break;
                }
            }
        }

        // Only if we have some values at the params we add our table and link with the ID of the item.
        if ($has_conditions) {
            Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_house_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_house_description_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_house_attr", DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_house_description_attr", DB_TABLE_PREFIX));
        }
    }
}

function realestate_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values
    
    // In this case we'll create a table to store the Example attributes
    ModelRealEstate::newInstance()->import('realestate_attributes/struct.sql') ;
}

function realestate_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values
    
    // In this case we'll remove the table we created to store Example attributes
    ModelRealEstate::newInstance()->uninstall();
}

function realestate_form($catId = null) {
    // We received the categoryID
    if ($catId!= null) {
        // We check if the category is the same as our plugin
        if (osc_is_this_category('realestate_plugin', $catId)) {
            $p_type = ModelRealEstate::newInstance()->getPropertyTypes() ;
            include_once 'item_edit.php';
        }
    }
}

function realestate_search_form($catId = null) {
    // We received the categoryID
    if ($catId!=null) {
        // We check if the category is the same as our plugin
        foreach($catId as $id) {
    		if(osc_is_this_category('realestate_plugin', $id)) {
                    $p_type = ModelRealEstate::newInstance()->getPropertyTypes() ;
                    include_once 'search_form.php';
                    break;
	    	}
        }
    }
}

/**
 * Get parameters from form
 * 
 * @return array
 */
function _getParameters()
{
    
    $heating        = Params::getParam('heating')!='' ? 1 : 0;
    $airCondition   = Params::getParam('airCondition')!='' ? 1 : 0;
    $elevator       = Params::getParam('elevator')!='' ? 1 : 0;
    $terrace        = Params::getParam('terrace')!='' ? 1 : 0;
    $parking        = Params::getParam('parking')!='' ? 1 : 0;
    $furnished      = Params::getParam('furnished')!='' ? 1 : 0;
    $new            = Params::getParam('new')!='' ? 1 : 0;
    $by_owner       = Params::getParam('by_owner')!='' ? 1 : 0;

    $insertArray = array(
        'squareMeters'  =>  Params::getParam('squareMeters'),
        'numRooms'      =>  Params::getParam('numRooms'),
        'numBathrooms'  =>  Params::getParam('numBathrooms'),
        'property_type' =>  Params::getParam('property_type'),
        'p_type'        =>  Params::getParam('p_type'),
        'status'        =>  Params::getParam('status'),
        'numFloors'     =>  Params::getParam('numFloors'),
        'numGarages'    =>  Params::getParam('numGarages'),
        'heating'       =>  $heating,
        'airCondition'  =>  $airCondition,
        'elevator'      =>  $elevator,
        'terrace'       =>  $terrace,
        'parking'       =>  $parking,
        'furnished'     =>  $furnished,
        'new'           =>  $new,
        'by_owner'      =>  $by_owner,
        'condition'     =>  Params::getParam('condition'),
        'year'          =>  Params::getParam('year'),
        'agency'        =>  Params::getParam('agency'),
        'floorNumber'   =>  Params::getParam('floorNumber'),
        'squareMetersTotal' => Params::getParam('squareMetersTotal')
    );
    return $insertArray;
}

/**
 * Prepare locales
 * 
 * @return array
 */
function _prepareLocales()
{
    $dataItem = array();
    foreach ($_REQUEST as $k => $v) {
        if (preg_match('|(.+?)#(.+)|', $k, $m)) {
            $dataItem[$m[1]][$m[2]] = $v;
        }
    }
    return $dataItem;
}

function realestate_form_post($catId = null, $item_id = null) {
    // We received the categoryID and the Item ID
    if ($catId!=null) {
        // We check if the category is the same as our plugin
        if (osc_is_this_category('realestate_plugin', $catId) && $item_id!=null) {
            
            // Insert the data in our plugin's table
            $insertArray = _getParameters();
            $insertArray['itemId'] = $item_id;
            ModelRealEstate::newInstance()->insertAttr($insertArray);
                
            // prepare locales
            $dataItem = _prepareLocales();

            // insert locales
            foreach ($dataItem as $k => $_data) {
                ModelRealEstate::newInstance()->insertDescriptions($item_id, $k, $_data['transport'], $_data['zone']) ;
            }
        }
    }
}

// Self-explanatory
function realestate_item_detail() {
    if (osc_is_this_category('realestate_plugin', osc_item_category_id()) && osc_get_preference('insertion','realestate_attributes') != 'manual') {
        realestate_attributes();
    }
}

// Self-explanatory
function realestate_item_edit($catId = null, $item_id = null) {
    if (osc_is_this_category('realestate_plugin', $catId)) {
        $detail = ModelRealEstate::newInstance()->getAttributes( $item_id );
        $p_type = ModelRealEstate::newInstance()->getPropertyTypes() ;
        require_once 'item_edit.php';
    }
}

function realestate_item_edit_post($catId = null, $item_id = null) {
    // We received the categoryID and the Item ID
    if ($catId!=null) {
        // We check if the category is the same as our plugin
        if (osc_is_this_category('realestate_plugin', $catId)) {
            $replaceArray = _getParameters();
            $replaceArray['itemId'] = $item_id;
            ModelRealEstate::newInstance()->replaceAttr($replaceArray) ;

            // prepare locales
            $dataItem = _prepareLocales();

            // insert locales
            foreach ($dataItem as $k => $_data) {
                ModelRealEstate::newInstance()->replaceDescriptions($item_id, $k, $_data['transport'], $_data['zone']) ;
            }
        }
    }
}

function realestate_delete_locale($locale) {
    ModelRealEstate::newInstance()->deleteLocale( $locale ) ;
}

function realestate_delete_item($item_id) {
    ModelRealEstate::newInstance()->deleteItem( $item_id ) ;
}



function realestate_admin_menu() {
    echo '<h3><a href="#">Realestate plugin</a></h3>
    <ul> 
        <li><a href="'.osc_admin_configure_plugin_url("realestate_attributes/index.php").'">&raquo; ' . __('Configure plugin', 'realestate_attributes') . '</a></li>
        <li><a href="'.osc_admin_render_plugin_url("realestate_attributes/conf.php").'?section=types">&raquo; ' . __('Property types', 'realestate_attributes') . '</a></li>
        <li><a href="'.osc_admin_render_plugin_url("realestate_attributes/view.php").'">&raquo; ' . __('View options', 'realestate_attributes') . '</a></li>
    </ul>';
}

function realestate_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__));
}

function realestate_pre_item_post() {
    $heating        = (Params::getParam('heating')!='') ? 1 : 0;
    $airCondition   = (Params::getParam('airCondition')!='') ? 1 : 0;
    $elevator       = (Params::getParam('elevator')!='') ? 1 : 0;
    $terrace        = (Params::getParam('terrace')!='') ? 1 : 0;
    $parking        = (Params::getParam('parking')!='') ? 1 : 0;
    $furnished      = (Params::getParam('furnished')!='') ? 1 : 0;
    $new            = (Params::getParam('new')!='') ? 1 : 0;
    $by_owner       = (Params::getParam('by_owner')!='') ? 1 : 0;

    Session::newInstance()->_setForm('pre_squareMeters'      , Params::getParam('squareMeters') );
    Session::newInstance()->_setForm('pre_numRooms'          , Params::getParam('numRooms') );
    Session::newInstance()->_setForm('pre_numBathrooms'      , Params::getParam('numBathrooms') );
    Session::newInstance()->_setForm('pre_property_type'     , Params::getParam('property_type') );
    Session::newInstance()->_setForm('pre_p_type'            , Params::getParam('p_type') );
    Session::newInstance()->_setForm('pre_status'            , Params::getParam('status') );
    Session::newInstance()->_setForm('pre_numFloors'         , Params::getParam('numFloors') );
    Session::newInstance()->_setForm('pre_numGarages'        , Params::getParam('numGarages') );
    Session::newInstance()->_setForm('pre_heating'           , $heating );
    Session::newInstance()->_setForm('pre_airCondition'      , $airCondition );
    Session::newInstance()->_setForm('pre_elevator'          , $elevator );
    Session::newInstance()->_setForm('pre_terrace'           , $terrace );
    Session::newInstance()->_setForm('pre_parking'           , $parking );
    Session::newInstance()->_setForm('pre_furnished'         , $furnished );
    Session::newInstance()->_setForm('pre_new'               , $new );
    Session::newInstance()->_setForm('pre_by_owner'          , $by_owner );
    Session::newInstance()->_setForm('pre_condition'         , Params::getParam('condition') );
    Session::newInstance()->_setForm('pre_year'              , Params::getParam('year') );
    Session::newInstance()->_setForm('pre_agency'            , Params::getParam('agency') );
    Session::newInstance()->_setForm('pre_floorNumber'       , Params::getParam('floorNumber') );
    Session::newInstance()->_setForm('pre_squareMetersTotal' , Params::getParam('squareMetersTotal') );

    $locales = osc_get_locales();
    foreach($locales as $locale) {
        Session::newInstance()->_setForm('pre_'.$locale['pk_c_code'].'transport' , Params::getParam($locale['pk_c_code'].'#transport') );
        Session::newInstance()->_setForm('pre_'.$locale['pk_c_code'].'zone' , Params::getParam($locale['pk_c_code'].'#zone') );
        Session::newInstance()->_keepForm('pre_'.$locale['pk_c_code'].'transport');
        Session::newInstance()->_keepForm('pre_'.$locale['pk_c_code'].'zone');
    }
    
    // keep values on session
    Session::newInstance()->_keepForm('pre_squareMeters');
    Session::newInstance()->_keepForm('pre_numRooms');
    Session::newInstance()->_keepForm('pre_numBathrooms');
    Session::newInstance()->_keepForm('pre_property_type');
    Session::newInstance()->_keepForm('pre_p_type');
    Session::newInstance()->_keepForm('pre_status');
    Session::newInstance()->_keepForm('pre_numFloors');
    Session::newInstance()->_keepForm('pre_numGarages');
    Session::newInstance()->_keepForm('pre_heating');
    Session::newInstance()->_keepForm('pre_airCondition');
    Session::newInstance()->_keepForm('pre_elevator');
    Session::newInstance()->_keepForm('pre_terrace');
    Session::newInstance()->_keepForm('pre_parking');
    Session::newInstance()->_keepForm('pre_furnished');
    Session::newInstance()->_keepForm('pre_new');
    Session::newInstance()->_keepForm('pre_by_owner');
    Session::newInstance()->_keepForm('pre_condition');
    Session::newInstance()->_keepForm('pre_year');
    Session::newInstance()->_keepForm('pre_agency');
    Session::newInstance()->_keepForm('pre_floorNumber');
    Session::newInstance()->_keepForm('pre_floorNumber');
    Session::newInstance()->_keepForm('pre_squareMetersTotal');
}

function realestate_item_style(){
    //osc_plugin_url(__FILE__).'img/
    echo "<link href=\"".osc_plugin_url(__FILE__)."/css/style.css\" rel=\"stylesheet\" type=\"text/css\" />";
}

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'realestate_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . "_configure", 'realestate_admin_configuration');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'realestate_call_after_uninstall');

// When publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'realestate_form');
// To add that new information to our custom table
osc_add_hook('item_form_post', 'realestate_form_post');

// When searching, display an extra form with our plugin's fields
osc_add_hook('search_form', 'realestate_search_form');
// When searching, add some conditions
osc_add_hook('search_conditions', 'realestate_search_conditions');

// Show an item special attributes
osc_add_hook('item_detail', 'realestate_item_detail');

// Edit an item special attributes
osc_add_hook('item_edit', 'realestate_item_edit');
// Edit an item special attributes POST
osc_add_hook('item_edit_post', 'realestate_item_edit_post');

osc_add_hook('admin_menu', 'realestate_admin_menu');

//Delete locale
osc_add_hook('delete_locale', 'realestate_delete_locale');
//Delete item
osc_add_hook('delete_item', 'realestate_delete_item');

// previous to insert item
osc_add_hook('pre_item_post', 'realestate_pre_item_post') ;

// Add styles
osc_add_hook('header', 'realestate_item_style');
?>