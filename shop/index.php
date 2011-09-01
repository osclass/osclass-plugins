<?php
/*
Plugin Name: Shop
Plugin URI: http://www.osclass.org/
Description: This plugin transforms your OSClass into a shop!
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: shop
*/


    function shop_install() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $path = osc_plugin_resource('shop/struct.sql');
            $sql = file_get_contents($path);
            $conn->osc_dbImportSQL($sql);
            $users = User::newInstance()->listAll();
            foreach($users as $user) {
                $conn->osc_dbExec("INSERT INTO %st_shop_user (fk_i_user_id, f_score, i_total_sales, i_total_buys) values (%d, 0, 0, 0)");
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function shop_uninstall() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $conn->osc_dbExec('DROP TABLE %st_shop_item', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_user', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_transaction', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_log', DB_TABLE_PREFIX);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }
    
    function shop_admin_menu() {
        echo '<h3><a href="#">Shop Plugin</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'shop') . '</a></li>
        </ul>';
    }
    
    function shop_redirect_to($url) {
        header('Location: ' . $url);
        exit;
    }
    
    function shop_configure_link() {
        osc_plugin_configure_view(osc_plugin_path(__FILE__) );
    }
    

    function shop_form($catId = null) {
        $detail['i_amount'] = 1;
        require_once 'item_edit.php';
    }

    function shop_item_detail() {
        $conn = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
        require_once 'item_detail.php';
    }

    function shop_item_edit($catId = null, $item_id = null) {
        $conn = getConnection();
        $detail = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);
        require_once 'item_edit.php';
    }

    function shop_item_edit_post($catId = null, $item_id = null) {
        $conn = getConnection();
        $item = $conn->osc_dbFetchResult("SELECT fk_i_item_id FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $item_id);
        $amount = Params::getParam('shop_amount')>=0?Params::getParam('shop_amount'):0;
        if(!isset($item['fk_i_item_id'])) {
            $conn->osc_dbExec("REPLACE INTO %st_shop_item (fk_i_item_id, i_amount, b_accept_shop, b_accept_bank_transfer) VALUES (%d, %d, %d, %d)", DB_TABLE_PREFIX, $item_id, $amount, Params::getParam('shop_accept_shop'), Params::getParam('shop_accept_bank_transfer') );
        } else {
            $conn->osc_dbExec("UPDATE %st_shop_item SET  i_amount = %d, b_accept_shop = %d, b_accept_bank_transfer = %d WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $amount, Params::getParam('shop_accept_shop'), Params::getParam('shop_accept_bank_transfer'), $item_id );
        }
    }

    function shop_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_shop_item WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
    }
    
    function shop_user_menu() {
        echo '<li class="opt_shop" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."menu_buyer.php") . '" >' . __("Items bought", "shop") . '</a></li>' ;
        echo '<li class="opt_shop" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."menu_seller.php") . '" >' . __("Items sold", "shop") . '</a></li>' ;
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'shop_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'shop_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'shop_configure_link');


    osc_add_hook('item_detail', 'shop_item_detail');

    osc_add_hook('item_form', 'shop_form');
    osc_add_hook('item_edit', 'shop_item_edit');
    osc_add_hook('item_form_post', 'shop_item_edit_post');
    osc_add_hook('item_edit_post', 'shop_item_edit_post');
    

    osc_add_hook('delete_item', 'shop_delete_item');
    
    
    osc_add_hook('admin_menu', 'shop_admin_menu');
    osc_add_hook('user_menu', 'shop_user_menu');
      
?>