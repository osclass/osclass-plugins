<?php

    //set include
    define('ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
    require_once ABS_PATH . 'oc-load.php';

    /* * ***************************
     * CONFIGURATION - EDIT THIS *
     * *************************** */

    $sandbox = false; 
    /*if(osc_get_preference('sandbox', 'paypal')==1) {
        $sandbox = true;
    }*/
    $email_admin = false;//true;

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
            $conn = getConnection();
            $payment = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_paypal_log WHERE s_code = '%s'", DB_TABLE_PREFIX, Params::getParam('txn_id'));
            if (!isset($payment['pk_i_id'])) {
                // Payment is not processed yet
                $conn->osc_dbExec("INSERT INTO %st_shop_paypal_log (s_concept, dt_date, s_code, f_amount, s_currency_code, s_email, fk_i_transaction_id) VALUES ('%s', '%s', '%s', %f, '%s', '%s', %d)", DB_TABLE_PREFIX, Params::getParam('item_name'), date('Y-m-d H:i:s'), Params::getParam('txn_id'), Params::getParam('payment_gross'), Params::getParam('mc_currency'), Params::getParam('payer_email'), Params::getParam('item_number'));
                $t = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_transactions WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('item_number'));
                $conn->osc_dbExec("INSERT INTO %st_shop_log (fk_i_transaction_id, e_status, fk_i_user_id, dt_date) VALUES (%d, 'PAID', %d, '%s')", DB_TABLE_PREFIX, Params::getParam('item_number'), $t['fk_i_buyer_id'], date('Y-m-d H:i:s'));
                $conn->osc_dbExec("UPDATE %st_shop_transactions SET e_status = 'PAID' WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('item_number'));
            }

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