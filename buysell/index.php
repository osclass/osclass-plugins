<?php
/*
Plugin Name: Buy/Sell type
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store a buy/sell type of the offer
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: buysell
*/

$buysell_types = array();
// ADD your own ads' types, VALUE should be a ONE WORD UPPERCASE used as a key
//$buysell_types['VALUE'] = __('Text to show', 'buysell');
$buysell_types['SELL'] = __('Sell', 'buysell');
$buysell_types['BUY'] = __('Buy', 'buysell');

View::newInstance()->_exportVariableToView("buysell_types", $buysell_types);

function buysell_search_conditions($params) {
        foreach($params as $key => $value) {
            if($key=="buysell_type") {
                if($value!='' && $value!='ALL') {
                    Search::newInstance()->addConditions(sprintf("%st_item_buysell.s_type = '%s'", DB_TABLE_PREFIX, $value));
                    Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_buysell.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
                    Search::newInstance()->addTable(sprintf("%st_item_buysell", DB_TABLE_PREFIX));
                }
            }
        }
}

function buysell_call_after_install() {
    $conn = getConnection() ;
    $conn->autocommit(false);
    try {
        $path = osc_plugin_resource('buysell/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function buysell_call_after_uninstall() {
    $conn = getConnection() ;
    $conn->autocommit(false);
    try {
        $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'buysell'", DB_TABLE_PREFIX);
        $conn->osc_dbExec('DROP TABLE %st_item_buysell', DB_TABLE_PREFIX);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function buysell_form($catId = '') {
    if($catId!="") {
        if(osc_is_this_category('buysell', $catId)) {
            include_once 'form.php';
        }
    }
}

function buysell_search_form($catId = null) {
	if($catId!=null) {
        foreach($catId as $id) {
    		if(osc_is_this_category('buysell', $id)) {
	    		include_once 'search_form.php';
	    		break;
	    	}
        }
	}
}


function buysell_form_post($catId = null, $item_id = null) {
    if($catId!=null) {
        if(osc_is_this_category('buysell', $catId)) {
                $conn = getConnection() ;
                $conn->osc_dbExec("INSERT INTO %st_item_buysell (fk_i_item_id, s_type) VALUES (%d, '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('buysell_type') );
        }
    }
}

function buysell_item_detail() {
    if(osc_is_this_category('buysell', osc_item_category_id())) {
        $conn = getConnection() ;
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_buysell WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
        if(isset($detail['fk_i_item_id'])) {
            include_once 'item_detail.php';
        }
    }
}

function buysell_item_edit($catId = null, $itemId = null) {
    if(osc_is_this_category('buysell', $catId)) {
        $conn = getConnection() ;
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_buysell WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $itemId);
        //if(isset($detail['fk_i_item_id'])) {
            include_once 'form.php';
        //}
    }
}

function buysell_item_edit_post($catId = null, $item_id = null) {
	if($catId!=null) {
		if(osc_is_this_category('buysell', $catId)) {
                    $conn = getConnection() ;
                    $conn->osc_dbExec("REPLACE INTO %st_item_buysell (fk_i_item_id, s_type) VALUES (%d, '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('buysell_type') );
		}
	}
}

function buysell_delete_item($item) {
    $conn = getConnection();
    $conn->osc_dbExec("DELETE FROM %st_item_buysell WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
}



function buysell_admin_configuration() {
    osc_plugin_configure_view(osc_plugin_path(__FILE__));
}


osc_register_plugin(osc_plugin_path(__FILE__), 'buysell_call_after_install');
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'buysell_admin_configuration');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'buysell_call_after_uninstall');

osc_add_hook('item_form', 'buysell_form', 0);
osc_add_hook('item_form_post', 'buysell_form_post');
osc_add_hook('search_form', 'buysell_search_form',0 );
osc_add_hook('search_conditions', 'buysell_search_conditions');
osc_add_hook('item_detail', 'buysell_item_detail', 0);
osc_add_hook('item_edit', 'buysell_item_edit', 0);
osc_add_hook('item_edit_post', 'buysell_item_edit_post');
osc_add_hook('delete_item', 'buysell_delete_item');

?>
