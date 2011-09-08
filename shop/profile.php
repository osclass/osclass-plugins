<?php View::newInstance()->_exportVariableToView('user', User::newInstance()->findByPrimaryKey(Params::getParam('user_id')));
$conn = getConnection();
$u = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_user WHERE fk_i_user_id = %d", DB_TABLE_PREFIX, Params::getParam('user_id'));
$items = $conn->osc_dbFetchResults("SELECT * FROM %st_item i, %st_shop_item si WHERE i.fk_i_user_id = %d AND si.fk_i_item_id = i.pk_i_id AND si.i_amount > 0 LIMIT 5", DB_TABLE_PREFIX, DB_TABLE_PREFIX, Params::getParam('user_id'));
$items = Item::newInstance()->extendData($items);
View::newInstance()->_exportVariableToView('items', $items);
?>
<h1><?php echo sprintf(__('My world : %s', 'shop'), osc_user_name()); ?></h1>
<div style="clear:both;"></div>
<div style="width:30%; float:left;">
    <br />
    <?php echo sprintf(__('Reg. date: %s', 'shop'), osc_user_regdate()); ?><br />
    <?php echo sprintf(__('Location: %s', 'shop'), osc_user_city()); ?><br />
    <?php echo sprintf(__('Score: %s', 'shop'), $u['f_score']); ?><br />
    <br />
    <br />
    <ul>
        <li><a href="<?php echo osc_search_url(array('sUser' => osc_user_id()));?>" ><?php echo sprintf(__("More items from %s", "shop"), osc_user_name()); ?></a></li>
        <li><a href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__)."favorites.php?add=".osc_user_id()); ?>" ><?php _e("Add seller to favorites", "shop"); ?></a></li>
        <li><a href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__)."contact.php?toid=".osc_user_id()); ?>" ><?php _e("Contact seller", "shop"); ?></a></li>
    </ul>
</div>
<div style="width:70%; float:left;">
    <h2><?php _e('Items'); ?></h2>
    <table border="0" cellspacing="0">
        <tbody>
            <?php $class = "even" ; ?>
            <?php while(osc_has_items()) { ?>
                <tr class="<?php echo $class; ?>">
                    <?php if( osc_images_enabled_at_items() ) { ?>
                     <td class="photo">
                         <?php if(osc_count_item_resources()) { ?>
                            <a href="<?php echo osc_item_url() ; ?>"><img src="<?php echo osc_resource_thumbnail_url() ; ?>" width="75px" height="56px" title="" alt="" /></a>
                        <?php } else { ?>
                            <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif') ; ?>" title="" alt="" />
                        <?php } ?>
                     </td>
                     <?php } ?>
                     <td class="text">
                         <h3>
                             <a href="<?php echo osc_item_url() ; ?>"><?php echo osc_item_title() ; ?></a>
                         </h3>
                         <p>
                             <strong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; ?> - <?php } echo osc_item_city(); ?> (<?php echo osc_item_region(); ?>) - <?php echo osc_format_date(osc_item_pub_date()); ?></strong>
                         </p>
                         <p><?php echo osc_highlight( strip_tags( osc_item_description() ) ) ; ?></p>
                     </td>
                 </tr>
                <?php $class = ($class == 'even') ? 'odd' : 'even' ; ?>
            <?php } ?>
        </tbody>
    </table>
</div>
<div style="clear:both;"></div>