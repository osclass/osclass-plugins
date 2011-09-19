<div>
    <?php $rpl = explode("|", Params::getParam('rpl'));
    View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($rpl[0]));
    _e('You cancelled the payment', 'shop'); ?><br />
    <a href="<?php echo osc_item_url();?>"><?php _e("Click here", 'shop');?></a> <?php _e('to continue', 'shop'); ?>
</div>