<?php 
$from = User::newInstance()->findByPrimaryKey(osc_logged_user_id());
$to = User::newInstance()->findByPrimaryKey(Params::getParam('toid'));
$item_id = Params::getParam('related');
if(Params::getParam('step')!='done') {
    if($item_id) {
        View::newInstance()->_exportVariableToView('item', Item::newInstance()->findByPrimaryKey($item_id));
    }
    

?>
<div class="content user_account">
    <h1>
        <strong><?php _e('User account manager', 'shop') ; ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu() ; ?>
    </div>
    <div id="main">
        <h2><?php echo sprintf(__('Contact %s', 'shop'), $to['s_name']); ?></h2>
        <form method="POST" action="<?php echo osc_base_url(true);?>" >
        <input type="hidden" name="page" value="custom" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>contact.php" />
        <input type="hidden" name="step" value="done" />
        <?php _e('Message', 'shop'); ?>
        <br />
        <textarea name="s_comment" rows="6" cols="60" ></textarea>
        <br />
        <input type="submit" value="<?php _e('Send message', 'shop');?>" />
        </form>
    </div>
</div>
<?php } else {
    $conn = getConnection();
    $conn->osc_dbExec("INSERT INTO %st_shop_message (`fk_i_item_id`, `fk_i_from_id`, `fk_i_to_id`, `s_comment`, `dt_date`) VALUES (%d, %d, %d, '%s', '%s')", DB_TABLE_PREFIX, $item_id==''?NULL:$item_id, osc_logged_user_id(), $to['pk_i_id'], Params::getParam('s_comment'), date('Y-m-d H:i:s'));
    
    shop_send_contact_email($from, $to, Params::getParam('s_comment'), $item_id);
    
    
    ?>
<div class="content user_account">
    <h1>
        <strong><?php _e('User account manager', 'shop') ; ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu() ; ?>
    </div>
    <div id="main">
        <h2><?php _e('Your message has been sent correctly', 'shop'); ?></h2>
    </div>
</div>
<?php } ?>
