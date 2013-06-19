<?php
/*
Plugin Name: Dating attributes
Plugin URI: http://www.osclass.org/
Description: This plugin extends a category of items to store dating attributes such as gender you're looking for and the type of relation.
Version: 3.0.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: dating_plugin
Plugin update URI: dating-attributes
*/

// Adds some plugin-specific search conditions
function dating_search_conditions($params) {
    // we need conditions and search tables (only if we're using our custom tables)
        $has_conditions = false;
        foreach($params as $key => $value) {
            // We may want to  have param-specific searches
            switch($key) {
                case 'genderFrom':
                    Search::newInstance()->addConditions(sprintf("%st_item_dating_attr.e_gender_to = '%s'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                case 'genderTo':
                    Search::newInstance()->addConditions(sprintf("%st_item_dating_attr.e_gender_from = '%s'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                case 'relation':
                    Search::newInstance()->addConditions(sprintf("%st_item_dating_attr.e_relation = '%s'", DB_TABLE_PREFIX, $value));
                    $has_conditions = true;
                    break;
                default:
                    break;
            }
        }
        
        // Only if we have some values at the params we add our table and link with the ID of the item.
        if($has_conditions) {
            Search::newInstance()->addConditions(sprintf("%st_item.pk_i_id = %st_item_dating_attr.fk_i_item_id ", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
            Search::newInstance()->addTable(sprintf("%st_item_dating_attr", DB_TABLE_PREFIX));
        }
    
}

function dating_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values

    // In this case we'll create a table to store the Example attributes
    $connection = DBConnectionClass::newInstance() ;
    $var = $connection->getOsclassDb();
    $conn       = new DBCommandClass( $var ) ;

    $path = osc_plugin_resource('dating_attributes/struct.sql');
    $sql = file_get_contents($path);
    
    if(! $conn->importSQL($sql) ){
        throw new Exception( $conn->getErrorLevel().' - '.$conn->getErrorDesc() ) ;
    }
}

function dating_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values
	
    // In this case we'll remove the table we created to store Example attributes
    $connection = DBConnectionClass::newInstance() ;
    $var = $connection->getOsclassDb();
    $conn       = new DBCommandClass( $var ) ;

    $conn->query('DROP TABLE '.DB_TABLE_PREFIX.'t_item_dating_attr') ;
    
    $error_num = $conn->getErrorLevel() ;
    
    if( $error_num > 0 ) {
        throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
    }
}

function dating_form($catId = '') {
    // We received the categoryID
    if($catId!="") {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('dating_plugin', $catId)) {
            require_once 'item_edit.php';
        }
    }
}

function dating_search_form($catId = null) {
    // We received the categoryID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        foreach($catId as $id) {
    		if(osc_is_this_category('dating_plugin', $id)) {
                    include_once 'search_form.php';
                    break;
	    	}
        }
    }
}

function dating_form_post($item) {
    $catId = isset($item['fk_i_category_id'])?$item['fk_i_category_id']:null;
    $item_id = isset($item['pk_i_id'])?$item['pk_i_id']:null;
    // We received the categoryID and the Item ID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('dating_plugin', $catId) && $item_id!=null) {
            // Insert the data in our plugin's table
            $connection = DBConnectionClass::newInstance() ;
            $var = $connection->getOsclassDb();
            $conn       = new DBCommandClass( $var ) ;

            $sql = sprintf("INSERT INTO %st_item_dating_attr (fk_i_item_id, e_gender_from, e_gender_to, e_relation) VALUES (%d, '%s', '%s', '%s')", 
                            DB_TABLE_PREFIX, $item_id, Params::getParam('genderFrom'), Params::getParam('genderTo'), Params::getParam('relation'));
            $conn->query($sql) ;

            $error_num = $conn->getErrorLevel() ;
            if( $error_num > 0 ) {
                throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
            }
        }
    }
}

