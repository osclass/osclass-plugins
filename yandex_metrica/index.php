<?php
/*
Plugin Name: Yandex.Metrica
Plugin URI: 
Description: This plugin adds the Yandex.Metrica script at the footer of every page.
Version: 1.0
Author: PMaN based on OSClass code
Author URI: 
Plugin update URI: 
*/

    function yandex_metrica_call_after_install() {
        $fields              = array() ;
        $fields["s_section"] = 'plugin-yandex_metrica' ;
        $fields["s_name"]    = 'yandex_metrica_id' ;
        $fields["e_type"]    = 'STRING' ;

        $conn = getConnection() ; 
        $conn->autocommit(true) ;
        Preference::newInstance()->insert($fields) ;
    }

    function yanex_metrica_call_after_uninstall() {
        $conn = getConnection() ; 
        $conn->autocommit(true) ;
        Preference::newInstance()->delete( array("s_section" => "plugin-yandex_metrica", "s_name" => "yandex_metrica_id") ) ;
    }

    function yandex_metrica_admin() {
        osc_admin_render_plugin('yandex_metrica/admin.php') ;
    }

    // HELPER
    function osc_yandex_metrica_id() {
        return osc_get_preference('yandex_metrica_id', 'plugin-yandex_metrica') ;
    }

    /**
     * This function is called every time the page footer is being rendered
     */
    function yandex_metrica_footer() {
        if(osc_yandex_metrica_id() != '') {
            require osc_plugins_path() . 'yandex_metrica/footer.php' ;
        }
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'yandex_metrica_call_after_install') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'yandex_metrica_call_after_uninstall') ;
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'yandex_metrica_admin') ;
    osc_add_hook('footer', 'yandex_metrica_footer') ;

?>