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
$wSizeImage = 255;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
    <head>
            <?php osc_current_web_theme_path('head.php') ; ?>
            <?php if( osc_item_is_expired () ) { ?>
            <meta name="robots" content="noindex, nofollow" />
            <meta name="googlebot" content="noindex, nofollow" />
            <?php } else { ?>
            <meta name="robots" content="index, follow" />
            <meta name="googlebot" content="index, follow" />
            <?php } ?>
    </head>
    <body>
        <div data-role="page" id="itemPage">
            
            <script type="text/javascript">
                $( '#itemPage' ).live( 'pageinit',function(event){
                    var nResources = <?php echo count(ItemResource::newInstance()->getAllResources( osc_item_id() ) ); ?>;
                    $('#scroll-image').live('swipeleft',function(event, ui){
                        var position = $("#scrollable").position();
                        var left = position.left;
                        var max  = (nResources-1)*<?php echo $wSizeImage; ?>;
                        if( Math.abs(left) < Math.abs(max) ) {
                            $("#scrollable").animate({"left": "-=255px"}, "slow");
                        }
                    })
                    $('#scroll-image').live('swiperight',function(event, ui){
                        var position = $("#scrollable").position();
                        var left = position.left;
                        if( left < 0 ) {
                            $("#scrollable").animate({"left": "+=255px"}, "slow");
                        }
                    })
                });
            </script>
            
            <style>
            .scroll-container > .ui-scrollview-view
            {
                <?php $widthImg = osc_count_item_resources() * 255;?>
                <?php if(osc_count_item_resources() == 1) { ?>
                width: 100%; 
                <?php } else { ?>
                width: <?php echo $widthImg;?>px; 
                <?php } ?>
                background-color: white;
            }
            </style>
            
            <div data-role="header" >
                <a data-icon="back" data-inline="true" data-iconpos="notext" data-rel="back" href=""></a>
                <h1><?php echo osc_page_title(); ?></h1>
                <?php osc_show_flash_message() ; ?>
                <a data-icon="home" data-inline="true" data-iconpos="notext" href="<?php echo osc_base_url(true); ?>"></a>
                <div data-role="navbar">
                    <ul>
                        <li>
                            <a onclick="$.mobile.showPageLoadingMsg();" data-transition="pop" data-rel="dialog" data-ajax="false" href="<?php echo $_SERVER['REQUEST_URI']; ?>#markas"><strong><?php _e('Mark as', 'osc_mobile') ; ?></strong></a>
                        </li>
                        <?php if( !osc_item_is_expired () ) { ?>
                        <?php     if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
                        <li>
                            <a onclick="$.mobile.showPageLoadingMsg();" data-transition="pop" data-ajax="false" data-rel="dialog" href="<?php echo $_SERVER['REQUEST_URI']; ?>#contact"><?php _e('Contact seller', 'osc_mobile') ; ?></a>
                        </li>
                        <?php     } ?>
                        <?php } ?>
                        <?php if( osc_comments_enabled() ) { ?>
                        <?php   if( osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments() ) { ?>
                        <li>
                            <a onclick="$.mobile.showPageLoadingMsg();" data-transition="pop" data-ajax="false" data-rel="dialog" href="<?php echo $_SERVER['REQUEST_URI']; ?>#comment"><?php _e('Comment', 'osc_mobile') ; ?></a>
                        </li>
                        <?php     } ?>
                        <?php } ?>
                    </ul>
                </div><!-- /navbar -->
            </div><!-- /header -->
            
            <div data-role="content" style="padding-top:0px;" >
                <h1><?php echo osc_item_title(); ?> - <?php echo osc_item_category();?></h1>
                <div class="ui-block">
                    <?php if( osc_images_enabled_at_items() ) { ?>
                     
                        <?php if( osc_count_item_resources() > 0 ) { ?>
                        <div id="scroll-image" style="overflow: hidden;background: none repeat scroll 0 0 white;border: 1px solid #CCCCCC; height: auto; padding: 0; width: 100%;position: relative;">
                            <div id="scrollable" style="left: 0; overflow: hidden; position: relative; top: 0;width: 1200px;">
                                <?php for ( $i = 0; osc_has_item_resources() ; $i++ ) { ?>
                                <div class="square">
                                <?php if( $i == 0 ) { ?>
                                    <img src="<?php echo osc_resource_url(); ?>"  alt="" title=""/>
                                <?php } else { ?>
                                    <img src="<?php echo osc_resource_thumbnail_url(); ?>"  alt="" title=""/>
                                <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="ui-block">
                        <a onclick="$.mobile.showPageLoadingMsg();" data-role="button" data-transition="pop" data-ajax="false" data-rel="dialog" href="<?php echo $_SERVER['REQUEST_URI']; ?>#share"><?php _e('Share', 'osc_mobile'); ?></a>
                    </div>
                </div>
                <div class="ui-block" style="padding-top:15px;">
                    <ul data-role="listview">
                        <?php if ( osc_price_enabled_at_items() ) { ?><li><?php _e("Price", 'osc_mobile'); ?>: <strong><?php echo osc_item_formated_price() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_country() != "" ) { ?><li><?php _e("Country", 'osc_mobile'); ?>: <strong><?php echo osc_item_country() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_region() != "" ) { ?><li><?php _e("Region", 'osc_mobile'); ?>: <strong><?php echo osc_item_region() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_city() != "" ) { ?><li><?php _e("City", 'osc_mobile'); ?>: <strong><?php echo osc_item_city() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_city_area() != "" ) { ?><li><?php _e("City area", 'osc_mobile'); ?>: <strong><?php echo osc_item_city_area() ; ?></strong></li><?php } ?>
                        <?php if ( osc_item_address() != "" ) { ?><li><?php _e("Address", 'osc_mobile') ; ?>: <strong><?php echo osc_item_address() ; ?></strong></li><?php } ?>
                    </ul>  
                </div>
                
                <div>
                    <h3><?php ?></h3>
                    <p><?php echo osc_item_description() ; ?></p>
                    <?php osc_run_hook('item_detail', osc_item() ) ; ?>

                    <div id="type_dates">
                        <em class="publish"><?php if ( osc_item_pub_date() != '' ) echo __('Published date', 'osc_mobile') . ': ' . osc_format_date( osc_item_pub_date() ) ; ?></em>
                        <em class="update"><?php if ( osc_item_mod_date() != '' ) echo __('Modified date', 'osc_mobile') . ': ' . osc_format_date( osc_item_mod_date() ) ; ?></em>
                    </div>

                </div>
                <hr/>
                <?php if( osc_comments_enabled() ) { ?>
                    <?php if( osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments() ) { ?>
                    <div id="comments">
                        <h3><?php _e('Comments', 'osc_mobile'); ?></h3>
                        <ul id="comment_error_list"></ul>
                        <?php if( osc_count_item_comments() >= 1 ) { ?>
                            <div class="comments_list">
                                <?php while ( osc_has_item_comments() ) { ?>
                                    <div class="comment">
                                        <h4><strong><?php echo osc_comment_title() ; ?></strong> <em><?php _e("by", 'osc_mobile') ; ?> <?php echo osc_comment_author_name() ; ?>:</em></h4>
                                        <p><?php echo osc_comment_body() ; ?> </p>
                                        <?php if ( osc_comment_user_id() && (osc_comment_user_id() == osc_logged_user_id()) ) { ?>
                                        <p>
                                            <a rel="nofollow" href="<?php echo osc_delete_comment_url(); ?>" title="<?php _e('Delete your comment', 'osc_mobile'); ?>"><?php _e('Delete', 'osc_mobile'); ?></a>
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

            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
            </div><!-- /footer -->
        </div>
        
        <!-- Start of MARKAS page -->
        <div data-role="dialog" data-theme="a" id="markas">
            <div data-theme="a" data-role="header">
                <h1><?php _e("Mark as", 'osc_mobile') ; ?></h1>
            </div><!-- /header -->

            <div data-theme="a" data-role="content">
                <div data-theme="a" data-role="controlgroup">
                    <a href="<?php echo osc_item_link_spam() ; ?>" data-role="button"><?php _e('spam', 'osc_mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_bad_category() ; ?>" data-role="button"><?php _e('misclassified', 'osc_mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_repeated() ; ?>" data-role="button"><?php _e('duplicated', 'osc_mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_expired() ; ?>" data-role="button"><?php _e('expired', 'osc_mobile') ; ?></a>
                    <a href="<?php echo osc_item_link_offensive() ; ?>" data-role="button"><?php _e('offensive', 'osc_mobile') ; ?></a>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->

        <!-- Start of CONTACT page -->
        <div data-role="dialog" data-theme="a" id="contact">
            <div data-theme="a" data-role="header">
                <h1><?php _e('Contact us', 'osc_mobile') ; ?></h1>
            </div><!-- /header -->

            <div data-theme="a" data-role="content">
                <div>
                    <form action="<?php echo osc_base_url(true); ?>" method="post" name="contact_form" id="contact_form" >
                        <?php ContactForm::primary_input_hidden() ; ?>
                        <?php ContactForm::action_hidden() ; ?>
                        <?php ContactForm::page_hidden() ; ?>
                        <fieldset>
                            <p><?php _e('To (seller)', 'osc_mobile'); ?>: <?php echo osc_item_contact_name() ;?></p>
                            <p><?php _e('Item', 'osc_mobile'); ?>: <a href="<?php echo osc_item_url(); ?>"><?php echo osc_item_title() ; ?></a></p>
                            <?php if(osc_is_web_user_logged_in()) { ?>
                                <input type="hidden" name="yourName" value="<?php echo osc_logged_user_name(); ?>" />
                                <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
                            <?php } else { ?>
                                <label for="yourName"><?php _e('Your name', 'osc_mobile'); ?></label> <?php ContactForm::your_name(); ?><br/>
                                <label for="yourEmail"><?php _e('Your e-mail address', 'osc_mobile'); ?></label> <?php ContactForm::your_email(); ?><br />
                            <?php }; ?>
                            <label for="phoneNumber"><?php _e('Phone number', 'osc_mobile'); ?> (<?php _e('optional', 'osc_mobile'); ?>)</label> <?php ContactForm::your_phone_number(); ?><br/>
                            <label for="message"><?php _e('Message', 'osc_mobile'); ?></label> <?php ContactForm::your_message(); ?><br />
                            <?php osc_show_recaptcha(); ?>
                            <button type="submit"><?php _e('Send message', 'osc_mobile'); ?></button>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
        <!-- Start of COMMENT page -->
        <div data-role="dialog" data-theme="a" id="comment">
            <div data-theme="a" data-role="header">
                <h1><?php _e('Leave your comment', 'osc_mobile') ; ?></h1>
            </div><!-- /header -->

            <div data-theme="a" data-role="content">
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
                                <label for="authorName"><?php _e('Your name', 'osc_mobile') ; ?>:</label> <?php CommentForm::author_input_text(); ?><br />
                                <label for="authorEmail"><?php _e('Your e-mail', 'osc_mobile') ; ?>:</label> <?php CommentForm::email_input_text(); ?><br />
                            <?php }; ?>
                            <label for="title"><?php _e('Title', 'osc_mobile') ; ?>:</label><?php CommentForm::title_input_text(); ?><br />
                            <label for="body"><?php _e('Comment', 'osc_mobile') ; ?>:</label><?php CommentForm::body_input_textarea(); ?><br />
                            <button type="submit"><?php _e('Send', 'osc_mobile') ; ?></button>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
        
        <!-- Start of SHARE page -->
        <div data-role="dialog" data-theme="a" id="share">
            <div data-theme="a" data-role="header">
                <h1><?php _e('Send to a friend', 'osc_mobile'); ?></h1>
            </div><!-- /header -->

            <div data-theme="a" data-role="content">
                <div>
                    <form id="sendfriend" name="sendfriend" action="<?php echo osc_base_url(true); ?>" method="post">
                        <fieldset>
                            <input type="hidden" name="action" value="send_friend_post" />
                            <input type="hidden" name="page" value="item" />
                            <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />
                            <label><?php _e('Item', 'osc_mobile'); ?>: <a href="<?php echo osc_item_url( ); ?>"><?php echo osc_item_title(); ?></a></label><br/>
                            <?php if(osc_is_web_user_logged_in()) { ?>
                                <input type="hidden" name="yourName" value="<?php echo osc_logged_user_name(); ?>" />
                                <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
                            <?php } else { ?>
                                <label for="yourName"><?php _e('Your name', 'osc_mobile'); ?></label> <?php SendFriendForm::your_name(); ?> <br/>
                                <label for="yourEmail"><?php _e('Your e-mail address', 'osc_mobile'); ?></label> <?php SendFriendForm::your_email(); ?> <br/>
                            <?php }; ?>
                            <label for="friendName"><?php _e("Your friend's name", 'osc_mobile'); ?></label> <?php SendFriendForm::friend_name(); ?> <br/>
                            <label for="friendEmail"><?php _e("Your friend's e-mail address", 'osc_mobile'); ?></label> <?php SendFriendForm::friend_email(); ?> <br/>
                            <label for="message"><?php _e('Message', 'osc_mobile'); ?></label> <?php SendFriendForm::your_message(); ?> <br/>
                            <?php osc_show_recaptcha(); ?>
                            <br/>
                            <button type="submit"><?php _e('Send', 'osc_mobile'); ?></button>
                        </fieldset>
                    </form>
                </div>
            </div><!-- /content -->
        </div><!-- /page -->
        
    </body>
</html>
