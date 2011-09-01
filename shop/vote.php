<div class="content user_account">
    <h1>
        <strong><?php _e('User account manager', 'shop') ; ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu() ; ?>
    </div>
    <div id="main">
        <h2><?php _e('Rate transaction', 'shop'); ?></h2>
<?php 
$conn = getConnection();
$transaction = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_transactions WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('txn_id'));
$whoami = '';
if(osc_logged_user_id()==$transaction['fk_i_user_id'] && Params::getParam('paction')=='vote_buyer') { //IM SELLER
    $whoami = 'seller';
} else if(osc_logged_user_id()==$transaction['fk_i_buyer_id'] && Params::getParam('paction')=='vote_seller') { //IM BUYER
    $whoami = 'buyer';
}
if(isset($transaction['pk_i_id']) && ($whoami=='seller' || $whoami=='buyer')) {
View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($transaction['fk_i_item_id'])); ?>
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
<?php if(Params::getParam('step')=='done') { ?>
<div style="width:50%; float:left; height:150px;">
    <?php
    $conn = getConnection();
    if($whoami=='seller') {
        $conn->osc_dbExec("UPDATE %st_shop_transactions SET i_buyer_score = %d, s_seller_comment = '%s', e_status = 'ENDED' WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('i_score'), Params::getParam('s_comment'), Params::getParam('txn_id'));
        $conn->osc_dbExec("INSERT INTO %st_shop_log (fk_i_transaction_id, e_status, fk_i_user_id, dt_date) VALUES (%d, 'ENDED', %d, '%s')", DB_TABLE_PREFIX, Params::getParam('txn_id'), osc_item_user_id(), date('Y-m-d H:i:s'));
    } else {
        $conn->osc_dbExec("UPDATE %st_shop_transactions SET i_seller_score = %d, s_buyer_comment = '%s', e_status = 'VOTE_BUYER' WHERE pk_i_id = %d", DB_TABLE_PREFIX, Params::getParam('i_score'), Params::getParam('s_comment'), Params::getParam('txn_id'));
        $conn->osc_dbExec("INSERT INTO %st_shop_log (fk_i_transaction_id, e_status, fk_i_user_id, dt_date) VALUES (%d, 'VOTE_BUYER', %d, '%s')", DB_TABLE_PREFIX, Params::getParam('txn_id'), osc_item_user_id(), date('Y-m-d H:i:s'));
    }
    _e('Thanks! The transaction has been updated correctly', 'shop');
    ?>
</div>
<?php } else { ?>
<div style="width:50%; float:left; height:150px;">
    <form method="POST" action="<?php echo osc_base_url(true); ?>" >
        <input type="hidden" name="page" value="custom" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>vote.php" />
        <input type="hidden" name="step" value="done" />
        <input type="hidden" name="paction" value="<?php echo Params::getParam('paction'); ?>" />
        <input type="hidden" name="txn_id" value="<?php echo Params::getParam('txn_id');?>" />
        <?php if($whoami=='seller') {
            _e('Rate the transaction with the buyer and comment it', 'shop');
        } else {
            _e('Rate the transaction with the seller and comment it', 'shop');
        }
        ?>
        <select name="i_score">
            <option value="5">5 - Highest</option>
            <option value="4">4</option>
            <option value="3">3</option>
            <option value="2">2</option>
            <option value="1">1 - Lowest</option>
        </select>
        <br />
        <?php _e('Leave a comment of your overall experience', 'shop'); ?>
        <input type="text" name="s_comment" />
        <br />
        <input type="submit" value="<?php _e('Rate', 'shop');?>" />
    </form>
</div>
<?php }; ?>
<div style="clear:both;"></div>
<?php } else { ?>

                <?php _e('Some error ocurred or you already have voted this transaction', 'shop'); ?>

<?php } ?>
        </div>
    </div>