// Self-explanatory
function dating_item_detail() {
    if(osc_is_this_category('dating_plugin', osc_item_category_id())) {
        $dating_array = array(
            'NI'         => __('Not informed', 'dating_attributes'),
            'MAN'        => __('Man', 'dating_attributes'),
            'WOMAN'      => __('Woman', 'dating_attributes'),
            'FRIENDSHIP' => __('Friendship', 'dating_attributes'),
            'FORMAL'     => __('Formal relation', 'dating_attributes'),
            'INFORMAL'   => __('Informal relation', 'dating_attributes')
        );
        
        $connection = DBConnectionClass::newInstance() ;
        $var = $connection->getOsclassDb();
        $conn       = new DBCommandClass( $var ) ;

        $sql = sprintf("SELECT * FROM %st_item_dating_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());

        $result = $conn->query($sql) ;
        $detail = $result->row();

        $error_num = $conn->getErrorLevel() ;
        if( $error_num > 0 ) {
            throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
        }

        $detail['e_gender_from'] = $dating_array[ $detail['e_gender_from'] ];
        $detail['e_gender_to']   = $dating_array[ $detail['e_gender_to'] ];
        $detail['e_relation']    = $dating_array[ $detail['e_relation'] ];

        if($detail['e_gender_from'] != '' && $detail['e_gender_to'] != '' && $detail['e_relation'] != '') {
            require_once 'item_detail.php';
        }
    }
}

// Self-explanatory
function dating_item_edit($catId = null, $item_id = null) {
    if(osc_is_this_category('dating_plugin', $catId)) {
        $connection = DBConnectionClass::newInstance() ;
        $var = $connection->getOsclassDb();
        $conn       = new DBCommandClass( $var ) ;

        $sql = sprintf("SELECT * FROM %st_item_dating_attr WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);

        $result = $conn->query($sql) ;
        $detail = $result->row();
        
        $error_num = $conn->getErrorLevel() ;
        if( $error_num > 0 ) {
            throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
        }

        if( isset($detail['fk_i_item_id']) ) {
            include_once 'item_edit.php';
        }
    }
}

function dating_item_edit_post($item) {
    $catId = isset($item['fk_i_category_id'])?$item['fk_i_category_id']:null;
    $item_id = isset($item['pk_i_id'])?$item['pk_i_id']:null;
    // We received the categoryID and the Item ID
    if($catId!=null) {
        // We check if the category is the same as our plugin
        if(osc_is_this_category('dating_plugin', $catId) && $item_id!=null) {
            $connection = DBConnectionClass::newInstance() ;
            $var = $connection->getOsclassDb();
            $conn       = new DBCommandClass( $var ) ;

            $sql = sprintf("REPLACE INTO %st_item_dating_attr (fk_i_item_id, e_gender_from, e_gender_to, e_relation) VALUES(%d, '%s', '%s', '%s')", DB_TABLE_PREFIX, $item_id, Params::getParam('genderFrom'), Params::getParam('genderTo'), Params::getParam('relation') );

            $conn->query($sql);

            $error_num = $conn->getErrorLevel() ;
            if( $error_num > 0 ) {
                throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
            }
        }
    }
}

function dating_delete_item($item) {
    
    $connection = DBConnectionClass::newInstance() ;
    $var = $connection->getOsclassDb();
    $conn       = new DBCommandClass( $var ) ;

    $sql = sprintf("DELETE FROM %st_item_dating_attr WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
    $conn->query($sql) ;

    $error_num = $conn->getErrorLevel() ;
    if( $error_num > 0 ) {
        throw new Exception($conn->getErrorLevel().' - '.$conn->getErrorDesc());
    }
}


function dating_admin_configuration() {
    // Standard configuration page for plugin which extend item's attributes
    osc_plugin_configure_view(osc_plugin_path(__FILE__));
}

function datting_pre_item_post() {

    Session::newInstance()->_setForm('pd_genderFrom' , Params::getParam('genderFrom'));
    Session::newInstance()->_setForm('pd_genderTo'   , Params::getParam('genderTo'));
    Session::newInstance()->_setForm('pd_relation'   , Params::getParam('relation'));
    // keep values on session
    Session::newInstance()->_keepForm('pd_genderFrom');
    Session::newInstance()->_keepForm('pd_genderTo');
    Session::newInstance()->_keepForm('pd_relation');
}

osc_register_plugin(osc_plugin_path(__FILE__), 'dating_call_after_install');
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'dating_admin_configuration');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'dating_call_after_uninstall');

osc_add_hook('item_form', 'dating_form');
osc_add_hook('posted_item', 'dating_form_post');
osc_add_hook('search_form', 'dating_search_form');
osc_add_hook('search_conditions', 'dating_search_conditions');
osc_add_hook('item_detail', 'dating_item_detail');
osc_add_hook('item_edit', 'dating_item_edit');
osc_add_hook('edited_item', 'dating_item_edit_post');
osc_add_hook('delete_item', 'dating_delete_item');
osc_add_hook('pre_item_post', 'datting_pre_item_post') ;

?>
