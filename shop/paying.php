<?php View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey(Params::getParam('item_id')));
$conn = getConnection();
$detail = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
$amount = min(Params::getParam('shop_amount')!=''?Params::getParam('shop_amount'):1, $detail['i_amount']);
if($amount<0) { $amount = 1; }; ?>
<div style="width:50%; float:left; height:150px;">
    <div class="odd">
        <?php if( osc_images_enabled_at_items() ) { ?>
         <div class="photo">
             <?php if(osc_count_item_resources()) { ?>
                <a href="<?php echo osc_item_url() ; ?>"><img src="<?php echo osc_resource_thumbnail_url() ; ?>" width="75px" height="56px" title="" alt="" /></a>
            <?php } else { ?>
                <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif') ; ?>" title="" alt="" />
            <?php } ?>
         </div>
         <?php } ?>
         <div class="text">
             <h3>
                 <a href="<?php echo osc_item_url() ; ?>"><?php echo osc_item_title() ; ?></a>
             </h3>
             <p>
                 <sdivong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; ?> - <?php } echo osc_item_city(); ?> (<?php echo osc_item_region(); ?>) - <?php echo osc_format_date(osc_item_pub_date()); ?></sdivong>
             </p>
             <p><?php echo osc_item_description(); ?></p>
         </div>
     </div>
