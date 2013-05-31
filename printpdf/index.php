<?php
/*
Plugin Name: Print PDF
Plugin URI: http://www.osclass.org/
Description: Create a PDF ready to print and share offline
Version: 1.4.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: printpdf
Plugin update URI: printpdf
*/


    function printpdf_install() {
        @mkdir(osc_content_path().'uploads/printpdf/');
        $conn= getConnection();
        osc_set_preference('upload_path', osc_content_path().'uploads/printpdf/', 'printpdf', 'STRING');
        $conn->commit();
    }

    function printpdf_uninstall() {
        $conn= getConnection();
        osc_delete_preference('upload_path', 'printpdf');
        $conn->commit();
        $files = glob(osc_get_preference('upload_path', 'printpdf')."*.pdf");
        foreach($files as $f) {
            @unlink($f);
        }
        @rmdir(osc_get_preference('upload_path', 'printpdf'));
    }
    

    
    function printpdf_admin_menu() {
        if(osc_version()<320) {
            echo '<h3><a href="#">Print PDF</a></h3>
            <ul>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'printpdf') . '</a></li>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php') . '">&raquo; ' . __('Help', 'printpdf') . '</a></li>
            </ul>';
        } else {
            osc_add_admin_submenu_divider('plugins', 'Print PDF', 'printpdf_divider', 'administrator');
            osc_add_admin_submenu_page('plugins', __('PrintPDF Settings', 'qrcode'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'printpdf_settings', 'administrator');
            osc_add_admin_submenu_page('plugins', __('PrintPDF Help', 'qrcode'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php'), 'printpdf_help', 'administrator');
        }
    }
    
    function printpdf_delete_item($item) {
        if(is_array($item)) {
            $item = $item['pk_i_id'];
        }
        $files = glob(osc_get_preference('upload_path', 'printpdf').$item."_*");
        foreach($files as $f) {
            @unlink($f);
        }
    }
    
    
    function printpdf_generatePDF($data, $id = '') {
        include "fpdf/pdf_rotation.php";
        if($id!='') {
            $filename = $id."_".md5($data)."_".osc_get_preference("code_size", "printpdf").".png";
        } else {
            $filename = md5($data)."_".osc_get_preference("code_size", "printpdf").".png";
        }
        $filename = osc_get_preference('upload_path', 'printpdf').$filename;
        printpdf::png($data, $filename, 'M', osc_get_preference("code_size", "printpdf"), 2);
    }
    
    
    function show_printpdf() {
        echo '<a href="'.osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'/download.php?item='.osc_item_id().'" class="printpdf_link" >'.__('Download PDF', 'printpdf').'</a>';
    }
    
    
    function printpdf_shorturl($url)  {  
        $ch = curl_init();  
        $timeout = 5;  
        curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
        $data = curl_exec($ch);  
        curl_close($ch);  
        return $data;  
    }    
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'printpdf_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'printpdf_uninstall');
    
    // DELETE ITEM
    osc_add_hook('delete_item', 'printpdf_delete_item');
    osc_add_hook('edited_item', 'printpdf_delete_item');

    // FANCY MENU
    if(osc_version()<320) {
        osc_add_hook('admin_menu', 'printpdf_admin_menu');
    } else {
        osc_add_hook('admin_menu_init', 'printpdf_admin_menu');
    }
    
?>
