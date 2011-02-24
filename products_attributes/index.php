<?php
/*
Plugin Name: Products attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store products attributes such as make, model and so on.
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: products_plugin
Plugin update URI: http://www.osclass.org/files/plugins/products_attributes/update.php
*/

// Adds some plugin-specific search conditions
function products_search_conditions($params) {
    // we need conditions and search tables (only if we're using our custom tables)
        $has_conditions = false;

        foreach($params as $key => $value) {
            // We may want to  have param-specific searches
            switch($key) {
                case 'make':
                    Search::newInstance()->addConditions(sprintf("%st_item_products_attr.s_make = '%%%s%%'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                case 'model':
                    Search::newInstance()->addConditions(sprintf("%st_item_products_attr.s_model = '%%%s$$'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                default:
                    break;
            }
        }

        // Only if we have some values at the params we add our table and link with the ID of the item.
        if($has_conditions) {
            Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_products_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_house_attr", DB_TABLE_PREFIX));
        }
}

function products_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values

    // In this case we'll create a table to store the Example attributes
    $conn = getConnection() ;
    $conn->autocommit(false);
    try {
        $path = osc_plugin_resource('products_attributes/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function products_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values
	
    // In this case we'll remove the table we created to store Example attributes
    $conn = getConnection() ;
    $conn->autocommit(false);
    try {
        $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'products_plugin'", DB_TABLE_PREFIX);
        $conn->osc_dbExec('DROP TABLE %st_item_products_attr', DB_TABLE_PREFIX);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function products_form($catId = '') {
    // We received the categoryID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('products_plugin', $catId)) {
            include_once 'form.php';
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
                $conn = getConnection() ;
                $conn->osc_dbExec("INSERT INTO %st_item_products_attr (fk_i_item_id, s_make, s_model) VALUES (%d, '%s', '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('make'), Params::getParam('model') );
        }
    }
}

// Self-explanatory
function products_item_detail() {
    if(osc_is_this_category('products_plugin', osc_item_category_id())) {
        $conn = getConnection() ;
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_products_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
        if(isset($detail['fk_i_item_id'])) {
            include_once 'item_detail.php';
        }
    }
}

// Self-explanatory
function products_item_edit() {
    if(osc_is_this_category('products_plugin', osc_item_category_id())) {
        $conn = getConnection() ;
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_products_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
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
                    $conn = getConnection() ;
                    $conn->osc_dbExec("REPLACE INTO %st_item_products_attr (fk_i_item_id, s_make, s_model) VALUES (%d, '%s', '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('make'), Params::getParam('model') );
		}
	}
}

function products_delete_item($item) {
    $conn = getConnection();
    $conn->osc_dbExec("DELETE FROM %st_item_products_attr WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
}



function products_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(__FILE__);
}


// This is needed in order to be able to activate the plugin
osc_register_plugin(__FILE__, 'products_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(__FILE__."_configure", 'products_admin_configuration');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(__FILE__."_uninstall", 'products_call_after_uninstall');

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

?>
