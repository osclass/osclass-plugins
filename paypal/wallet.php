<?php 
    $url = '';
    if(osc_is_web_user_logged_in()) {
        $conn = getConnection();
        $rpl = explode('|', Params::getParam('rpl'));
        $product_type = explode('x', Params::getParam("inumber"));
        $item = Item::newInstance()->findByPrimaryKey($rpl[1]);
        $wallet = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_wallet WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_logged_user_id());
        $category_fee = 0;
        if(osc_logged_user_id()==$item['fk_i_user_id']) {
            $ppl_category = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_prices WHERE fk_i_category_id = %d", DB_TABLE_PREFIX, $item['fk_i_category_id']);
            if ($product_type[0] == '101') {
                if(!paypal_is_paid($item['pk_i_id'])) {
                    if($ppl_category && isset($ppl_category['f_publish_cost'])) {
                        $category_fee = $ppl_category['f_publish_cost'];
                    } else {
                        $category_fee = osc_get_preference('default_publish_cost', 'paypal');
                    }
                }
            } else if ($product_type[0] == '201') {
                if(!paypal_is_premium($item['pk_i_id'])) {
                    if($ppl_category && isset($ppl_category['f_premium_cost']) && $ppl_category['f_premium_cost']>0) {
                        $category_fee = $ppl_category['f_premium_cost'];
                    } else {
                        $category_fee = osc_get_preference('default_premium_cost', 'paypal');
                    }
                }
            }
        }
        if($category_fee > 0 && $wallet['f_amount']>$category_fee) {

            $paypal_id    = paypal_save_log(Params::getParam("desc"), 'wallet_'.date("YmdHis"), $category_fee, osc_get_preference("currency", "paypal"), $rpl[2], $rpl[0], $rpl[1], $product_type[0], 'WALLET');
            $conn->osc_dbExec("UPDATE %st_paypal_wallet SET f_amount = '%f' WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, ($wallet['f_amount'] - $category_fee),  osc_logged_user_id());
            if ($product_type[0] == '101') {
                // PUBLISH FEE
                $paid = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_publish WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $rpl[1]);
                if ($paid) {
                    $conn->osc_dbExec("UPDATE %st_paypal_publish SET dt_date = '%s', b_paid =  '1', fk_i_paypal_id = '%d' WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, date('Y-m-d H:i:s'), $paypal_id, $rpl[1]);
                } else {
                    $conn->osc_dbExec("INSERT INTO  %st_paypal_publish (fk_i_item_id, dt_date, b_paid, fk_i_paypal_id) VALUES ('%d',  '%s', 1, '%s')", DB_TABLE_PREFIX, $rpl[1], date('Y-m-d H:i:s'), $paypal_id);
                }

                Item::newInstance()->update(array('b_enabled' => 1), array('pk_i_id' => $rpl[1]));
                $item     = Item::newInstance()->findByPrimaryKey($rpl[1]);
                $category = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
                View::newInstance()->_exportVariableToView('category', $category);
                $url = osc_search_category_url();
            } else if ($product_type[0] == '201') {
                // PREMIUM FEE
                $paid = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_premium WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $rpl[1]);
                if ($paid) {
                    $conn->osc_dbExec("UPDATE %st_paypal_premium SET dt_date = '%s', fk_i_paypal_id = '%d' WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, date('Y-m-d H:i:s'), $paypal_id, $rpl[1]);
                } else {
                    $conn->osc_dbExec("INSERT INTO  %st_paypal_premium (fk_i_item_id, dt_date, fk_i_paypal_id) VALUES ('%d',  '%s',  '%s')", DB_TABLE_PREFIX, $rpl[1], date('Y-m-d H:i:s'), $paypal_id);
                }
                $mItem = new ItemActions(false);
                $mItem->premium($item['pk_i_id'], true);

                $url = osc_render_file_url(osc_plugin_folder(__FILE__) . 'user_menu.php');
            } else {
                // PUBLISH/PREMIUM PACKS
                $wallet = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_wallet WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, $rpl[0]);
                if(isset($wallet['f_amount'])) {
                    $conn->osc_dbExec("UPDATE %st_paypal_wallet SET f_amount = '%f' WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, ($wallet['f_amount'] + urldecode($doresponse['PAYMENTINFO_0_AMT'])),$rpl[0]);
                } else {
                    $conn->osc_dbExec("INSERT INTO  %st_paypal_wallet (`fk_i_user_id`, `f_amount`) VALUES ('%d',  '%f')", DB_TABLE_PREFIX, $rpl[0], urldecode($doresponse['PAYMENTINFO_0_AMT']));
                }

                $url = osc_render_file_url(osc_plugin_folder(__FILE__) . 'user_menu.php');
            }
        }
    }
    
    if($url!='') {
        osc_add_flash_ok_message(__('Payment processed correctly', 'paypal'));
        paypal_js_redirect_to($url);
    } else {
        osc_add_flash_ok_message(__('There were some errors, please try again later or contact the administrators', 'paypal'));
        paypal_js_redirect_to(osc_render_file_url(osc_plugin_folder(__FILE__) . 'user_menu.php'));
    }
?>
