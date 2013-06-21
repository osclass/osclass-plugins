<?php
/*
Plugin Name: Piwik Web Analytics
Plugin URI: http://www.osclass.org/
Description: Enable Piwik Web Analytics
Version: 1.0.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: piwik
Plugin update URI: piwik
*/

    function piwik_install() {
        osc_set_preference('js_code', '', 'piwik', 'STRING');
    }

    function piwik_uninstall() {
        osc_delete_preference('js_code', 'piwik');
    }
    
    function piwik_footer() {
        echo osc_get_preference('js_code', 'piwik');
    }

    function piwik_admin_menu() {
        osc_add_admin_submenu_divider('plugins', __('Piwik Analytics', 'piwik'), 'piwik_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Settings', 'piwik'), osc_route_admin_url('piwik-conf'), 'piwik_conf', 'administrator');
    }

    osc_add_route('piwik-conf', 'piwik/help', 'piwik/help', osc_plugin_folder(__FILE__).'admin/conf.php');

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'piwik_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'piwik_uninstall');

    osc_add_hook('admin_menu_init', 'piwik_admin_menu');
    osc_add_hook('footer', 'piwik_footer');
    
?>
