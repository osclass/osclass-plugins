<?php
/*
Plugin Name: Piwik Web Analytics
Plugin URI: http://www.osclass.org/
Description: Enable Piwik Web Analytics
Version: 0.9.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: piwik
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
        echo '<h3><a href="#">' . __('Piwik Analytics', 'piwik') . '</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'piwik') . '</a></li>
        </ul>';
    }


    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'piwik_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'piwik_uninstall');

    osc_add_hook('admin_menu', 'piwik_admin_menu');
    osc_add_hook('footer', 'piwik_footer');
    
?>