<?php
    $packs = array();
    if(osc_get_preference("pack_price_1", "payment")!='' && osc_get_preference("pack_price_1", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_1", "payment");
    }
    if(osc_get_preference("pack_price_2", "payment")!='' && osc_get_preference("pack_price_2", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_2", "payment");
    }
    if(osc_get_preference("pack_price_3", "payment")!='' && osc_get_preference("pack_price_3", "payment")!='0') {
        $packs[] = osc_get_preference("pack_price_3", "payment");
    }
    $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
    $wallet = ModelPayment::newInstance()->getWallet(osc_logged_user_id());
    $amount = isset($wallet['formatted_amount'])?$wallet['formatted_amount']:0;

    if(osc_get_preference('currency', 'payment')=='BTC') {
        $formatted_amount = payment_format_btc($amount);
    } else {
        $formatted_amount = osc_format_price($amount*1000000, osc_get_preference('currency', 'payment'));
    }

    print_r($_REQUEST);

?>
<div class="content user_account">
    <h1>
        <strong><?php _e('User account manager', 'payment') ; ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu() ; ?>
    </div>
    <div id="main">
        <h2><?php echo sprintf(__('Credit packs. Your current credit is %s', 'payment'), $formatted_amount); ?></h2>
        <?php $pack_n = 0;
        foreach($packs as $pack) { $pack_n++; ?>
            <div>
                <h3><?php echo sprintf(__('Credit pack #%d', 'payment'), $pack_n); ?></h3>
                <div style="float:left;width:200px"><label><?php _e("Price", "payment");?>:</label> <?php echo $pack." ".osc_get_preference('currency', 'payment'); ?>
                </div>
                <?php if(osc_get_preference('paypal_enabled', 'payment')==1) {?>
                    <div>
                        <?php Paypal::button($pack, sprintf(__("Credit for %s %s at %s", "payment"), $pack, osc_get_preference("currency", "payment"), osc_page_title()), '301x'.$pack, array('user' => $user['pk_i_id'], 'itemid' => $user['pk_i_id'], 'email' => $user['s_email'])); ?>
                    </div>
                <?php };
                if(osc_get_preference('blockchain_enabled', 'payment')==1) {?>
                    <div>
                        <?php Blockchain::button($pack, sprintf(__("Credit for %s %s at %s", "payment"), $pack, osc_get_preference("currency", "payment"), osc_page_title()), '301x'.$pack, array('user' => $user['pk_i_id'], 'itemid' => $user['pk_i_id'], 'email' => $user['s_email'])); ?>
                   </div>
                <?php }; ?>
                <div>
                    <?php BraintreePayment::button($pack, sprintf(__("Credit for %s %s at %s", "payment"), $pack, osc_get_preference("currency", "payment"), osc_page_title()), '301x'.$pack, array('user' => $user['pk_i_id'], 'itemid' => $user['pk_i_id'], 'email' => $user['s_email'])); ?>
                </div>
                <?php /* <div>
                    <?php Payza::button($pack, sprintf(__("Credit for %s %s at %s", "payment"), $pack, osc_get_preference("currency", "payment"), osc_page_title()), '301x'.$pack, array('user' => $user['pk_i_id'], 'itemid' => $user['pk_i_id'], 'email' => $user['s_email'])); ?>
                </div> */ ?>
            </div>
            <div style="clear:both;"></div>
            <br/>
        <?php } ?>
        <div name="result_div" id="result_div"></div>
        <script type="text/javascript">
            var rd = document.getElementById("result_div");
        </script>
        <?php BraintreePayment::dialogJS(); ?>
    </div>
</div>