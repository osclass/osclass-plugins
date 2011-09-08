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
                $conn->osc_dbExec("INSERT INTO %st_shop_user (fk_i_user_id, f_score, i_total_sales, i_total_buys) values (%d, 0, 0, 0)", DB_TABLE_PREFIX, $user['pk_i_id']);
            }
            $items = $conn->osc_dbFetchResults("SELECT * FROM %st_item", DB_TABLE_PREFIX);
            foreach($items as $item) {
                $conn->osc_dbExec("INSERT INTO %st_shop_item (fk_i_item_id, i_amount, b_digital, b_accept_paypal, b_accept_bank_transfer) values (%d, 0, 0, 0, 0)", DB_TABLE_PREFIX, $item['pk_i_id']);
            }
            $conn->osc_dbExec("INSERT INTO %st_pages (s_internal_name, b_indelible, dt_pub_date) VALUES ('email_shop_sold_buyer', 1, NOW() )", DB_TABLE_PREFIX);
            $conn->osc_dbExec("INSERT INTO %st_pages_description (fk_i_pages_id, fk_c_locale_code, s_title, s_text) VALUES (%d, '%s', '{WEB_TITLE} - Congratulations! You just bought {ITEM_TITLE} ({TXN_CODE})', '<p>Hi {CONTACT_NAME}!</p>\r\n<p> </p>\r\n<p>You just bought ({ITEM_TITLE}) on {WEB_TITLE} for {PRICE} (transaction #ID: {TXN_CODE}).</p>\r\n<p> You need to pay for it, follow these instructions : {INSTRUCTIONS}</p>\r\n<p>Thanks</p>')", DB_TABLE_PREFIX, $conn->get_last_id(), osc_language());
            $conn->osc_dbExec("INSERT INTO %st_pages (s_internal_name, b_indelible, dt_pub_date) VALUES ('email_shop_sold_seller', 1, NOW() )", DB_TABLE_PREFIX);
            $conn->osc_dbExec("INSERT INTO %st_pages_description (fk_i_pages_id, fk_c_locale_code, s_title, s_text) VALUES (%d, '%s', '{WEB_TITLE} - Your item {ITEM_TITLE} has been sold', '<p>Hi {CONTACT_NAME}!</p>\r\n<p> </p>\r\n<p>We just sold your item ({ITEM_TITLE}) on {WEB_TITLE} to {BUYER_NAME}.</p>\r\n<p>Instructions have been sent to buyer, please wait until the buyer pay for it to continue the process </p>\r\n<p>Thanks</p>')", DB_TABLE_PREFIX, $conn->get_last_id(), osc_language());
            $conn->osc_dbExec("INSERT INTO %st_pages (s_internal_name, b_indelible, dt_pub_date) VALUES ('email_shop_contact', 1, NOW() )", DB_TABLE_PREFIX);
            $conn->osc_dbExec("INSERT INTO %st_pages_description (fk_i_pages_id, fk_c_locale_code, s_title, s_text) VALUES (%d, '%s', '{WEB_TITLE} - Someone has a question', '<p>Hi {CONTACT_NAME}!</p>\n<p>{USER_NAME} ({USER_EMAIL}, {USER_PHONE}) left you a message :</p>\n<p>{COMMENT}</p>\n<p>Regards,</p>\n<p>{WEB_TITLE}</p>')", DB_TABLE_PREFIX, $conn->get_last_id(), osc_language());
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
            $conn->osc_dbExec('DROP TABLE %st_shop_transactions', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_log', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_paypal_log', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_favs', DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_shop_message', DB_TABLE_PREFIX);
            $page_id = $conn->osc_dbFetchResult("SELECT * FROM %st_pages WHERE s_internal_name = 'email_shop_sold_buyer'", DB_TABLE_PREFIX);
            $conn->osc_dbExec("DELETE FROM %st_pages_description WHERE fk_i_pages_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $conn->osc_dbExec("DELETE FROM %st_pages WHERE pk_i_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $page_id = $conn->osc_dbFetchResult("SELECT * FROM %st_pages WHERE s_internal_name = 'email_shop_sold_seller'", DB_TABLE_PREFIX);
            $conn->osc_dbExec("DELETE FROM %st_pages_description WHERE fk_i_pages_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $conn->osc_dbExec("DELETE FROM %st_pages WHERE pk_i_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $page_id = $conn->osc_dbFetchResult("SELECT * FROM %st_pages WHERE s_internal_name = 'email_shop_contact'", DB_TABLE_PREFIX);
            $conn->osc_dbExec("DELETE FROM %st_pages_description WHERE fk_i_pages_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $conn->osc_dbExec("DELETE FROM %st_pages WHERE pk_i_id = %d", DB_TABLE_PREFIX, $page_id['pk_i_id']);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
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
            $conn->osc_dbExec("INSERT INTO %st_shop_item (fk_i_item_id, i_amount, b_accept_paypal, b_accept_bank_transfer) VALUES (%d, %d, %d, %d)", DB_TABLE_PREFIX, $item_id, $amount, Params::getParam('shop_accept_paypal'), Params::getParam('shop_accept_bank_transfer') );
        } else {
            $conn->osc_dbExec("UPDATE %st_shop_item SET  i_amount = %d, b_accept_paypal = %d, b_accept_bank_transfer = %d WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $amount, Params::getParam('shop_accept_paypal'), Params::getParam('shop_accept_bank_transfer'), $item_id );
        }
    }

    function shop_delete_item($item) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_shop_item WHERE fk_i_item_id = '" . $item . "'", DB_TABLE_PREFIX);
    }
    
    function shop_user_menu() {
        echo '<li class="opt_shop" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."menu_buyer.php") . '" >' . __("Items bought", "shop") . '</a></li>' ;
        echo '<li class="opt_shop" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."menu_seller.php") . '" >' . __("Items sold", "shop") . '</a></li>' ;
        echo '<li class="opt_shop" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__)."favorites.php") . '" >' . __("Favourite sellers", "shop") . '</a></li>' ;
    }
    
    function shop_profile_link($id = NULL) {
        if($id==NULL || $id=='') {
            $id = osc_user_id();
            if($id=='') {
                $id = osc_item_user_id();
            }
        }
        if($id!='') {
            return osc_render_file_url(osc_plugin_folder(__FILE__)."profile.php&user_id=").$id;
        } else {
            return '';
        }
    }
    
    
    function shop_send_contact_email($from, $to, $msg, $item_id) {
        if($item_id!='') {
            $aItem = array(
                'id' => $item_id
                ,'yourEmail' => $from['s_email']
                ,'yourName' => $from['s_name']
                ,'phoneNumber' => ($from['s_phone_land']==''?$from['s_phone_mobile']:$from['s_phone_land'])
                ,'message' => $msg
            );
            fn_email_item_inquiry($aItem);
        } else {
            $mPages = new Page();
            $aPage = $mPages->findByInternalName('email_shop_contact');
            $locale = osc_current_user_locale() ;

            $content = array();
            if(isset($aPage['locale'][$locale]['s_title'])) {
                $content = $aPage['locale'][$locale];
            } else {
                $content = current($aPage['locale']);
            }

            $words   = array();
            $words[] = array('{CONTACT_NAME}', '{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}',
                                 '{WEB_URL}', '{COMMENT}');

            $words[] = array($to['s_name'], $from['s_name'], $from['s_email'],
                             ($from['s_phone_land']==''?$from['s_phone_mobile']:$from['s_phone_land']),
                             osc_base_url(), $msg );

            $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_shop_contact_title', $content['s_title'])), $words);
            $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_shop_conatct_description', $content['s_text'])), $words);

            $from_email = osc_contact_email() ;
            $from_name = osc_page_title() ;

            $emailParams = array (
                                'from'      => $from_email
                                ,'from_name' => $from_name
                                ,'subject'   => $title
                                ,'to'        => $to['s_email']
                                ,'to_name'   => $to['s_name']
                                ,'body'      => $body
                                ,'alt_body'  => $body
                                ,'reply_to'  => $from
                            ) ;

            osc_sendMail($emailParams);
        }
    }
    
    function shop_send_sold_email($txn_id) {
        
        $conn = getConnection();
        $txn = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_transactions WHERE pk_i_id = %d", DB_TABLE_PREFIX, $txn_id);
        $item = Item::newInstance()->findByPrimaryKey($txn['fk_i_item_id']);
        View::newInstance()->_exportVariableToView('item', $item);
        $seller = User::newInstance()->findByPrimaryKey($txn['fk_i_user_id']);
        $buyer = User::newInstance()->findByPrimaryKey($txn['fk_i_buyer_id']);
        
        $shop_item = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $txn['fk_i_item_id']);
        
        $item_url = osc_item_url();
        $item_url = '<a href="'.$item_url.'" >'.$item_url.'</a>';
        $from = osc_contact_email() ;
        $from_name = osc_page_title() ;


        // EMAIL TO BUYER
        $mPages = new Page();
        $aPage = $mPages->findByInternalName('email_shop_sold_buyer');
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $price = ($item['f_price']*$txn['i_amount'])." ".$item['fk_c_currency_code'];
        $instructions = '';
        if($shop_item['b_accept_paypal']==1) {
            if($detail['b_accept_paypal']==1) {
                //$ENDPOINT     = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                $ENDPOINT     = 'https://www.paypal.com/cgi-bin/webscr';

                $r = rand(0,1000);
                $rpl = osc_item_id()."|".$amount."|".osc_item_price()."|".osc_item_currency()."|".$r;

                $RETURNURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'return.php?rpl=' . $rpl;
                $CANCELURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'cancel.php?rpl=' . $rpl;
                $NOTIFYURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'notify_url.php?rpl=' . $rpl;

                $instructions .= sprintf(__('Seller accepts Paypal as payment, click the button below or make a Paypal payment manually with the concept "%s"', 'shop'), $txn['s_code']);
                $instructions .= "<br /><br />";
                $instructions .= '<form action="'.$ENDPOINT.'" method="post" id="payment_'.$r.'">
                  <input type="hidden" name="cmd" value="_xclick" />
                  <input type="hidden" name="upload" value="1" />
                  <input type="hidden" name="business" value="'.osc_item_contact_email().'" />
                  <input type="hidden" name="item_name" value="'.osc_item_title().'" />
                  <input type="hidden" name="item_number" value="'.$transaction.'" />
                  <input type="hidden" name="amount" value="'.osc_item_price().'" />
                  <input type="hidden" name="quantity" value="'.$amount.'" />

                  <input type="hidden" name="currency_code" value="'.osc_item_currency().'" />
                  <input type="hidden" name="rm" value="2" />
                  <input type="hidden" name="no_note" value="1" />
                  <input type="hidden" name="charset" value="utf-8" />
                  <input type="hidden" name="return" value="'.$RETURNURL.'" />
                  <input type="hidden" name="notify_url" value="'.$NOTIFYURL.'" />
                  <input type="hidden" name="cancel_return" value="'.$CANCELURL.'" />
                  <input type="hidden" name="custom" value="'.$rpl.'" />
                </form>
                <div class="buttons">
                  <div class="right"><a id="button-confirm" class="button" onclick="$("#payment_'.$r.'").submit();"><span><img src="'.osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'paypal.gif" border="0" /></span></a></div>
                </div><br/><br/><br/>';
            };
            if($detail['b_accept_bank_transfer']==1) {
                $instructions .= sprintf(__('Seller accepts bank transfers as payment, please contact the seller to knowmore details about this payment option. Remember your transaction #ID is "%s"', 'shop'), $txn['s_code']);
                $instructions .= "<br /><br />";
                $instructions .= '<a href="'.osc_render_file_url(osc_plugin_folder(__FILE__)."contact.php?toid=".$seller['pk_i_id']."&related=".osc_item_id()).'" >'.__('Click here to contact seller', 'shop').'</a>';
                $instructions .= "<br /><br />";
            }
            };
        $words   = array();
        $words[] = array('{CONTACT_NAME}', '{USER_NAME}', '{USER_EMAIL}', '{USER_PHONE}',
                             '{WEB_URL}', '{ITEM_TITLE}','{ITEM_URL}', '{INSTRUCTIONS}','{PRICE}', '{TXN_CODE}');

        $words[] = array($item['s_contact_name'], $buyer['s_name'], $buyer['s_name'],
                         ($buyer['s_phone_land']==''?$buyer['s_phone_mobile']:$buyer['s_phone_land']), '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'], $item_url, $instructions, $price, $txn['s_code'] );

        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_shop_sold_buyer_title', $content['s_title'])), $words);
        $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_shop_sold_buyer_description', $content['s_text'])), $words);

        $emailParams = array (
                            'from'      => $from
                            ,'from_name' => $from_name
                            ,'subject'   => $title
                            ,'to'        => $buyer['s_email']
                            ,'to_name'   => $buyer['s_name']
                            ,'body'      => $body
                            ,'alt_body'  => $body
                            ,'reply_to'  => $from
                        ) ;

        osc_sendMail($emailParams);
        
        
        
        // EMAIL TO SELLER
        $mPages = new Page();
        $aPage = $mPages->findByInternalName('email_shop_sold_seller');
        $locale = osc_current_user_locale() ;

        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }


        $instructions = '';

        $words   = array();
        $words[] = array('{CONTACT_NAME}', '{USER_NAME}', '{USER_EMAIL}',
                             '{WEB_URL}', '{ITEM_TITLE}','{ITEM_URL}', '{BUYER_NAME}');

        $words[] = array($item['s_contact_name'], $seller['s_name'], $seller['s_name'],
                         '<a href="'.osc_base_url().'" >'.osc_base_url().'</a>', $item['s_title'], $item_url, $buyer['s_name']);

        $title = osc_mailBeauty(osc_apply_filter('email_title', osc_apply_filter('email_shop_sold_seller_title', $content['s_title'])), $words);
        $body = osc_mailBeauty(osc_apply_filter('email_description', osc_apply_filter('email_shop_sold_seller_description', $content['s_text'])), $words);

        $emailParams = array (
                            'from'      => $from
                            ,'from_name' => $from_name
                            ,'subject'   => $title
                            ,'to'        => $seller['s_email']
                            ,'to_name'   => $seller['s_name']
                            ,'body'      => $body
                            ,'alt_body'  => $body
                            ,'reply_to'  => $from
                        ) ;

        osc_sendMail($emailParams);
        
    }
    

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'shop_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'shop_uninstall');
    

    osc_add_hook('item_detail', 'shop_item_detail');

    osc_add_hook('item_form', 'shop_form');
    osc_add_hook('item_edit', 'shop_item_edit');
    osc_add_hook('item_form_post', 'shop_item_edit_post');
    osc_add_hook('item_edit_post', 'shop_item_edit_post');
    

    osc_add_hook('delete_item', 'shop_delete_item');
    
    
    osc_add_hook('user_menu', 'shop_user_menu');
      
?>