<h3 style="margin-left: 40px;margin-top: 20px;"><?php _e('Shop options', 'shop'); ?></h3>
<div class="box">
    <div class="box dg_files">
        <form method="POST" action="<?php echo osc_base_url(true); ?>">
            <input type="hidden" name="item_id" value="<?php echo osc_item_id(); ?>" />
            <input type="hidden" name="page" value="custom" />
            <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__) ?>paying.php" />
            <div><?php if(isset($detail) && isset($detail['i_amount']) && $detail['i_amount']>0) {
                if($detail['i_amount']>1) { ?>
                    <input type="text" name="shop_amount" value="1" onKeyPress="return numbersonly(this, event)"/> <?php echo sprintf(__('of %d items', 'shop'), $detail['i_amount'])?>
                <?php } ?>
                    <br />
                    <input type="submit" value="<?php _e('Buy!', 'shop')?>" />
            <?php } else { ?>
                <strong><?php _e('Item sold', 'shop'); ?></strong>
            <?php }; ?></div>
            <?php if(isset($detail) && isset($detail['b_accept_paypal']) && $detail['b_accept_paypal']==1) { ?>
                <div><?php _e('The seller accepts Paypal as payment', 'shop'); ?></div><br />
            <?php }; ?>
            <?php if(isset($detail) && isset($detail['b_accept_bank_transfer']) && $detail['b_accept_bank_transfer']==1) { ?>
                <div><?php _e('The seller accepts bank transfer as payment', 'shop'); ?></div><br />
            <?php }; ?>
        </form>
    </div>
</div>
<SCRIPT TYPE="text/javascript">
function numbersonly(myfield, e, dec)
{
var key;
var keychar;

if (window.event)
   key = window.event.keyCode;
else if (e)
   key = e.which;
else
   return true;
keychar = String.fromCharCode(key);

// control keys
if ((key==null) || (key==0) || (key==8) || 
    (key==9) || (key==13) || (key==27) )
   return true;

// numbers
else if ((("0123456789").indexOf(keychar) > -1))
   return true;

// decimal point jump
else if (dec && (keychar == "."))
   {
   myfield.form.elements[dec].focus();
   return false;
   }
else
   return false;
}

</SCRIPT>