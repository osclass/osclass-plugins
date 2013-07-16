<?php
/*
Plugin Name: QR Codes
Plugin URI: http://www.osclass.org/
Description: Add a qr code to your ad page, print it and share it offline
Version: 1.0.3
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: qrcode
Plugin update URI: qrcode
*/


    function qrcode_install() {
        @mkdir(osc_content_path().'uploads/qrcode/');
        $conn= getConnection();
        osc_set_preference('upload_path', osc_content_path().'uploads/qrcode/', 'qrcode', 'STRING');
        osc_set_preference('upload_url', osc_base_url().'oc-content/uploads/qrcode/', 'qrcode', 'STRING');
        osc_set_preference('code_size', '2', 'qrcode', 'INTEGER');
        $conn->commit();
    }

    function qrcode_uninstall() {
        $conn= getConnection();
        osc_delete_preference('upload_path', 'qrcode');
        osc_delete_preference('upload_url', 'qrcode');
        osc_delete_preference('code_size', 'qrcode');
        $conn->commit();
        $files = glob(osc_get_preference('upload_path', 'qrcode')."*.png");
        foreach($files as $f) {
            @unlink($f);
        }
        @rmdir(osc_get_preference('upload_path', 'qrcode'));
    }


    function qrcode_admin_menu() {
        if(osc_version()<320) {
            echo '<h3><a href="#">QR Code</a></h3>
            <ul>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'qrcode') . '</a></li>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php') . '">&raquo; ' . __('Help', 'qrcode') . '</a></li>
            </ul>';
        } else {
            osc_add_admin_submenu_divider('plugins', 'QR Codes', 'qrcode_divider', 'administrator');
            osc_add_admin_submenu_page('plugins', __('QR Settings', 'qrcode'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'qrcode_settings', 'administrator');
            osc_add_admin_submenu_page('plugins', __('QR Help', 'qrcode'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php'), 'qrcode_help', 'administrator');
        }
    }

    function qrcode_delete_item($itemId) {
        $files = glob(osc_get_preference('upload_path', 'qrcode').$itemId."_*");
        foreach($files as $f) {
            @unlink($f);
        }
    }
    
    
    function qrcode_generateqr($data, $id = '') {
        include "lib/qrlib.php";
        if($id!='') {
            $filename = $id."_".md5($data)."_".osc_get_preference("code_size", "qrcode").".png";
        } else {
            $filename = md5($data)."_".osc_get_preference("code_size", "qrcode").".png";
        }
        $filename = osc_get_preference('upload_path', 'qrcode').$filename;
        QRcode::png($data, $filename, 'M', osc_get_preference("code_size", "qrcode"), 2);
    }
    
    function show_qrcode() {
        $filename = osc_item_id()."_".md5(osc_item_url())."_".osc_get_preference("code_size", "qrcode").".png";
        if(!file_exists(osc_get_preference('upload_path', 'qrcode').$filename)) {
            qrcode_generateqr(osc_item_url(), osc_item_id());
        }
        echo '<img src="'.osc_get_preference('upload_url', 'qrcode').$filename.'" alt="QR CODE" id="qrcode_'.osc_item_id().'" class="qrcode" />';
    }
 
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'qrcode_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'qrcode_uninstall');
    
    // DELETE ITEM
    osc_add_hook('delete_item', 'qrcode_delete_item');

    // FANCY MENU
    if(osc_version()<320) {
        osc_add_hook('admin_menu', 'qrcode_admin_menu');
    } else {
        osc_add_hook('admin_menu_init', 'qrcode_admin_menu');
    }
    
?>
