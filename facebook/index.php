<?php
/*
Plugin Name: Facebook Connect
Plugin URI: http://www.osclass.org/
Description: Use Facebook to connect and log in your users accounts
Version: 1.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: facebook
Plugin update URI: facebook-connect
*/

    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    require_once dirname( __FILE__ ) . '/OSCFacebook.php' ;

    function fbc_init() {
        $facebook = OSCFacebook::newInstance()->init( osc_get_preference('fbc_appId', 'facebook_connect')
                                                     ,osc_get_preference('fbc_secret', 'facebook_connect') ) ;
    }

    /**
     * Just in case you want to customize the loign button with some imag
     *
     * @return string Facebook login url
     */
    function fbc_login_url() {
        return OSCFacebook::newInstance()->loginUrl() ;
    }

    function fbc_button() {
        $user = OSCFacebook::newInstance()->getUser() ;

        if( $user && osc_is_web_user_logged_in() ) {
            echo '<a href="' . OSCFacebook::newInstance()->logoutUrl() . '">' . __( 'Logout', 'facebook' ) . '</a>' ;
        } else {
            echo '<div><a href="' . OSCFacebook::newInstance()->loginUrl() . '">' . __( 'Login with Facebook', 'facebook' ) . '</a></div>' ;
        }
    }

    function fbc_call_after_install() {
        OSCFacebook::newInstance()->import( 'facebook/struct.sql' ) ;
        osc_set_preference('fbc_appId', '', 'facebook_connect', 'STRING') ;
        osc_set_preference('fbc_secret', '', 'facebook_connect', 'STRING') ;
    }

    function fbc_call_after_uninstall() {
        OSCFacebook::newInstance()->uninstall() ;
        osc_delete_preference( 'fbc_appId', 'facebook_connect' ) ;
        osc_delete_preference( 'fbc_secret', 'facebook_connect' ) ;
    }

    function fbc_delete_user( $userID ) {
        $osc = OSCFacebook::newInstance() ;
        $osc->deleteByPrimaryKey( $userID ) ;
    }

    // Display help
    function fbc_conf() {
        osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/conf.php' ) ;
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin( osc_plugin_path( __FILE__ ), 'fbc_call_after_install' ) ;
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'fbc_conf' ) ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook( osc_plugin_path( __FILE__ ) . '_uninstall', 'fbc_call_after_uninstall' ) ;

    osc_add_hook( 'before_html', 'fbc_init' ) ;
    osc_add_hook( 'delete_user', 'fbc_delete_user' ) ;

    /* file end: ./oc-content/plugins/facebook/index.php */
?>
