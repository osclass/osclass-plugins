<?php
/*
Plugin Name: Payment system
Plugin URI: http://www.osclass.org/
Description: Payment system
Version: 2.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: payments
*/

    define('PAYMENT_CRYPT_KEY', 'randompasswordchangethis');
    // PAYMENT STATUS
    define('PAYMENT_FAILED', 0);
    define('PAYMENT_COMPLETED', 1);
    define('PAYMENT_PENDING', 2);
    define('PAYMENT_ALREADY_PAID', 3);


    // load necessary functions
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'ModelPayment.php';
    // Load different methods of payments
    if(osc_get_preference('paypal_enabled', 'payment')==1) {
        require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/paypal/Paypal.php';
    }
    if(osc_get_preference('blockchain_enabled', 'payment')==1) {
        require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/blockchain/Blockchain.php'; // Ready, but untested
    }
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/braintree/BraintreePayment.php'; // Ready, but untested

    /**
    * Create tables and variables on t_preference and t_pages
    */
    function payment_install() {
        ModelPayment::newInstance()->install();
    }

    /**
    * Clean up all the tables and preferences
    */
    function payment_uninstall() {
        ModelPayment::newInstance()->uninstall();
    }

    /**
    * Create a menu on the admin panel
    */
    function payment_admin_menu() {
        osc_add_admin_submenu_divider('plugins', 'Payment plugin', 'payment_divider', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Payment options', 'payment'), osc_route_admin_url('payment-admin-conf'), 'payment_settings', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Categories fees', 'payment'), osc_route_admin_url('payment-admin-prices'), 'payment_help', 'administrator');
    }

    /**
     * Load payment's js library
     */
    function payment_load_js() {
        if(osc_get_preference('paypal_enabled', 'payment')==1) {
            osc_register_script('paypal', 'https://www.paypalobjects.com/js/external/dg.js', array('jquery'));
        }
        if(osc_get_preference('blockchain_enabled', 'payment')==1) {
            osc_register_script('blockchain', 'https://blockchain.info/Resources/wallet/pay-now-button.js', array('jquery'));
        }
        osc_register_script('braintree', 'https://blockchain.info/Resources/wallet/pay-now-button.js', array('jquery'));
    }

    /**
     * Redirect to payment page after publishing an item
     *
     * @param integer $item
     */
    function payment_publish($item) {
        if( // WE HAVE CORRECTLY SETUP PAYPAL
            (osc_get_preference('paypal_enabled', 'payment')==1 &&
                ((osc_get_preference('paypal_standard', 'payment')==1 && osc_get_preference('paypal_email', 'payment')!='') ||
                (payment_decrypt(osc_get_preference('paypal_api_username', 'payment'))!='' &&
                payment_decrypt(osc_get_preference('paypal_api_password', 'payment'))!='' &&
                payment_decrypt(osc_get_preference('paypal_api_signature', 'payment'))!='' &&
                osc_get_preference('paypal_standard', 'payment')==0)))
            ||
            // WE HAVE CORRECTLY SETUP BLOCKCHAIN
            (osc_get_preference('blockchain_enabled', 'payment')==1 && osc_get_preference('blockchain_btc_address', 'payment')!='')) {
            // Need to pay to publish ?
            if(osc_get_preference('pay_per_post', 'payment')==1) {
                $category_fee = ModelPayment::newInstance()->getPublishPrice($item['fk_i_category_id']);
                payment_send_email($item, $category_fee);
                if($category_fee>0) {
                    // Catch and re-set FlashMessages
                    osc_resend_flash_messages();
                    $mItems = new ItemActions(false);
                    $mItems->disable($item['pk_i_id']);
                    ModelPayment::newInstance()->createItem($item['pk_i_id'],0);
                    osc_redirect_to(osc_route_url('payment-publish', array('itemId' => $item['pk_i_id'])));
                } else {
                    // PRICE IS ZERO
                    ModelPayment::newInstance()->createItem($item['pk_i_id'], 1);
                }
            } else {
                // NO NEED TO PAY PUBLISH FEE
                payment_send_email($item, 0);
                if(osc_get_preference('allow_premium', 'payment')==1) {
                    $premium_fee = ModelPayment::newInstance()->getPremiumPrice($item['fk_i_category_id']);
                    if($premium_fee>0) {
                        osc_redirect_to(osc_route_url('payment-premium', array('itemId' => $item['pk_i_id'])));
                    }
                }
            }
        }
    }

    /**
     * Create a new menu option on users' dashboards
     */
    function payment_user_menu() {
        echo '<li class="opt_payment" ><a href="'.osc_route_url('payment-user-menu').'" >'.__("Item payment status", "payment").'</a></li>' ;
        if((osc_get_preference('pack_price_1', 'payment')!='' && osc_get_preference('pack_price_1', 'payment')!='0') || (osc_get_preference('pack_price_2', 'payment')!='' && osc_get_preference('pack_price_2', 'payment')!='0') || (osc_get_preference('pack_price_3', 'payment')!='' && osc_get_preference('pack_price_3', 'payment')!='0')) {
            echo '<li class="opt_payment_pack" ><a href="'.osc_route_url('payment-user-pack').'" >'.__("Buy credit for payments", "payment").'</a></li>' ;
        }
    }

    /**
     * Executed hourly with cron to clean up the expired-premium ads
     */
    function payment_cron() {
        ModelPayment::newInstance()->purgeExpired();
    }

    /**
     * Executed when an item is manually set to NO-premium to clean up it on the plugin's table
     *
     * @param integer $id
     */
    function payment_premium_off($id) {
        ModelPayment::newInstance()->premiumOff($id);
    }

    /**
     * Executed before editing an item
     *
     * @param array $item
     */
    function payment_before_edit($item) {
        // avoid category changes once the item is paid
        if((osc_get_preference('pay_per_post', 'payment') == '1' && ModelPayment::newInstance()->publishFeeIsPaid($item['pk_i_id']))|| (osc_get_preference('allow_premium','payment') == '1' && ModelPayment::newInstance()->premiumFeeIsPaid($item['pk_i_id']))) {
            $cat[0] = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
            View::newInstance()->_exportVariableToView('categories', $cat);
        }
    }


    /**
     * Executed before showing an item
     *
     * @param array $item
     */
    function payment_show_item($item) {
        if(osc_get_preference("pay_per_post", "payment")=="1" && !ModelPayment::newInstance()->publishFeeIsPaid($item['pk_i_id']) ) {
            payment_publish($item);
        };
    };

    function payment_item_delete($itemId) {
        ModelPayment::newInstance()->deleteItem($itemId);
    }

    function payment_configure_link() {
        osc_redirect_to(osc_route_admin_url('payment-admin-conf'));
    }

    function payment_update_version() {
        ModelPayment::newInstance()->versionUpdate();
    }


    /**
     * ADD ROUTES (VERSION 3.2+)
     */
    osc_add_route('payment-admin-conf', 'payment/admin/conf', 'payment/admin/conf', osc_plugin_folder(__FILE__).'admin/conf.php');
    osc_add_route('payment-admin-prices', 'payment/admin/prices', 'payment/admin/prices', osc_plugin_folder(__FILE__).'admin/conf_prices.php');
    osc_add_route('payment-publish', 'payment/publish/([0-9]+)', 'payment/publish/{itemId}', osc_plugin_folder(__FILE__).'user/payperpublish.php');
    osc_add_route('payment-premium', 'payment/premium/([0-9]+)', 'payment/premium/{itemId}', osc_plugin_folder(__FILE__).'user/makepremium.php');
    osc_add_route('payment-user-menu', 'payment/menu', 'payment/menu', osc_plugin_folder(__FILE__).'user/menu.php');
    osc_add_route('payment-user-pack', 'payment/pack', 'payment/pack', osc_plugin_folder(__FILE__).'user/pack.php');
    osc_add_route('payment-wallet', 'payment/wallet/([^\/]+)/([^\/]+)/([^\/]+)/(.+)', 'payment/wallet/{a}/{extra}/{desc}/{product}', osc_plugin_folder(__FILE__).'/user/wallet.php');

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'payment_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'payment_configure_link');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'payment_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_enable", 'payment_update_version');

    osc_add_hook('admin_menu_init', 'payment_admin_menu');

    osc_add_hook('header', 'payment_load_js');
    osc_add_hook('posted_item', 'payment_publish', 3);
    osc_add_hook('user_menu', 'payment_user_menu');
    osc_add_hook('cron_hourly', 'payment_cron');
    osc_add_hook('item_premium_off', 'payment_premium_off');
    osc_add_hook('before_item_edit', 'payment_before_edit');
    osc_add_hook('show_item', 'payment_show_item');
    osc_add_hook('delete_item', 'payment_item_delete');

    osc_add_hook('ajax_braintree', array('BraintreePayment', 'ajaxPayment'));
?>
