<?php

    //set include
    define('ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
    require_once ABS_PATH . 'oc-load.php';
    require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';

    /* * ***************************
     * CONFIGURATION - EDIT THIS *
     * *************************** */

    $sandbox = false; 
    $email_admin = false;
    if(osc_get_preference('sandbox', 'paypal')==1) {
        $sandbox = true;
        $email_admin = true;
    }

    /* * ****************************
     * STANDARD PAYPAL NOTIFY URL *
     *    NOT MODIFY BELOW CODE   *
     * **************************** */
    // Read the post from PayPal and add 'cmd'
    $header = '';
    $req    = 'cmd=_notify-validate';
    if (function_exists('get_magic_quotes_gpc')) {
        $get_magic_quotes_exists = true;
    } else {
        $get_magic_quotes_exists = false;
    }
    
    foreach ($_POST as $key => $value) {
        // Handle escape characters, which depends on setting of magic quotes 
        if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
            $value = urlencode(stripslashes($value));
        } else {
            $value = urlencode($value);
        }
        if($key!='rpl') {
            $req .= "&$key=$value";
        }
    }

    // Post back to PayPal to validate
    if(!$sandbox) {
        $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
    } else {
        $curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
    }

    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $req);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
    $res = curl_exec($curl);
    if (strcmp($res, 'VERIFIED') == 0) {
        if ($_REQUEST['payment_status'] == 'Completed') {
            // Have we processed the payment already?
            $conn    = getConnection();
            $payment = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_log WHERE s_code = '%s'", DB_TABLE_PREFIX, Params::getParam('txn_idn'));
            if (!isset($payment['pk_i_id'])) {
                    $rpl = explode('|', Params::getParam('custom'));
                    $product_type = explode('x', Params::getParam('item_number'));
                    $paypal_id    = paypal_save_log(Params::getParam('item_name'), Params::getParam('txn_id'), Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'), Params::getParam('mc_currency'), Params::getParam('payer_email')!=''?Params::getParam('payer_email'):'', $rpl[0], $rpl[1]=='dash'?0:$rpl[1], $product_type[0], 'PAYPAL');
                    if ($product_type[0] == '101') {
                        // PUBLISH FEE
                        $conn = getConnection();
                        $paid = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_publish WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $rpl[1]);
                        if ($paid) {
                            $conn->osc_dbExec("UPDATE %st_paypal_publish SET dt_date = '%s', b_paid =  '1', fk_i_paypal_id = '%d' WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, date('Y-m-d H:i:s'), $paypal_id, $rpl[1]);
                        } else {
                            $conn->osc_dbExec("INSERT INTO  %st_paypal_publish (fk_i_item_id, dt_date, b_paid, fk_i_paypal_id) VALUES ('%d',  '%s', 1, '%s')", DB_TABLE_PREFIX, $rpl[1], date('Y-m-d H:i:s'), $paypal_id);
                        }


                        Item::newInstance()->update(array('b_enabled' => 1), array('pk_i_id' => $rpl[1]));
                    } else if ($product_type[0] == '201') {
                        // PREMIUM FEE
                        $conn = getConnection();
                        $paid = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_premium WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $rpl[1]);
                        if ($paid) {
                            $conn->osc_dbExec("UPDATE %st_paypal_premium SET dt_date = '%s', fk_i_paypal_id = '%d' WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, date('Y-m-d H:i:s'), $paypal_id, $rpl[1]);
                        } else {
                            $conn->osc_dbExec("INSERT INTO  %st_paypal_premium (fk_i_item_id, dt_date, fk_i_paypal_id) VALUES ('%d',  '%s',  '%s')", DB_TABLE_PREFIX, $rpl[1], date('Y-m-d H:i:s'), $paypal_id);
                        }
                        $mItem = new ItemActions(false);
                        $mItem->premium($rpl[1], true);
                    } else {
                        // PUBLISH/PREMIUM PACKS
                        $conn = getConnection();
                        $wallet = $conn->osc_dbFetchResult("SELECT * FROM %st_paypal_wallet WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, $rpl[0]);
                        if(isset($wallet['f_amount'])) {
                            $conn->osc_dbExec("UPDATE %st_paypal_wallet SET f_amount = '%f' WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, ($wallet['f_amount'] + (Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'))),$rpl[0]);
                        } else {
                            $conn->osc_dbExec("INSERT INTO  %st_paypal_wallet (`fk_i_user_id`, `f_amount`) VALUES ('%d',  '%f')", DB_TABLE_PREFIX, $rpl[0], Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'));
                        }
                    }
            } // ELSE THE PAY IS ALREADY PROCESSED

            if($email_admin) {
                $emailtext = '';
                foreach ($_REQUEST as $key => $value) {
                    $emailtext .= $key . ' = ' . $value . '\n\n';
                }
                mail(osc_contact_email() , 'OSCLASS PAYPAL DEBUG', $emailtext . '\n\n ---------------- \n\n' . $req);
            }
        }
    } else if (strcmp($res, 'INVALID') == 0) {
        // INVALID: Do nothing
    }
    
?>