<?php
/*
Plugin Name: Digital Goods
Plugin URI: http://www.osclass.org/
Description: This plugin allows your users to attach a digital file to their ads
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: digitalgoods
*/


    function digitalgoods_install() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $path = osc_plugin_resource('digitalgoods/struct.sql');
            $sql = file_get_contents($path);
            $conn->osc_dbImportSQL($sql);
            $conn->commit();
            osc_set_preference('max_files', '1', 'digitalgoods', 'INTEGER');
            osc_set_preference('upload_path', osc_content_path().'uploads/', 'digitalgoods', 'STRING');
            osc_set_preference('allowed_ext', 'zip,rar,tgz', 'digitalgoods', 'INTEGER');
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function digitalgoods_uninstall() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'digitalgoods'", DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_item_dg_files', DB_TABLE_PREFIX);
            $conn->commit();
            osc_delete_preference('max_files', 'digitalgoods');
            osc_delete_preference('upload_path', 'digitalgoods');
            osc_delete_preference('allowed_ext', 'digitalgoods');
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }
    
    function digitalgoods_admin_menu() {
        echo '<h3><a href="#">Digital Goods</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'digitalgoods') . '</a></li>
            <li><a href="'.osc_admin_configure_plugin_url("digitalgoods/index.php").'">&raquo; ' . __('Configure categories', 'digitalgoods') . '</a></li>
        </ul>';
    }
    
    function digitalgoods_redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }
    
    function digitalgoods_configure_link() {
        //digitalgoods_redirect_to(osc_admin_render_plugin_url(osc_plugin_folder(__FILE__)).'conf.php');
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
            $conn = getConnection();
            $dg_files = $conn->osc_dbFetchResults("SELECT * FROM %st_item_dg_files WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
            require_once 'item_detail.php';
        }
    }

    function digitalgoods_item_edit($catId = null, $item_id = null) {
        if(osc_is_this_category('digitalgoods', $catId)) {
            $conn = getConnection();
            $dg_files = $conn->osc_dbFetchResults("SELECT * FROM %st_item_dg_files WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);
            $dg_item = Item::newInstance()->findByPrimaryKey($item_id);
            $secret = $dg_item['s_secret'];
            unset($dg_item);
            require_once 'item_edit.php';
        }
    }

    function digitalgoods_upload_files($catId = null, $item_id = null) {
        if($catId!=null) {
            if(osc_is_this_category('digitalgoods', $catId)) {
                $files = Params::getFiles('dg_files');
                if(count($files)>0) {
                    $conn = getConnection() ;
                    require LIB_PATH . 'osclass/classes/mimes.php';
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
                                    $file_name = $date.'_'.$item_id.'_'.$files['name'][$key];
                                    $path = osc_get_preference('upload_path', 'digitalgoods').$file_name;
                                    if (move_uploaded_file($files['tmp_name'][$key], $path)) {
                                        $conn->osc_dbExec("INSERT INTO %st_item_dg_files (fk_i_item_id, s_name, s_code) VALUES (%d, '%s', '%s')", DB_TABLE_PREFIX, $item_id, $files['name'][$key], $date);
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
                    if(!$failed) { // THIS MESSAGE IS NOT SHOW
                        osc_add_flash_error_message(__('Some of the files were not uploaded because they have incorrect extension', 'digitalgoods'));
                    }
                }
            }
        }
    }

    function digitalgoods_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_item_dg_files WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
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
    
    osc_add_hook('item_edit_post', 'digitalgoods_upload_files');
    osc_add_hook('item_form_post', 'digitalgoods_upload_files');

    osc_add_hook('delete_item', 'digitalgoods_delete_item');
    
    
    osc_add_hook('admin_menu', 'digitalgoods_admin_menu');
    
?>