<?php
/*
Plugin Name: Products attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store products attributes such as make, model and so on.
Version: 3.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: products_plugin
Plugin update URI: products-attributes
*/

require_once 'ModelProducts.php';

// Adds some plugin-specific search conditions
function products_search_conditions($params) {
    // we need conditions and search tables (only if we're using our custom tables)
    $has_conditions = false;

    foreach($params as $key => $value) {
        // We may want to  have param-specific searches
        switch($key) {
            case 'make':
                Search::newInstance()->addConditions(sprintf("%st_item_products_attr.s_make LIKE '%s%%'", DB_TABLE_PREFIX, $value));
                $has_conditions = true;
                break;
            case 'model':
                Search::newInstance()->addConditions(sprintf("%st_item_products_attr.s_model LIKE '%s%%'", DB_TABLE_PREFIX, $value));
                $has_conditions = true;
                break;
            default:
                break;
        }
    }

    // Only if we have some values at the params we add our table and link with the ID of the item.
    if($has_conditions) {
        Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_products_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
        Search::newInstance()->addTable(sprintf("%st_item_products_attr", DB_TABLE_PREFIX));
    }
}

function products_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values

    // In this case we'll create a table to store the Example attributes
    ModelProducts::newInstance()->import('products_attributes/struct.sql');
}

function products_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values
	
    // In this case we'll remove the table we created to store Example attributes
    ModelProducts::newInstance()->uninstall();
}

function products_form($catId = '') {
    // We received the categoryID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('products_plugin', $catId)) {
            include_once 'item_edit.php';
        }
    }
}

function products_search_form($catId = null) {
    // We received the categoryID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        foreach($catId as $id) {
            if(osc_is_this_category('products_plugin', $id)) {
                include_once 'search_form.php';
                break;
            }
        }
    }
}


function products_form_post($catId = null, $item_id = null) {
    // We received the categoryID and the Item ID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('products_plugin', $catId)) {
            // Insert the data in our plugin's table
            ModelProducts::newInstance()->insertAttr($item_id, Params::getParam('make'), Params::getParam('model'));
        }
    }
}

// Self-explanatory
function products_item_detail() {
    if(osc_is_this_category('products_plugin', osc_item_category_id())) {
        $detail = ModelProducts::newInstance()->getAttrByItemId( osc_item_id() );
        if(isset($detail['fk_i_item_id'])) {
            include_once 'item_detail.php';
        }
    }
}

// Self-explanatory
function products_item_edit($catId = null, $item_id = null) {
    if(osc_is_this_category('products_plugin', $catId)) {
        $detail = ModelProducts::newInstance()->getAttrByItemId( $item_id );
        if(isset($detail['fk_i_item_id'])) {
            include_once 'item_edit.php';
        }
    }
}

function products_item_edit_post($catId = null, $item_id = null) {
    // We received the categoryID and the Item ID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('products_plugin', $catId)) {
            ModelProducts::newInstance()->updateAttr($item_id, Params::getParam('make'), Params::getParam('model'));
        }
    }
}

function products_delete_item($item_id) {
    ModelProducts::newInstance()->deleteItem($item_id) ;
}



function products_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__));
}

function products_pre_item_post() {
    Session::newInstance()->_setForm('pp_make' , Params::getParam('make'));
    Session::newInstance()->_setForm('pp_model'   , Params::getParam('model'));
    // keep values on session
    Session::newInstance()->_keepForm('pp_make' );
    Session::newInstance()->_keepForm('pp_model');
}

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'products_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'products_admin_configuration');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'products_call_after_uninstall');

// When publishing an item we show an extra form with more attributes
osc_add_hook('item_form', 'products_form');
// To add that new information to our custom table
osc_add_hook('item_form_post', 'products_form_post');

// When searching, display an extra form with our plugin's fields
osc_add_hook('search_form', 'products_search_form');
// When searching, add some conditions
osc_add_hook('search_conditions', 'products_search_conditions');

// Show an item special attributes
osc_add_hook('item_detail', 'products_item_detail');

// Edit an item special attributes
osc_add_hook('item_edit', 'products_item_edit');
// Edit an item special attributes POST
osc_add_hook('item_edit_post', 'products_item_edit_post');

//Delete item
osc_add_hook('delete_item', 'products_delete_item');

// previous to insert item
osc_add_hook('pre_item_post', 'products_pre_item_post') ;

?>
