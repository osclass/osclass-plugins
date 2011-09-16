<?php
/*
Plugin Name: Simple Cache
Plugin URI: http://www.osclass.org/
Description: A simple cache system for OSClass, make your web load faster!
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: simplecache
*/


    function simplecache_install() {
        @mkdir(osc_content_path().'uploads/simplecache/');
        $conn= getConnection();
        osc_set_preference('upload_path', osc_content_path().'uploads/simplecache/', 'simplecache', 'STRING');
        osc_set_preference('hours', '1', 'simplecache', 'INTEGER');
        $conn->commit();
    }

    function simplecache_uninstall() {
        $conn= getConnection();
        osc_delete_preference('upload_path', 'simplecache');
        osc_delete_preference('hours', 'simplecache');
        $conn->commit();
        $files = glob(osc_get_preference('upload_path', 'simplecache')."*.cache");
        foreach($files as $f) {
            @unlink($f);
        }
        @rmdir(osc_get_preference('upload_path', 'simplecache'));
    }
    
    function simplecache_before_html() {
        if(osc_is_ad_page()) {
            if(!file_exists(osc_get_preference('upload_path', 'simplecache')."osc_item_".osc_item_id().".cache")) {
                ob_start();
            } else {
                require_once(osc_get_preference('upload_path', 'simplecache')."osc_item_".osc_item_id().".cache");
                die;
            }
        }
    }
    
    function simplecache_after_html() {
        if(osc_is_ad_page()) {
            if(!file_exists(osc_get_preference('upload_path', 'simplecache')."osc_item_".osc_item_id().".cache")) {
                $contents = ob_get_contents();
                ob_end_flush();
                $handle = fopen(osc_get_preference('upload_path', 'simplecache').'osc_item_'.osc_item_id().'.cache', "w");
                @fwrite($handle, $contents);
                fclose($handle);
            }
        }
    }
    
    function simplecache_delete_file($cat_id, $item_id) {
        @unlink(osc_get_preference('upload_path', 'simplecache').'osc_item_'.$item_id.'.cache');
    }
    
    function simplecache_delete_item($item) {
        @unlink(osc_get_preference('upload_path', 'simplecache').'osc_item_'.$item['pk_i_id'].'.cache');
    }
    
    function simplecache_change_theme($theme) {
        $files = glob(osc_get_preference('upload_path', 'simplecache')."*.cache");
        foreach($files as $f) {
            @unlink($f);
        }
    }
    
    function simplecache_admin_menu() {
        echo '<h3><a href="#">Simple Cache</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'simplecache') . '</a></li>
        </ul>';
    }
    
    function simplecache_configure_link() {
        osc_plugin_configure_view(osc_plugin_path(__FILE__) );
    }
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'simplecache_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'simplecache_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'simplecache_configure_link');
    
    osc_add_hook('before_html', 'simplecache_before_html');
    osc_add_hook('after_html', 'simplecache_after_html');

    osc_add_hook('item_edit_post', 'simplecache_delete_file');
    osc_add_hook('delete_item', 'simplecache_delete_item');
    osc_add_hook('theme_activate', 'simplecache_change_theme');
    
    osc_add_hook('admin_menu', 'simplecache_admin_menu');
    
?>