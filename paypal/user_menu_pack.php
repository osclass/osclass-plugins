<?php 
    $packs = array();
    if(osc_get_preference("pack_price_1", "paypal")!='' && osc_get_preference("pack_price_1", "paypal")!='0') {
        $packs[] = osc_get_preference("pack_price_1", "paypal");
    }
    if(osc_get_preference("pack_price_2", "paypal")!='' && osc_get_preference("pack_price_2", "paypal")!='0') {
        $packs[] = osc_get_preference("pack_price_2", "paypal");
    }
    if(osc_get_preference("pack_price_3", "paypal")!='' && osc_get_preference("pack_price_3", "paypal")!='0') {
        $packs[] = osc_get_preference("pack_price_3", "paypal");
    }
    $user = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
    $conn = getConnection();
    $wallet = $conn->osc_dbFetchresult("SELECT * FROM %st_paypal_wallet WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, osc_logged_user_id());
    $amount = isset($wallet['f_amount'])?$wallet['f_amount']:0;
?>
            <div class="content user_account">
                <h1>
                    <strong><?php _e('User account manager', 'paypal') ; ?></strong>
                </h1>
                <div id="sidebar">
                    <?php echo osc_private_user_menu() ; ?>
                </div>
                <div id="main">
                    <h2><?php echo sprintf(__('Credit packs. Your current credit is %.2f %s', 'paypal'), $amount, osc_get_preference('currency', 'paypal')); ?></h2>
                    <?php $pack_n = 0;
                        foreach($packs as $pack) { $pack_n++; ?>
                        <div>
                            <h3><?php echo sprintf(__('Credit pack #%d', 'paypal'), $pack_n); ?></h3>
                            <div style="float:left;width:200px"><label><?php _e("Price", "paypal");?>:</label> <?php echo $pack." ".osc_get_preference('currency', 'paypal'); ?>
                            </div>
                            <div style="float:left;">
                                <?php paypal_button($pack, sprintf(__("Credit for %s %s at %s", "paypal"), $pack, osc_get_preference("currency", "paypal"), osc_page_title()), $user['pk_i_id']."|dash|".$user['s_email'], "301x".$pack); ?>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <br/>
                    <?php } ?>
                    <div name="result_div" id="result_div"></div>
                    <script type="text/javascript">
                        var rd = document.getElementById("result_div");
                    </script>
                </div>
            </div>