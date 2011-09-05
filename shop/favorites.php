<?php

$conn = getConnection();

if(Params::getParam('add')!='') {
    if(osc_logged_user_id()!='') {
        $fav = $conn->osc_dbFetchResult("SELECT * FROM %st_shop_favs WHERE fk_i_seller_id = %d AND fk_i_user_id = %d", DB_TABLE_PREFIX, Params::getParam('add'), osc_logged_user_id());
        if(!isset($fav['fk_i_user_id'])) {
            $conn->osc_dbExec("INSERT INTO %st_shop_favs (`fk_i_user_id`, `fk_i_seller_id`) VALUES (%d, %d)", DB_TABLE_PREFIX, osc_logged_user_id(), Params::getParam('add'));
        }
    }
}


$favs = $conn->osc_dbFetchResults("SELECT * FROM %st_shop_favs f, %st_user u WHERE f.fk_i_user_id = %d AND u.pk_i_id = f.fk_i_seller_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX, osc_logged_user_id());
    /*foreach($favs as $k => $v) {
        $favs[$k]['items'] = Item::newInstance()->findByUserIDEnabled($v['fk_i_seller_id'], 0, 5);
    }*/



?>
<h1><?php echo _e('My favourites', 'shop'); ?></h1>
<div style="clear:both;"></div>
<div>
    <?php foreach($favs as $fav) {
        View::newInstance()->_exportVariableToView('items', Item::newInstance()->findByUserIDEnabled($fav['fk_i_seller_id'], 0, 5));
        View::newInstance()->_exportVariableToView('user', $fav);
    ?>
    <h2><?php _e("Seller", 'shop');?></h2>
    <?php echo osc_user_name(); ?> 
        <h3><?php _e('Items'); ?></h3>
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
        <?php }; ?>
</div>