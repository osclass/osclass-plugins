<?php 
    $user_id = osc_logged_user_id();
	if(Params::getParam('delete')!=''){
		delete_item(Params::getParam('delete'),$user_id);
	}
    $itemsPerPage = (Params::getParam('itemsPerPage') != '') ? Params::getParam('itemsPerPage') : 5;
    $page         = (Params::getParam('iPage') != '') ? Params::getParam('iPage') : 0;
	$conn   = getConnection();
	$total_items  = $conn->osc_dbFetchValues("SELECT COUNT(*) FROM %st_item_watchlist WHERE fk_i_user_id = %d", DB_TABLE_PREFIX , $user_id);
	$total_pages  = ceil($total_items[0]/$itemsPerPage);
	$items = $conn->osc_dbFetchResults("SELECT * FROM %st_item LEFT JOIN %st_item_watchlist ON (pk_i_id = fk_i_item_id) WHERE %st_item_watchlist.fk_i_user_id = %d LIMIT %d,%d", DB_TABLE_PREFIX , DB_TABLE_PREFIX , DB_TABLE_PREFIX, $user_id,$page * $itemsPerPage,$itemsPerPage);
	$items = Item::newInstance()->extendData($items);
    

    View::newInstance()->_exportVariableToView('items', $items);
    View::newInstance()->_exportVariableToView('list_total_pages', $total_pages);
    View::newInstance()->_exportVariableToView('list_total_items', $total_items);
    View::newInstance()->_exportVariableToView('items_per_page', $itemsPerPage);
    View::newInstance()->_exportVariableToView('list_page', $page);

	//Delete item from watchlist
	function delete_item($item,$uid){
		$conn   = getConnection();
		$conn->osc_dbExec("DELETE FROM %st_item_watchlist WHERE fk_i_item_id = %d AND fk_i_user_id = %d LIMIT 1", DB_TABLE_PREFIX , $item, $uid);
	}
?>
            <div class="content user_account">
                <h1>
                    <strong><?php _e('Your watchlist', 'watchlist') ; ?></strong>
                </h1>
                <div id="sidebar">
                    <?php echo osc_private_user_menu() ; ?>
                </div>
                <div id="main">
                    <h2><?php _e('Here is your favorite items', 'watchlist'); ?></h2>
                    <?php if(osc_count_items() == 0) { ?>
                        <h3><?php _e('You don\'t have any items yet', 'watchlist'); ?></h3>
                    <?php } else { ?>

                    <div class="ad_list">
                        <div id="list_head"></div>
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
											 <a href="<?php echo osc_item_url() ; ?>"><?php echo osc_item_title() ; ?>t</a>
										 </h3>
										 <p>
											 <strong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; ?> - <?php } echo osc_item_city(); ?> (<?php echo osc_item_region(); ?>) - <?php echo osc_format_date(osc_item_pub_date()); ?></strong>
										 </p>
										 <p><?php echo osc_highlight( strip_tags( osc_item_description() ) ) ; ?></p>
										 <p align="right"><a class="delete" onclick="javascript:return confirm('<?php _e('This action can not be undone. Are you sure you want to continue?', 'watchlist'); ?>')" href="<?php echo  osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php').'?delete='.osc_item_id();?>" ><?php _e('Delete', 'watchlist'); ?></a><p>
									 </td>
								 </tr>
								<?php $class = ($class == 'even') ? 'odd' : 'even' ; ?>
							<?php } ?>
						</tbody>
					</table>
		        </div>
                        <br />
                        <div class="paginate">
                        <?php for($i = 0 ; $i < osc_list_total_pages() ; $i++) {
                            if($i == osc_list_page()) {
                                printf('<a class="searchPaginationSelected" href="%s">%d</a>', osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '?iPage=' . $i, ($i + 1));
                            } else {
                                printf('<a class="searchPaginationNonSelected" href="%s">%d</a>', osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '?iPage='. $i, ($i + 1));
                            }
                        } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>