</div>
<?php if(osc_item_user_id()!=null && osc_item_user_id()!=0) { ?>
<?php if(Params::getParam('step')=='done') { ?>
<div style="width:50%; float:left; height:150px;">
    <?php if(osc_item_user_id()!=osc_logged_user_id()) {
    echo sprintf(__('CONGRATULATIONS! You just bought %d units of %s at a total price of %s %s', 'shop'), $amount, osc_item_title(), ($amount*  osc_item_price()), osc_item_currency()); ?><br />
    <?php if(osc_is_web_user_logged_in()) {
        
        $shop_item = $conn->osc_dbFetchResult("SELECT i_amount FROM %st_shop_item WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id());
        if(isset($shop_item['i_amount'])) {
            if($amount>$shop_item['i_amount']) {
                $amount = $shop_item['i_amount'];
            }
                $txn_code = strtoupper(osc_genRandomPassword(12));
                $conn->osc_dbExec("INSERT INTO %st_shop_transactions (fk_i_item_id, fk_i_user_id, fk_i_buyer_id, i_amount, f_item_price, s_currency, e_status, s_code) VALUES (%d, %d, %d, %d, %f, '%s', 'SOLD', '%s')", DB_TABLE_PREFIX, osc_item_id(), osc_item_user_id(), osc_logged_user_id(), $amount, osc_item_price(), osc_item_currency(), $txn_code);
                $transaction = $conn->get_last_id();
                $conn->osc_dbExec("INSERT INTO %st_shop_log (fk_i_transaction_id, e_status, fk_i_user_id, dt_date) VALUES (%d, 'SOLD', %d, '%s')", DB_TABLE_PREFIX, $transaction, osc_item_user_id(), date('Y-m-d H:i:s'));
                $conn->osc_dbExec("UPDATE %st_shop_item SET i_amount = %d WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $shop_item['i_amount']-$amount, osc_item_id());
                $seller = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_user WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_item_user_id());
                $buyer = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_user WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_logged_user_id());
                $conn->osc_dbExec("UPDATE %st_shop_user SET i_total_sales = %d WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, $seller['i_total_sales']+1, $seller['fk_i_user_id']);
                $conn->osc_dbExec("UPDATE %st_shop_user SET i_total_buys = %d WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, $buyer['i_total_buys']+1, $buyer['fk_i_user_id']);
                shop_send_sold_email($transaction);

            if($detail['b_accept_paypal']==1) {
                //$ENDPOINT     = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                $ENDPOINT     = 'https://www.paypal.com/cgi-bin/webscr';

                $r = rand(0,1000);
                $rpl = osc_item_id()."|".$amount."|".osc_item_price()."|".osc_item_currency()."|".$r;

                $RETURNURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'return.php?rpl=' . $rpl;
                $CANCELURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'cancel.php?rpl=' . $rpl;
                $NOTIFYURL = osc_base_url(true) . '?page=custom&file=' . osc_plugin_folder(__FILE__) . 'notify_url.php?rpl=' . $rpl;

            ?>


                <form action="<?php echo $ENDPOINT; ?>" method="post" id="payment_<?php echo $r; ?>">
                  <input type="hidden" name="cmd" value="_xclick" />
                  <input type="hidden" name="upload" value="1" />
                  <input type="hidden" name="business" value="<?php echo osc_item_contact_email(); ?>" />
                  <input type="hidden" name="item_name" value="<?php echo osc_item_title(); ?>" />
                  <input type="hidden" name="item_number" value="<?php echo $transaction; ?>" />
                  <input type="hidden" name="amount" value="<?php echo osc_item_price(); ?>" />
                  <input type="hidden" name="quantity" value="<?php echo $amount; ?>" />

                  <input type="hidden" name="currency_code" value="<?php echo osc_item_currency(); ?>" />
                  <input type="hidden" name="rm" value="2" />
                  <input type="hidden" name="no_note" value="1" />
                  <input type="hidden" name="charset" value="utf-8" />
                  <input type="hidden" name="return" value="<?php echo $RETURNURL; ?>" />
                  <input type="hidden" name="notify_url" value="<?php echo $NOTIFYURL; ?>" />
                  <input type="hidden" name="cancel_return" value="<?php echo $CANCELURL; ?>" />
                  <input type="hidden" name="custom" value="<?php echo $rpl; ?>" />
                </form>
                <div class="buttons">
                  <div class="right"><a id="button-confirm" class="button" onclick="$('#payment_<?php echo $r; ?>').submit();"><span><img src='<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__); ?>paypal.gif' border='0' /></span></a></div>
                </div>
            <?php }; ?>
            <?php if($detail['b_accept_bank_transfer']==1) { 
                $instructions = sprintf(__('Seller accepts bank transfers as payment, please contact the seller to knowmore details about this payment option. Remember your transaction #ID is "%s"', 'shop'), $txn_code);
                $instructions .= "<br /><br />";
                $instructions .= '<a href="'.osc_render_file_url(osc_plugin_folder(__FILE__)."contact.php?toid=".osc_item_user_id()."&related=".osc_item_id()).'" >'.__('Click here to contact seller', 'shop').'</a>';
                $instructions .= "<br /><br />";
                echo $instructions;
            }; ?>
            <br />
            <?php _e('Contact the seller to gather more information about payment methods allowed', 'shop'); ?>
            <?php /* CODE FOR PRIVATE MESSAGES MODULE */ ?>
            <a href="<?php echo osc_base_url(true).'?page=item&action=contact&id='.osc_item_id(); ?>" ><?php _e('Contact seller', 'shop'); ?></a>
        <?php } else { 
            _e('Some error ocurred during the transaction, we were unabled to proccess it. Please try again re-loading the page', 'shop');
        }; ?>
    <?php };
    } else {
        _e('Some error ocurred. You can not buy your own products', 'shop');
    } ?>
</div>
<?php } else { ?>
<div style="width:50%; float:left; height:150px;">
    <?php if(osc_is_web_user_logged_in()) {
        echo sprintf(__('You are going to buy %d units of %s at a total price of %s %s', 'shop'), $amount, osc_item_title(), ($amount*  osc_item_price()), osc_item_currency()); ?><br />
        <?php _e('This action can not be undone', 'shop'); ?><br />
        <form action="<?php echo osc_base_url(true)?>" method="POST" >
            <input type="hidden" name="page" value="custom" />
            <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>paying.php" />
            <input type="hidden" name="step" value="done" />
            <input type="hidden" name="item_id" value="<?php echo osc_item_id(); ?>" />
            <input type="hidden" name="shop_amount" value="<?php echo $amount; ?>" />
            <input type="submit" value="<?php _e('Buy this item','shop')?>" />
        </form>
    <?php } else {
        _e('You need to login in order to buy this item', 'shop');
        ?>
        <form id="login" action="<?php echo osc_base_url(true) ; ?>" method="post">
            <fieldset>
                <input type="hidden" name="page" value="login" />
                <input type="hidden" name="action" value="login_post" />
                <input type="hidden" name="http_referer" value="<?php echo osc_base_url(true)."?page=custom&file=".osc_plugin_folder(__FILE__)."paying.php&item_id=".osc_item_id()."&shop_amount=".$amount; ?>" />
                <label for="email"><?php _e('E-mail', 'modern') ; ?></label>
                <?php UserForm::email_login_text() ; ?>
                <label for="password"><?php _e('Password', 'modern') ; ?></label>
                <?php UserForm::password_login_text() ; ?>
                <p class="checkbox"><?php UserForm::rememberme_login_checkbox();?> <label for="rememberMe"><?php _e('Remember me', 'modern') ; ?></label></p>
                <button type="submit"><?php _e('Log in', 'modern') ; ?></button>
                <div class="forgot">
                    <a href="<?php echo osc_recover_user_password_url() ; ?>"><?php _e("Forgot password?", 'modern');?></a>
                </div>
            </fieldset>
        </form>
    <?php }; ?>
</div>
<?php }; ?>
<div style="clear:both;"></div>
<?php } else { ?>
<div style="width:50%; float:left; height:150px;">
    <?php _e('Some error ocurred, we can not process the payment right now', 'shop'); ?>
</div>
<?php } ?>
