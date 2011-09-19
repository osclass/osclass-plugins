<?php
    /*
     * return.php
     *
     * This page will handle the GetECDetails, and DoECPayment API Calls
     */
    
        
    $rpl = explode('|', Params::getParam('custom'));
    $item = Item::newInstance()->findByPrimaryKey($rpl[0]);
    View::newInstance()->_exportVariableToView('item', $item);
    
    if(Params::getParam('payment_status')=='Completed') {

        $conn = getConnection();
        $payment = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_paypal_log WHERE s_code = '%s'", DB_TABLE_PREFIX, Params::getParam('txn_id'));
        if (!isset($payment['pk_i_id'])) {
            // Payment is not processed yet
            $conn->osc_dbExec("INSERT INTO %st_shop_paypal_log (s_concept, dt_date, s_code, f_amount, s_currency_code, s_email, fk_i_transaction_id) VALUES ('%s', '%s', '%s', %f, '%s', '%s', %d)", DB_TABLE_PREFIX, Params::getParam('item_name'), date('Y-m-d H:i:s'), Params::getParam('txn_id'), Params::getParam('payment_gross'), Params::getParam('mc_currency'), Params::getParam('payer_email'), Params::getParam('item_number'));
            $t = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_transactions WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('item_number'));
            $conn->osc_dbExec("INSERT INTO %st_shop_log (fk_i_transaction_id, e_status, fk_i_user_id, dt_date) VALUES (%d, 'PAID', %d, '%s')", DB_TABLE_PREFIX, Params::getParam('item_number'), $t['fk_i_buyer_id'], date('Y-m-d H:i:s'));
            $conn->osc_dbExec("UPDATE %st_shop_transactions SET e_status = 'PAID' WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('item_number'));
        }
        _e('Payment processed correctly', 'shop');
    } else {
        _e('There were an error, please contact the seller', 'shop');
    } ?>
    <br />
    <a href="<?php echo osc_item_url(); ?>" ><?php _e('Click here', 'shop'); ?></a> <?php _e('to continue', 'shop'); ?>
<?php       
?>