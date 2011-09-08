<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
        <?php osc_current_web_theme_path('head.php') ; ?>
        <?php if(osc_count_items() == 0) { ?>
            <meta name="robots" content="noindex, nofollow" />
            <meta name="googlebot" content="noindex, nofollow" />
        <?php } else { ?>
            <meta name="robots" content="index, follow" />
            <meta name="googlebot" content="index, follow" />
        <?php } ?>
    </head>
    <body>
        <div data-role="page">
            <div data-role="header">
                <?php osc_current_web_theme_path('header.php') ; ?>
                <h1><a href="<?php echo osc_base_url(true); ?>"><?php echo osc_item_title() ; ?></a></h1>
            </div><!-- /header -->

            <div data-role="content" class="content" style="padding-top:0px;">
                <p><strong><?php echo osc_item_category() ; ?></strong></p>
                <a data-role="button" href="<?php echo osc_base_url(true)."?page=item&action=markas&id=".osc_item_id();?>"><strong><?php _e('Mark as', 'modern') ; ?></strong></a>

                <div class="ui-grid-a">
                    <?php if( osc_images_enabled_at_items() ) { ?>
                        <?php if( osc_count_item_resources() > 0 ) { ?>
                        <div id="photos" class="ui-block-a">
                            <?php for ( $i = 0; osc_has_item_resources() ; $i++ ) { ?>
                            <a href="<?php echo osc_resource_url(); ?>" rel="image_group">
                                <?php if( $i == 0 ) { ?>
                                    <img style="width: 90%;" src="<?php echo osc_resource_url(); ?>"  alt="" title=""/>
                                <?php } else { ?>
                                    <img style="width: 90%;" src="<?php echo osc_resource_thumbnail_url(); ?>"  alt="" title=""/>
                                <?php } ?>
                            </a>
                            <?php } ?>
                        </div>
                        <?php } else { ?>
                        <div id="photos" class="ui-block-b">
                            <img style="width: 90%; padding-left:4px;float:left;padding-right: 10px;"  src="<?php echo osc_current_web_theme_url('images/no_photo.gif') ; ?>" title="" alt="" />
                        </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="ui-block-b">
                        <p><strong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; }?> </strong></p>
                        <p><?php echo osc_item_city();?></p>
                    </div>
                </div>
                <div>
                    <ul id="item_location">
                        <?php if ( osc_item_country() != "" ) { ?><li><?php _e("Country", 'modern'); ?>: <strong><?php echo osc_item_country() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_region() != "" ) { ?><li><?php _e("Region", 'modern'); ?>: <strong><?php echo osc_item_region() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_city() != "" ) { ?><li><?php _e("City", 'modern'); ?>: <strong><?php echo osc_item_city() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_city_area() != "" ) { ?><li><?php _e("City area", 'modern'); ?>: <strong><?php echo osc_item_city_area() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_address() != "" ) { ?><li><?php _e("Address", 'modern') ; ?>: <strong><?php echo osc_item_address() ; ?></strong></li><?php } ?>
                    </ul>

                    <p><?php echo osc_item_description() ; ?></p>
                    <?php osc_run_hook('item_detail', osc_item() ) ; ?>

                    <div id="type_dates">
                        <em class="publish"><?php if ( osc_item_pub_date() != '' ) echo __('Published date', 'modern') . ': ' . osc_format_date( osc_item_pub_date() ) ; ?></em>
                        <em class="update"><?php if ( osc_item_mod_date() != '' ) echo __('Modified date', 'modern') . ': ' . osc_format_date( osc_item_mod_date() ) ; ?></em>
                    </div>


                    <?php if( !osc_item_is_expired () ) { ?>
                        <?php     if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
                        <strong><a href="#contact"><?php _e('Contact seller', 'modern') ; ?></a></strong>
                        <?php     } ?>
                    <?php } ?>

                </div>

                <?php if( osc_comments_enabled() ) { ?>
                    <?php if( osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments() ) { ?>
                    <div id="comments">
                        <h2><?php _e('Comments', 'modern'); ?></h2>
                        <ul id="comment_error_list"></ul>
                        <?php if( osc_count_item_comments() >= 1 ) { ?>
                            <div class="comments_list">
                                <?php while ( osc_has_item_comments() ) { ?>
                                    <div class="comment">
                                        <h3><strong><?php echo osc_comment_title() ; ?></strong> <em><?php _e("by", 'modern') ; ?> <?php echo osc_comment_author_name() ; ?>:</em></h3>
                                        <p><?php echo osc_comment_body() ; ?> </p>
                                        <?php if ( osc_comment_user_id() && (osc_comment_user_id() == osc_logged_user_id()) ) { ?>
                                        <p>
                                            <a rel="nofollow" href="<?php echo osc_delete_comment_url(); ?>" title="<?php _e('Delete your comment', 'modern'); ?>"><?php _e('Delete', 'modern'); ?></a>
                                        </p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <div class="pagination">
                                    <?php echo osc_comments_pagination(); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                <?php } ?>
               
            </div>

            <div data-role="footer">
                <?php osc_current_web_theme_path('footer.php') ; ?>
            </div><!-- /footer -->
        </div>

        
        
    </body>
</html>
