<?php
/*
Plugin Name: Digital Goods
Plugin URI: http://www.osclass.org/
Description: This plugin allows your users to attach a digital file to their ads
Version: 1.1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: digitalgoods
Plugin update URI: digital-goods
*/

    require_once 'DGModel.php';

    function digitalgoods_install() {
        DGModel::newInstance()->import('cars_attributes/struct.sql');
        @mkdir(osc_content_path().'uploads/digitalgoods/');
        osc_set_preference('upload_path', osc_content_path().'uploads/digitalgoods/', 'digitalgoods', 'STRING');
        osc_set_preference('max_files', '1', 'digitalgoods', 'INTEGER');
        osc_set_preference('allowed_ext', 'zip,rar,tgz', 'digitalgoods', 'INTEGER');
    }

    function digitalgoods_uninstall() {
        DGModel::newInstance()->uninstall();
        osc_delete_preference('max_files', 'digitalgoods');
        osc_delete_preference('upload_path', 'digitalgoods');
        osc_delete_preference('allowed_ext', 'digitalgoods');
    }

    function digitalgoods_admin_menu() {
        if(osc_version()<320) {
            echo '<h3><a href="#">Digital Goods</a></h3>
            <ul>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/conf.php') . '">&raquo; ' . __('Settings', 'digitalgoods') . '</a></li>
                <li><a href="'.osc_admin_configure_plugin_url("digitalgoods/index.php").'">&raquo; ' . __('Configure categories', 'digitalgoods') . '</a></li>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/stats.php') . '">&raquo; ' . __('Stats', 'digitalgoods') . '</a></li>
            </ul>';
        } else {
            osc_add_admin_submenu_divider('plugins', 'Digital Goods', 'digitalgoods_divider', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Settings', 'digitalgoods'), osc_route_admin_url('digitalgoods-admin-conf'), 'digitalgoods_settings', 'administrator');
            osc_add_admin_submenu_page('plugins', __('Configure categories', 'digitalgoods'), osc_admin_configure_plugin_url("digitalgoods/index.php"), 'digitalgoods_categories', 'administrator');
            osc_add_admin_submenu_page('plugins', __('File stats', 'digitalgoods'), osc_route_admin_url('digitalgoods-admin-stats'), 'digitalgoods_stats', 'administrator');
        };
    }

    function digitalgoods_configure_link() {
        osc_plugin_configure_view(osc_plugin_path(__FILE__) );
    }


    function digitalgoods_form($catId = null) {
        if($catId!="") {
            if(osc_is_this_category('digitalgoods', $catId)) {
                $dg_files = null;
                require_once 'item_edit.php';
            }
        }
    }

    function digitalgoods_item_detail() {
        if(osc_is_this_category('digitalgoods', osc_item_category_id())) {
            $dg_files = DGModel::newInstance()->getFilesFromItem(osc_item_id());
            require_once 'item_detail.php';
        }
    }

    function digitalgoods_item_edit($catId = null, $item_id = null) {
        if(osc_is_this_category('digitalgoods', $catId)) {
            $dg_files = DGModel::newInstance()->getFilesFromItem($item_id);
            $dg_item = Item::newInstance()->findByPrimaryKey($item_id);
            $secret = $dg_item['s_secret'];
            unset($dg_item);
            require_once 'item_edit.php';
        }
    }

    function digitalgoods_upload_files($item){
        if($item['fk_i_category_id']!=null) {
            if(osc_is_this_category('digitalgoods', $catId)) {
                $files = Params::getFiles('dg_files');
                if(count($files)>0) {
                    require LIB_PATH . 'osclass/mimes.php';
                    $aMimesAllowed = array();
                    $aExt = explode(',', osc_get_preference('allowed_ext', 'digitalgoods'));
                    foreach($aExt as $ext){
                        $mime = $mimes[$ext];
                        if( is_array($mime) ){
                            foreach($mime as $aux){
                                if( !in_array($aux, $aMimesAllowed) ) {
                                    array_push($aMimesAllowed, $aux );
                                }
                            }
                        } else {
                            if( !in_array($mime, $aMimesAllowed) ) {
                                array_push($aMimesAllowed, $mime );
                            }
                        }
                    }
                    $failed = false;
                    $maxSize = osc_max_size_kb() * 1024;
                    foreach ($files['error'] as $key => $error) {
                        $bool_img = false;
                        if ($error == UPLOAD_ERR_OK) {
                            $size = $files['size'][$key];
                            if($size <= $maxSize){
                                $fileMime = $files['type'][$key] ;

                                if(in_array($fileMime,$aMimesAllowed)) {
                                    $date = date('YmdHis');
                                    $file_name = $date.'_'.$item['pk_i_id'].'_'.$files['name'][$key];
                                    $path = osc_get_preference('upload_path', 'digitalgoods').$file_name;
                                    if (move_uploaded_file($files['tmp_name'][$key], $path)) {
                                        DGModel::newInstance()->insertFile($item['pk_i_id'], $files['name'][$key], $date);
                                    } else {
                                        $failed = true;
                                    }
                                } else {
                                    $failed = true;
                                }
                            } else {
                                $failed = true;
                            }
                        }
                    }
                    if($failed) {
                        osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'digitalgoods'),'admin');
                    }
                }
            }
        }
    }

    function digitalgoods_delete_item($item) {
        DGModel::newInstance()->removeItem($item);
    }

    if(osc_version()>=320) {
        /**
         * ADD ROUTES (VERSION 3.2+)
         */
        osc_add_route('digitalgoods-admin-conf', 'digitalgoods/admin/conf', 'digitalgoods/admin/conf', osc_plugin_folder(__FILE__).'admin/conf.php');
        osc_add_route('digitalgoods-admin-stats', 'digitalgoods/admin/stats', 'digitalgoods/admin/stats', osc_plugin_folder(__FILE__).'admin/stats.php');
        osc_add_route('digitalgoods-ajax', 'digitalgoods/ajax', 'digitalgoods/ajax', osc_plugin_folder(__FILE__).'ajax.php');
        osc_add_route('digitalgoods-download', 'digitalgoods/download/(.+)', 'digitalgoods/download/{file}', osc_plugin_folder(__FILE__).'download.php');
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'digitalgoods_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'digitalgoods_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'digitalgoods_configure_link');


    osc_add_hook('item_detail', 'digitalgoods_item_detail');

    osc_add_hook('item_form', 'digitalgoods_form');
    osc_add_hook('item_edit', 'digitalgoods_item_edit');

    osc_add_hook('edited_item', 'digitalgoods_upload_files');
    osc_add_hook('posted_item', 'digitalgoods_upload_files');

    osc_add_hook('delete_item', 'digitalgoods_delete_item');


    if(osc_version()<320) {
        osc_add_hook('admin_menu', 'digitalgoods_admin_menu');
    } else {
        osc_add_hook('admin_menu_init', 'digitalgoods_admin_menu');
    }

?>
