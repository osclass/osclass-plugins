<?php
/*
Plugin Name: Routes example
Plugin URI: http://www.osclass.org/
Description: Example plugin to demostrate the new routes functions
Version: 1.0.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: routes
Plugin update URI: routes
*/


    /**
    *  Documentation on the wiki : http://doc.osclass.org/Using_routes_in_Osclass
    */


    /**
    * ADD SOME ROUTES
    */
    // Static route (without variables)
    osc_add_route('static-route', 'static-route/', 'static-route/', osc_plugin_folder(__FILE__).'myroute.php');
    // Dynamic route (with variables)
    osc_add_route('dynamic-route', 'dynamic-route/([0-9]+)/(.+)', 'dynamic-route/{my-numeric-param}/{my-own-param}', osc_plugin_folder(__FILE__).'mydynamicroute.php');
    // Dynamic ADMIN route (it's inside an "admin" folder, so it will only open in admin panel)
    osc_add_route('dynamic-admin-route', 'dynamic-admin-route/([0-9]+)/(.+)', 'dynamic-admin-route/{my-numeric-param}/{my-own-param}', osc_plugin_folder(__FILE__).'admin/mydynamicroute.php');


    /**
    * ADD SOME LINKS IN THE ADMIN MENU TO TEST THE ROUTES
    */
    function routes_menu() {
        // NEW OPTION
        osc_add_admin_menu_page( __('Routes plugin', 'routes'), "#", 'routes_plugin', 'administrator' );
        // STATIC ROUTE IN FRONT-END
        osc_add_admin_submenu_page('routes_plugin', __('Static route (PUBLIC)', 'routes'), osc_route_url('static-route'), 'routes_static-front', 'administrator');
        // STATIC ROUTE IN BACK-END
        osc_add_admin_submenu_page('routes_plugin', __('Static route (ADMIN)', 'routes'), osc_route_admin_url('static-route'), 'routes_static-back', 'administrator');
        // DYNAMIC ROUTE IN FRONT-END
        osc_add_admin_submenu_page('routes_plugin', __('Dynamic route (PUBLIC)', 'routes'), osc_route_url('dynamic-route', array('my-numeric-param' => '12345', 'my-own-param' => 'my-own-value')), 'routes_dynamic-front', 'administrator');
        // DYNAMIC ROUTE IN BACK-END
        osc_add_admin_submenu_page('routes_plugin', __('Dynamic route (ADMIN)', 'routes'), osc_route_admin_url('dynamic-route', array('my-numeric-param' => '12345', 'my-own-param' => 'my-own-value')), 'routes_dynamic-back', 'administrator');
        // DYNAMIC ROUTE IN FRONT-END (since it's inside the folder "admin" it will return a 404 error)
        osc_add_admin_submenu_page('routes_plugin', __('Dynamic route (PUBLIC - NOT FOUND)', 'routes'), osc_route_url('dynamic-admin-route', array('my-numeric-param' => '12345', 'my-own-param' => 'my-own-value')), 'routes_dynamic-adm-front', 'administrator');
        // DYNAMIC ROUTE IN BACK-END (it's inside the folder "admin", it could only be opened in the admin panel)
        osc_add_admin_submenu_page('routes_plugin', __('Dynamic route (ADMIN - WORKS)', 'routes'), osc_route_admin_url('dynamic-admin-route', array('my-numeric-param' => '12345', 'my-own-param' => 'my-own-value')), 'routes_dynamic-adm-back', 'administrator');
    }


    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), '');
    osc_add_hook('admin_menu_init', 'routes_menu');

?>
