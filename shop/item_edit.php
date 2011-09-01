<h2><?php _e("Shop options", 'shop');?></h2>

<div class="box">
    <div class="box dg_files">
        <label for="shop_amount"><?php _e('Amount of items', 'shop'); ?></label>
        <input type="text" name="shop_amount" value="<?php echo @$detail['i_amount']; ?>" />
        <br />
        <input style="width: 20px;" type="checkbox" name="shop_accept_paypal" id="shop_accept_paypal" value="1" <?php if(@$detail['b_accept_paypal'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="shop_accept_paypal"><?php _e('Accept Paypal', 'shop'); ?></label>
        <br />
        <input style="width: 20px;" type="checkbox" name="shop_accept_bank_transfer" id="shop_accept_bank_transfer" value="1" <?php if(@$detail['b_accept_bank_transfer'] == 1) { echo 'checked="yes"'; } ?>/> <label style="float:none;" for="shop_accept_bank_transferm"><?php _e('Accept bank transfer', 'shop'); ?></label>        
    </div>
</div>