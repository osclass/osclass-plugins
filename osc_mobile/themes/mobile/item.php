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
        <div data-role="page" id="main">
            <div data-role="header">
                <?php osc_current_web_theme_path('header.php') ; ?>
                <a data-icon="back" data-role="button" data-inline="true" data-iconpos="notext" data-back="true" href=""></a>
                
                <h1><a href="<?php echo osc_base_url(true); ?>"><?php echo osc_item_title() ; ?></a></h1>
                <div data-role="navbar">
                    <ul>
                        <li>
                            <a data-transition="pop" data-rel="dialog" href="#markas"><strong><?php _e('Mark as', 'mobile') ; ?></strong></a>
                        </li>
                        <?php if( !osc_item_is_expired () ) { ?>
                        <?php     if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
                        <li>
                            <a data-transition="pop" data-rel="dialog" href="#contact"><?php _e('Contact seller', 'mobile') ; ?></a>
                        </li>
                        <?php     } ?>
                        <?php } ?>
                        <?php if( osc_comments_enabled() ) { ?>
                        <?php   if( osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments() ) { ?>
                        <li>
                            <a data-transition="pop" data-rel="dialog" href="#comment"><?php _e('Comment', 'mobile') ; ?></a>
                        </li>
                        <?php     } ?>
                        <?php } ?>
                    </ul>
                </div><!-- /navbar -->
                <?php osc_show_flash_message() ; ?>
            </div><!-- /header -->

            <div data-role="content" class="content" style="padding-top:0px;">
                <p><strong><?php mbl_breadcrumbs(); ?></strong></p>
                
                

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
                        <a data-role="button" data-transition="pop" data-rel="dialog" href="#share"><?php _e('Share'); ?></a>
                    </div>
                </div>
                <br/><br/>
                <ul data-role="listview">
                    <?php if ( osc_item_country() != "" ) { ?><li><?php _e("Country", 'mobile'); ?>: <strong><?php echo osc_item_country() ; ?></strong></li><?php } ?>
                    <?php if ( osc_item_region() != "" ) { ?><li><?php _e("Region", 'mobile'); ?>: <strong><?php echo osc_item_region() ; ?></strong></li><?php } ?>
                    <?php if ( osc_item_city() != "" ) { ?><li><?php _e("City", 'mobile'); ?>: <strong><?php echo osc_item_city() ; ?></strong></li><?php } ?>
                    <?php if ( osc_item_city_area() != "" ) { ?><li><?php _e("City area", 'mobile'); ?>: <strong><?php echo osc_item_city_area() ; ?></strong></li><?php } ?>
                    <?php if ( osc_item_address() != "" ) { ?><li><?php _e("Address", 'mobile') ; ?>: <strong><?php echo osc_item_address() ; ?></strong></li><?php } ?>
                    
                </ul>  
                <br/>
                <div>
                    <h3><?php ?></h3>
                    <p><?php echo osc_item_description() ; ?></p>
                    <?php osc_run_hook('item_detail', osc_item() ) ; ?>

                    <div id="type_dates">
                        <em class="publish"><?php if ( osc_item_pub_date() != '' ) echo __('Published date', 'mobile') . ': ' . osc_format_date( osc_item_pub_date() ) ; ?></em>
                        <em class="update"><?php if ( osc_item_mod_date() != '' ) echo __('Modified date', 'mobile') . ': ' . osc_format_date( osc_item_mod_date() ) ; ?></em>
                    </div>

                </div>
                <hr/>
                <?php if( osc_comments_enabled() ) { ?>
                    <?php if( osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments() ) { ?>
                    <div id="comments">
                        <h3><?php _e('Comments', 'mobile'); ?></h3>
                        <ul id="comment_error_list"></ul>
                        <?php if( osc_count_item_comments() >= 1 ) { ?>
                            <div class="comments_list">
                                <?php while ( osc_has_item_comments() ) { ?>
                                    <div class="comment">
                                        <h4><strong><?php echo osc_comment_title() ; ?></strong> <em><?php _e("by", 'mobile') ; ?> <?php echo osc_comment_author_name() ; ?>:</em></h4>
                                        <p><?php echo osc_comment_body() ; ?> </p>
                                        <?php if ( osc_comment_user_id() && (osc_comment_user_id() == osc_logged_user_id()) ) { ?>
                                        <p>
                                            <a rel="nofollow" href="<?php echo osc_delete_comment_url(); ?>" title="<?php _e('Delete your comment', 'mobile'); ?>"><?php _e('Delete', 'mobile'); ?></a>
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
        
        <!-- Start of MARKAS page -->
        <div data-role="page" data-theme="b" id="markas">
            <div data-theme="b" data-role="header">
                <h1><?php _e("Mark as", 'mobile') ; ?></h1>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <div data-theme="b" data-role="controlgroup">
                    <a href="<?php echo osc_item_link_spam() ; ?>" data-role="button"><?php _e('spam', 'mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_bad_category() ; ?>" data-role="button"><?php _e('misclassified', 'mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_repeated() ; ?>" data-role="button"><?php _e('duplicated', 'mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_expired() ; ?>" data-role="button"><?php _e('expired', 'mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_offensive() ; ?>" data-role="button"><?php _e('offensive', 'mobile') ; ?></a>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->

        <!-- Start of CONTACT page -->
        <div data-role="page" data-theme="b" id="contact">
            <div data-theme="b" data-role="header">
                <h1><?php _e('Contact us', 'mobile') ; ?></h1>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <div>
                    <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact" id="contact-form">
                        <input type="hidden" name="page" value="contact" />
                        <input type="hidden" name="action" value="contact_post" />
                        <fieldset>
                            <label for="subject"><?php _e('Subject', 'mobile') ; ?> (<?php _e('optional', 'mobile'); ?>)</label> <?php ContactForm::the_subject() ; ?><br />
                            <label for="message"><?php _e('Message', 'mobile') ; ?></label> <?php ContactForm::your_message() ; ?><br />
                            <label for="yourName"><?php _e('Your name', 'mobile') ; ?> (<?php _e('optional', 'mobile'); ?>)</label> <?php ContactForm::your_name() ; ?><br />
                            <label for="yourEmail"><?php _e('Your e-mail address', 'mobile') ; ?></label> <?php ContactForm::your_email(); ?><br />
                            <?php osc_show_recaptcha(); ?>
                            <button type="submit"><?php _e('Send', 'mobile') ; ?></button>
                            <?php osc_run_hook('user_register_form') ; ?>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
        <!-- Start of COMMENT page -->
        <div data-role="page" data-theme="b" id="comment">
            <div data-theme="b" data-role="header">
                <h1><?php _e('Leave your comment', 'mobile') ; ?></h1>
                <?php _e('spam and offensive messages will be removed)','mobile'); ?>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <div>
                    <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="comment_form" id="comment-form">
                        <fieldset>
                            <input type="hidden" name="action" value="add_comment" />
                            <input type="hidden" name="page" value="item" />
                            <input type="hidden" name="id" value="<?php echo osc_item_id() ; ?>" />
                            <?php if(osc_is_web_user_logged_in()) { ?>
                                <input type="hidden" name="authorName" value="<?php echo osc_logged_user_name(); ?>" />
                                <input type="hidden" name="authorEmail" value="<?php echo osc_logged_user_email();?>" />
                            <?php } else { ?>
                                <label for="authorName"><?php _e('Your name', 'mobile') ; ?>:</label> <?php CommentForm::author_input_text(); ?><br />
                                <label for="authorEmail"><?php _e('Your e-mail', 'mobile') ; ?>:</label> <?php CommentForm::email_input_text(); ?><br />
                            <?php }; ?>
                            <label for="title"><?php _e('Title', 'mobile') ; ?>:</label><?php CommentForm::title_input_text(); ?><br />
                            <label for="body"><?php _e('Comment', 'mobile') ; ?>:</label><?php CommentForm::body_input_textarea(); ?><br />
                            <button type="submit"><?php _e('Send', 'mobile') ; ?></button>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
        
        <!-- Start of SHARE page -->
        <div data-role="page" data-theme="b" id="share">
            <div data-theme="b" data-role="header">
                <h1><?php _e('Send to a friend', 'mobile'); ?></h1>
            </div><!-- /header -->

            <div data-theme="b" data-role="content">
                <div>
                    <form id="sendfriend" name="sendfriend" action="<?php echo osc_base_url(true); ?>" method="post">
                        <fieldset>
                            <input type="hidden" name="action" value="send_friend_post" />
                            <input type="hidden" name="page" value="item" />
                            <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />
                            <label><?php _e('Item', 'modern'); ?>: <a href="<?php echo osc_item_url( ); ?>"><?php echo osc_item_title(); ?></a></label><br/>
                            <?php if(osc_is_web_user_logged_in()) { ?>
                                <input type="hidden" name="yourName" value="<?php echo osc_logged_user_name(); ?>" />
                                <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
                            <?php } else { ?>
                                <label for="yourName"><?php _e('Your name', 'mobile'); ?></label> <?php SendFriendForm::your_name(); ?> <br/>
                                <label for="yourEmail"><?php _e('Your e-mail address', 'mobile'); ?></label> <?php SendFriendForm::your_email(); ?> <br/>
                            <?php }; ?>
                            <label for="friendName"><?php _e("Your friend's name", 'mobile'); ?></label> <?php SendFriendForm::friend_name(); ?> <br/>
                            <label for="friendEmail"><?php _e("Your friend's e-mail address", 'mobile'); ?></label> <?php SendFriendForm::friend_email(); ?> <br/>
                            <label for="message"><?php _e('Message', 'mobile'); ?></label> <?php SendFriendForm::your_message(); ?> <br/>
                            <?php osc_show_recaptcha(); ?>
                            <br/>
                            <button type="submit"><?php _e('Send', 'mobile'); ?></button>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
    </body>
</html>
