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
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/paypal/Paypal.php';
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/blockchain/Blockchain.php';
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'payments/payza/Payza.php';

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
    * Gets the path of payments folder
    *
    * @return string
    */
    function payment_path() {
        return osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__);
    }

    function payment_url() {
        return osc_render_file_url(osc_plugin_folder(__FILE__));
    }


    /**
    * Create and print a "Wallet" button
    *
    * @param float $amount
    * @param string $description
    * @param string $rpl custom variables
    * @param string $itemnumber (publish fee, premium, pack and which category)
    */
    function wallet_button($amount = '0.00', $description = '', $product = '101', $extra = '||') {
        echo '<a href="'.osc_render_file_url(osc_plugin_folder(__FILE__)."wallet.php?a=".$amount."&desc=".$description."&extra=".implode("|", $extra)."&product=".$product).'"><button>'.__("Pay with your credit", "payment").'</button></a>';
    }

    /**
    * Create a menu on the admin panel
    */
    function payment_admin_menu() {
        echo '<h3><a href="#">payment Options</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('payment Options', 'payment') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf_prices.php') . '">&raquo; ' . __('Categories fees', 'payment') . '</a></li>
        </ul>';
    }

    /**
     * Load payment's js library
     */
    function payment_load_js() {
        Paypal::loadJS();
        Blockchain::loadJS();
    }

    /**
     * Redirect to function via JS
     *
     * @param string $url
     */
    function payment_js_redirect_to($url) { ?>
        <script type="text/javascript">
            window.top.location.href = "<?php echo $url; ?>";
        </script>
    <?php }

    function payment_prepare_custom($extra_array = null) {
        if($extra_array!=null) {
            if(is_array($extra_array)) {
                $extra = '';
                foreach($extra_array as $k => $v) {
                    $extra .= $k.",".$v."|";
                }
            } else {
                $extra = $extra_array;
            }
        } else {
            $extra = "";
        }
        return $extra;
    }

    function payment_get_custom($custom) {
        $tmp = array();
        if(preg_match_all('@\|?([^,]+),([^\|]*)@', $custom, $m)){
            $l = count($m[1]);
            for($k=0;$k<$l;$k++) {
                $tmp[$m[1][$k]] = $m[2][$k];
            }
        }
        return $tmp;
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
                    osc_redirect_to(osc_render_file_url(osc_plugin_folder(__FILE__) . 'payperpublish.php&itemId=' . $item['pk_i_id']));
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
                        osc_redirect_to(osc_render_file_url(osc_plugin_folder(__FILE__) . 'makepremium.php&itemId=' . $item['pk_i_id']));
                    }
                }
            }
        }
    }

    /**
     * Create a new menu option on users' dashboards
     */
    function payment_user_menu() {
        echo '<li class="opt_payment" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."user_menu.php") . '" >' . __("Item payment status", "payment") . '</a></li>' ;
        if((osc_get_preference('pack_price_1', 'payment')!='' && osc_get_preference('pack_price_1', 'payment')!='0') || (osc_get_preference('pack_price_2', 'payment')!='' && osc_get_preference('pack_price_2', 'payment')!='0') || (osc_get_preference('pack_price_3', 'payment')!='' && osc_get_preference('pack_price_3', 'payment')!='0')) {
            echo '<li class="opt_payment_pack" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."user_menu_pack.php") . '" >' . __("Buy credit for payments", "payment") . '</a></li>' ;
        }
    }

    /**
     * Send email to un-registered users with payment options
     *
     * @param integer $item
     * @param float $category_fee
     */
    function payment_send_email($item, $category_fee) {

        if(osc_is_web_user_logged_in()) {
            return false;
        }

        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_payment') ;
        $locale = osc_current_user_locale() ;
        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $item_url    = osc_item_url( ) ;
        $item_url    = '<a href="' . $item_url . '" >' . $item_url . '</a>';
        $publish_url = osc_render_file_url(osc_plugin_folder(__FILE__) . 'payperpublish.php&itemId=' . $item['pk_i_id']);
        $premium_url = osc_render_file_url(osc_plugin_folder(__FILE__) . 'makepremium.php&itemId=' . $item['pk_i_id']);

        $words   = array();
        $words[] = array('{ITEM_ID}', '{CONTACT_NAME}', '{CONTACT_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}',
            '{ITEM_URL}', '{WEB_TITLE}', '{PUBLISH_LINK}', '{PUBLISH_URL}', '{PREMIUM_LINK}', '{PREMIUM_URL}',
            '{START_PUBLISH_FEE}', '{END_PUBLISH_FEE}', '{START_PREMIUM_FEE}', '{END_PREMIUM_FEE}');
        $words[] = array($item['pk_i_id'], $item['s_contact_name'], $item['s_contact_email'], osc_base_url(), $item['s_title'],
            $item_url, osc_page_title(), '<a href="' . $publish_url . '">' . $publish_url . '</a>', $publish_url, '<a href="' . $premium_url . '">' . $premium_url . '</a>', $premium_url, '', '', '', '') ;

        if($category_fee==0) {
            $content['s_text'] = preg_replace('|{START_PUBLISH_FEE}(.*){END_PUBLISH_FEE}|', '', $content['s_text']);
        }

        $premium_fee = ModelPayment::newInstance()->getPremiumPrice($item['fk_i_category_id']);

        if($premium_fee==0) {
            $content['s_text'] = preg_replace('|{START_PREMIUM_FEE}(.*){END_PREMIUM_FEE}|', '', $content['s_text']);
        }

        $title = osc_mailBeauty($content['s_title'], $words) ;
        $body  = osc_mailBeauty($content['s_text'], $words) ;

        $emailParams =  array('subject'  => $title
                             ,'to'       => $item['s_contact_email']
                             ,'to_name'  => $item['s_contact_name']
                             ,'body'     => $body
                             ,'alt_body' => $body);

        osc_sendMail($emailParams);
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
        osc_redirect_to(osc_admin_render_plugin_url(osc_plugin_folder(__FILE__)).'conf.php');
    }

    function payment_update_version() {
        ModelPayment::newInstance()->versionUpdate();
    }

    function payment_format_btc($btc, $symbol = "BTC") {
        if($btc<0.00001) {
            return ($btc*1000000).'µ'.$symbol;
        } else if($btc<0.01) {
            return ($btc*1000).'m'.$symbol;
        }
        return $btc.$symbol;
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'payment_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'payment_configure_link');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'payment_uninstall');
    osc_add_hook(osc_plugin_path(__FILE__)."_enable", 'payment_update_version');

    osc_add_hook('admin_menu', 'payment_admin_menu');
    osc_add_hook('header', 'payment_load_js', 10);
    osc_add_hook('posted_item', 'payment_publish', 3);
    osc_add_hook('user_menu', 'payment_user_menu');
    osc_add_hook('cron_hourly', 'payment_cron');
    osc_add_hook('item_premium_off', 'payment_premium_off');
    osc_add_hook('before_item_edit', 'payment_before_edit');
    osc_add_hook('show_item', 'payment_show_item');
    osc_add_hook('delete_item', 'payment_item_delete');
?>
