<?php
/*
Plugin Name: Embed youtuve videos
Plugin URI: http://www.osclass.org/
Description: This plugin extends the item to embed youtube videos.
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: youtube
Plugin update URI: 
*/

    function youtube_call_after_install() {
        // Insert here the code you want to execute after the plugin's install
        // for example you might want to create a table or modify some values
        
        $conn = getConnection();
        $path = osc_plugin_resource('youtube/struct.sql');
        $sql  = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
    }

    function youtube_call_after_uninstall() {
        // Insert here the code you want to execute after the plugin's uninstall
        // for example you might want to drop/remove a table or modify some values

        $conn = getConnection();
        $conn->osc_dbExec('DROP TABLE %st_item_youtube', DB_TABLE_PREFIX);
    }

    function youtube_form($catId = null) {
        require_once 'item_form.php';
    }

    function youtube_form_post($catId = null, $item_id = null)  {
        // We received the categoryID and the Item ID
        $youtube_video = addslashes(Params::getParam('s_youtube'));
        $youtube_video = convert_youtube_url($youtube_video);
        if( empty($youtube_video) ) return false;
        
        $conn = getConnection();
        $conn->osc_dbExec("INSERT INTO %st_item_youtube (fk_i_item_id, s_youtube) VALUES (%d, '%s')", DB_TABLE_PREFIX, $item_id, $youtube_video );
    }
    
    // Self-explanatory
    function youtube_item_detail() {
        $conn   = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_youtube WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());

        require_once 'item_detail.php';
    }

    // Self-explanatory
    function youtube_item_edit($catId = null, $item_id = null) {
        $conn   = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_item_youtube WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);

        require_once 'item_form.php';
    }

    function youtube_item_edit_post($catId = null, $item_id = null) {
        $youtube_video = addslashes(Params::getParam('s_youtube'));
        $youtube_video = convert_youtube_url($youtube_video);
        
        $conn = getConnection() ;   
        $conn->osc_dbExec("REPLACE INTO %st_item_youtube (fk_i_item_id, s_youtube) VALUES (%d, '%s')", DB_TABLE_PREFIX, $item_id, $youtube_video);
    }

    function youtube_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_item_youtube WHERE fk_i_item_id = '$item'", DB_TABLE_PREFIX);
    }
    
    function convert_youtube_url($url) {
        $youtube_url = '';
        if(!preg_match('|.*?youtube.*?v[\?/=](.{11})|', $url)) {
            return '';
        }
        
        $youtube_url = preg_replace('|.*?youtube.*?v[\?/=](.{11}).*|', 'http://www.youtube.com/v/$01?fs=1', $url);
        return $youtube_url;
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'youtube_call_after_install');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__). '_uninstall', 'youtube_call_after_uninstall');

    // When publishing an item we show an extra form with more attributes
    osc_add_hook('item_form', 'youtube_form');
    // To add that new information to our custom table
    osc_add_hook('item_form_post', 'youtube_form_post');

    // Show an item special attributes
    osc_add_hook('item_detail', 'youtube_item_detail');

    // Edit an item special attributes
    osc_add_hook('item_edit', 'youtube_item_edit');
    // Edit an item special attributes POST
    osc_add_hook('item_edit_post', 'youtube_item_edit_post');

    //Delete item
    osc_add_hook('delete_item', 'youtube_delete_item');

?>
