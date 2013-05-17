<?php
/*
  Plugin Name: Watchlist
  Plugin URI: https://github.com/osclass/osclass-plugins/tree/watchlist/watchlist
  Description: This plugin add possibility for user to watch items.
  Version: 1.0.6
  Author: Richard Martin (keny) & Osclass
  Author URI: http://www.proodi.com
  Author Email: keny10@gmail.com
  Short Name: WatchList
  Plugin update URI: watchlist
 */

    define('WATCHLIST_VERSION', '1.0.5');

    function watchlist() {
        echo '<a href="javascript://" class="watchlist" id="' . osc_item_id() . '">';
        echo '<span>' . __('Add to watchlist', 'watchlist') . '</span>';
        echo '</a>';
    }

    function watchlist_user_menu() {
        echo '<li class="" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '" >' . __('Watchlist', 'watchlist') . '</a></li>';
    }

    function watchlist_call_after_install() {
        $conn = getConnection();
        $path = osc_plugin_resource('watchlist/struct.sql');
        $sql  = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
    }

    function watchlist_call_after_uninstall() {
        $conn = getConnection();
        $conn->osc_dbExec('DROP TABLE %st_item_watchlist', DB_TABLE_PREFIX);
    }

    function watchlist_footer() {
        echo '<!-- Watchlist js -->';
        echo '<script type="text/javascript">';
        echo 'var watchlist_url = "' . osc_ajax_plugin_url('watchlist/ajax_watchlist.php') . '";';
        echo '</script>';
        echo '<script type="text/javascript" src="' . osc_plugin_url('watchlist/js/watchlist.js') . 'watchlist.js"></script>';
        echo '<!-- Watchlist js end -->';
    }

    function watchlist_scripts_loaded() {
        echo '<!-- Watchlist js -->';
        echo '<script type="text/javascript">';
        echo 'var watchlist_url = "' . osc_ajax_plugin_url('watchlist/ajax_watchlist.php') . '";';
        echo '</script>';
        echo '<!-- Watchlist js end -->';
    }

    function watchlist_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_item_watchlist WHERE fk_i_item_id = '$item'", DB_TABLE_PREFIX);
    }

    function watchlist_help() {
        osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php');
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'watchlist_call_after_install');

    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'watchlist_call_after_uninstall');

    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'watchlist_help');

    // Add link in user menu page
    osc_add_hook('user_menu', 'watchlist_user_menu');

    // add javascript
    if(osc_version()<311) {
        osc_add_hook('footer', 'watchlist_footer');
    } else {
        osc_add_hook('scripts_loaded', 'watchlist_scripts_loaded');
        osc_register_script('watchlist', osc_plugin_url('watchlist/js/watchlist.js') . 'watchlist.js', array('jquery'));
        osc_enqueue_script('watchlist');
    }

    //Delete item
    osc_add_hook('delete_item', 'watchlist_delete_item');

?>
