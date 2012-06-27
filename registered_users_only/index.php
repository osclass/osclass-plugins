<?php
/*
Plugin Name: Registered users only
Plugin URI: http://www.osclass.org/
Description: This plugin block non-registered users
Version: 0.9.3
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: registered_users_only
Plugin update URI: registered-users-only
*/

    function login_necessary( ) {
        $location   = Rewrite::newInstance()->get_location() ;
        $section    = Rewrite::newInstance()->get_section() ;
        
        switch($location) {
            case('login'):
            case('register'):
            break;
            default:            // message
                                if( !osc_is_web_user_logged_in() ) {
                                    osc_add_flash_info_message(__('Only registered users can enter to this site. Please register or login', 'registered_users_only')) ;
                                    header('Location: ' . osc_register_account_url()) ; exit() ;
                                }
            break;
        }
    }

    osc_register_plugin(osc_plugin_path(__FILE__), '');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');

    osc_add_hook('before_html', 'login_necessary');

?>
