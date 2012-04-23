<?php 
    $i_userId = osc_logged_user_id();
	if(Params::getParam('delete') != '' && osc_is_web_user_logged_in()){
		delete_item(Params::getParam('delete'), $i_userId);
	}

    $itemsPerPage = (Params::getParam('itemsPerPage') != '') ? Params::getParam('itemsPerPage') : 5;
    $iPage        = (Params::getParam('iPage') != '') ? Params::getParam('iPage') : 0;

    Search::newInstance()->addConditions(sprintf("%st_item_watchlist.fk_i_user_id = %d", DB_TABLE_PREFIX, $i_userId));
    Search::newInstance()->addConditions(sprintf("%st_item_watchlist.fk_i_item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
    Search::newInstance()->addTable(sprintf("%st_item_watchlist", DB_TABLE_PREFIX));
    Search::newInstance()->page($iPage, $itemsPerPage);

    $aItems      = Search::newInstance()->doSearch();
    $iTotalItems = Search::newInstance()->count();
    $iNumPages   = ceil($iTotalItems / $itemsPerPage) ;

    View::newInstance()->_exportVariableToView('items', $aItems);
    View::newInstance()->_exportVariableToView('search_total_pages', $iNumPages);
    View::newInstance()->_exportVariableToView('search_page', $iPage) ;

	// delete item from watchlist
	function delete_item($item, $uid){
		$conn = getConnection();
		$conn->osc_dbExec("DELETE FROM %st_item_watchlist WHERE fk_i_item_id = %d AND fk_i_user_id = %d LIMIT 1", DB_TABLE_PREFIX , $item, $uid);
	}
?>
<div class="content user_account">
    <h1>
        <strong><?php _e('Your watchlist', 'watchlist'); ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu(); ?>
    </div>
    <div id="main">
        <?php if (osc_count_items() == 0) { ?>
        <h3><?php _e('You don\'t have any items yet', 'watchlist'); ?></h3>
        <?php } else { ?>
        <h3><?php printf(_n('You are watching %d item', 'You are watching %d items', $iTotalItems, 'watchlist'), $iTotalItems) ; ?></h3>
        <div class="ad_list">
            <div id="list_head"></div>
            <table border="0" cellspacing="0">
                <tbody>
                    <?php $class = "even"; ?>
                    <?php while ( osc_has_items() ) { ?>
                    <tr class="<?php echo $class; ?>">
                        <?php if (osc_images_enabled_at_items()) { ?>
                            <td class="photo">
                            <?php if (osc_count_item_resources()) { ?>
                                <a href="<?php echo osc_item_url(); ?>"><img src="<?php echo osc_resource_thumbnail_url(); ?>" width="75px" height="56px" title="" alt="" /></a>
                            <?php } else { ?>
                                <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" title="" alt="" />
                            <?php } ?>
                            </td>
                        <?php } ?>
                        <td class="text">
                            <h3>
                                <a href="<?php echo osc_item_url(); ?>"><?php echo osc_item_title(); ?></a>
                            </h3>
                            <p>
                                <strong><?php if (osc_price_enabled_at_items()) { echo osc_item_formated_price(); ?> - <?php } echo osc_item_city(); ?> (<?php echo osc_item_region(); ?>) - <?php echo osc_format_date(osc_item_pub_date()); ?></strong>
                            </p>
                            <p><?php echo osc_highlight(strip_tags(osc_item_description())); ?></p>
                            <p align="right"><a class="delete" onclick="javascript:return confirm('<?php _e('This action can not be undone. Are you sure you want to continue?', 'watchlist'); ?>')" href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '?delete=' . osc_item_id(); ?>" ><?php _e('Delete', 'watchlist'); ?></a><p>
                        </td>
                    </tr>
                    <?php $class = ($class == 'even') ? 'odd' : 'even'; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="paginate">
            <?php echo osc_pagination(array('url' => osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '?iPage={PAGE}')); ?>
        </div>
        <?php } ?>
    </div>
</div>