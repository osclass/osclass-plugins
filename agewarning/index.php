<?php
/*
Plugin Name: Age warning
Plugin URI: http://www.osclass.org/
Description: Display a warning message about adult content
Version: 1.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: agewarning
Plugin update URI: age-warning
*/


    function agewarning_install() {
    }

    function agewarning_uninstall() {
    }

    function agewarning_splash() {
        if (Session::newInstance()->_get("agewarning_accepted") != '1' &&
        Cookie::newInstance()->get_value('agewarning_accepted') != '1'&&
        !osc_is_web_user_logged_in() &&
        Params::getParam('action')!='renderplugin') {
            if(Params::getParam('file')!=osc_plugin_folder(__FILE__) . 'warning.php' && Params::getParam('file')!=osc_plugin_folder(__FILE__) . 'confirm.php') {
                Session::newInstance()->_set('agew_backto', $_SERVER['REQUEST_URI']);
                header("Location: ".osc_render_file_url(osc_plugin_folder(__FILE__) . 'warning.php'));
                exit;
            } else if(Params::getParam('file')==osc_plugin_folder(__FILE__) . 'confirm.php') {
                $url = Session::newInstance()->_get('agew_backto');
                Session::newInstance()->_set("agewarning_accepted", "1");
                Cookie::newInstance()->push("agewarning_accepted", "1");
                Cookie::newInstance()->set();
                Session::newInstance()->_drop('agew_backto');
                header("Location: ".$url);
                exit;
            }
        }
    }


    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'agewarning_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'agewarning_uninstall');

    osc_add_hook('before_html', 'agewarning_splash');
    
?>
