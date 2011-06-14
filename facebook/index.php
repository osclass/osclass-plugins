<?php
/*
Plugin Name: Facebook Connect
Plugin URI: http://www.osclass.org/
Description: Use Facebook to connect and log in your users accounts
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: facebook
*/

require_once 'OSCFacebook.php';
function fbc_init() {
    $facebook = OSCFacebook::newInstance()->init( osc_get_preference('fbc_appId', 'facebook_connect')
                                                 ,osc_get_preference('fbc_secret', 'facebook_connect') );
}

function fbc_button() {
    $osc_facebook = OSCFacebook::newInstance();
    $user = $osc_facebook->getUser();
    if ($user) {
        echo '<a href="' . $osc_facebook->logoutUrl() . '">' . __('Logout', 'facebook') . '</a>';
    } else {
        echo '<div><a href="' . $osc_facebook->loginUrl() . '">' . __('Login with Facebook', 'facebook') . '</a></div>';
    };
}


function fbc_call_after_install() {
    // Insert here the code you want to execute after the plugin's install
    // for example you might want to create a table or modify some values
    $conn = getConnection() ;
    $conn->autocommit(false) ;
    try {
        $path = osc_plugins_path().osc_plugin_folder(__FILE__).'struct.sql';
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
        osc_set_preference('fbc_appId', '', 'facebook_connect', 'STRING');
        osc_set_preference('fbc_secret', '', 'facebook_connect', 'STRING');
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}

function fbc_call_after_uninstall() {
    // Insert here the code you want to execute after the plugin's uninstall
    // for example you might want to drop/remove a table or modify some values

    $conn = getConnection();
    $conn->autocommit(false);
    try {
        $conn->osc_dbExec('DROP TABLE %st_facebook_connect', DB_TABLE_PREFIX);
        $conn->commit();
        osc_delete_preference('fbc_appId', 'facebook_connect');
        osc_delete_preference('fbc_secret', 'facebook_connect');
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}


function fbc_delete_user($id) {
    $conn = getConnection();
    return $conn->osc_dbExec('DELETE FROM %st_facebook_connect WHERE fk_i_user_id = %d', DB_TABLE_PREFIX, $id);
}


// Display help
function fbc_conf() {
    osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/conf.php') ;
}


// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'fbc_call_after_install');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'fbc_conf');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'fbc_call_after_uninstall');

osc_add_hook('before_html', 'fbc_init');
osc_add_hook('delete_user', 'fbc_delete_user');

?>