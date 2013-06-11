<?php
    if(osc_get_preference('pay_per_post', 'payment')) {
        // Load Item Information, so we could tell the user which item is he/she paying for
        $item = Item::newInstance()->findByPrimaryKey(Params::getParam('itemId'));
        if($item) {
            // Check if it's already payed or not
            if(!ModelPayment::newInstance()->publishFeeIsPaid(Params::getParam("itemId"))) {
                // Item is not paid, continue
                $category_fee = ModelPayment::newInstance()->getPublishPrice($item['fk_i_category_id']);
                if($category_fee > 0) {
                ?>

                <h1><?php _e('Continue the publish process', 'payment'); ?></h1>
                <div>
                    <div style="float:left; width: 50%;">
                        <label style="font-weight: bold;"><?php _e("Item's title", 'payment'); ?>:</label> <?php echo $item['s_title']; ?><br/>
                        <label style="font-weight: bold;"><?php _e("Item's description", 'payment'); ?>:</label> <?php echo $item['s_description']; ?><br/>
                    </div>
                    <div style="float:left; width: 50%;">
                        <?php _e("In order to make visible your ad to other users, it's required to pay a fee", 'payment'); ?>.<br/>
                        <?php echo sprintf(__('The current fee for this category is: %.2f %s', 'payment'), $category_fee, osc_get_preference('currency', 'payment')); ?><br/>
                        <?php if(osc_is_web_user_logged_in()) {
                                $wallet = ModelPayment::newInstance()->getWallet(osc_logged_user_id());
                                if(isset($wallet['formatted_amount']) && $wallet['formatted_amount']>=$category_fee) {
                                    wallet_button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                } else {
                                    if(osc_get_preference('paypal_enabled', 'payment')) {
                                        Paypal::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                    };
                                    if(osc_get_preference('blockchain_enabled', 'payment')) {
                                        Blockchain::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                    };
                                    if(osc_get_preference('braintree_enabled', 'payment')) {
                                        BraintreePayment::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                    };
                                }
                            } else {
                                if(osc_get_preference('paypal_enabled', 'payment')) {
                                    Paypal::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                }
                                if(osc_get_preference('blockchain_enabled', 'payment')) {
                                    Blockchain::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                }
                                if(osc_get_preference('braintree_enabled', 'payment')) {
                                    BraintreePayment::button($category_fee, sprintf(__('Publish fee for item %d at %s', 'payment'), $item['pk_i_id'], osc_page_title()), "101x".$item['fk_i_category_id']."x".$item['pk_i_id'], array('user' => $item['fk_i_user_id'], 'itemid' => $item['pk_i_id'], 'email' => $item['s_contact_email']));
                                }
                            };
                        ?>
                    </div>
                    <div style="clear:both;"></div>
                    <div name="result_div" id="result_div"></div>
                    <script type="text/javascript">
                        var rd = document.getElementById("result_div");
                    </script>
                </div>
                <?php
                } else {
                    // PRICE IS ZERO!
                    ?>
                    <h1><?php _e("There was an error", 'payment'); ?></h1>
                    <div>
                        <p><?php _e("There's no need to pay the publish fee", 'payment'); ?></p>
                    </div>
                    <?php
                }
            } else {
                // ITEM WAS ALREADY PAID! STOP HERE
                ?>
                <h1><?php _e('There was an error', 'payment'); ?></h1>
                <div>
                    <p><?php _e('The publish fee is already paid', 'payment'); ?></p>
                </div>
                <?php
            }
        } else {
            //ITEM DOES NOT EXIST! STOP HERE
            ?>
            <h1><?php _e('There was an error','payment'); ?></h1>
            <div>
                <p><?php _e('The item doesn not exists', 'payment'); ?></p>
            </div>
            <?php
        }
    } else {
        // NO NEED TO PAY AT ALL!
        ?>
        <h1><?php _e('There was an error', 'payment'); ?></h1>
        <div>
            <p><?php _e("There's no need to pay the publish fee", 'payment'); ?></p>
        </div>
        <?php
    }
?>