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
    $sQuery = __("ie. PHP Programmer", 'osc_mobile');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php osc_current_web_theme_path('head.php') ; ?>
        <meta name="robots" content="index, follow" />
        <meta name="googlebot" content="index, follow" />
        <script type="text/javascript">
            var sQuery = '<?php echo $sQuery; ?>' ;
            $('div#search_form').live('pageshow', function(){

                if($('input[name=sPattern]').val() == sQuery) {
                    $('input[name=sPattern]').css('color', 'gray');
                }
                $('input[name=sPattern]').click(function(){
                    if($('input[name=sPattern]').val() == sQuery) {
                        $('input[name=sPattern]').val('');
                        $('input[name=sPattern]').css('color', '');
                    }
                });
                $('input[name=sPattern]').blur(function(){
                    if($('input[name=sPattern]').val() == '') {
                        $('input[name=sPattern]').val(sQuery);
                        $('input[name=sPattern]').css('color', 'gray');
                    }
                });
                $('input[name=sPattern]').keypress(function(){
                    $('input[name=sPattern]').css('background','');
                })
            });
            function doSearch() {
                if($('input[name=sPattern]').val() == sQuery){
                    return false;
                }
                if($('input[name=sPattern]').val().length < 3) {
                    $('input[name=sPattern]').css('background', '#FFC6C6');
                    return false;
                }
                return true;
            }
        </script> 
    </head>
    <body>
        <div data-theme="c" data-role="page" data-title="<?php echo osc_page_title() ; ?>">
            <div data-role="header">
                <h1><?php echo osc_page_title() ; ?></h1>
                <?php osc_show_flash_message() ; ?>
                <a data-icon="search" data-iconpos="notext" data-transition="pop" data-rel="dialog" href="#search_form"></a>
                <div data-theme="b" data-role="navbar" >
                    <ul>
                        <li>
                            <?php if( osc_is_web_user_logged_in() ) { ?>
                            <a href="<?php echo osc_user_logout_url() ; ?>"><?php _e('Logout', 'osc_mobile') ; ?></a>
                            <?php } else {?>
                            <a href="<?php echo osc_user_login_url(); ?>"><?php _e('Log in','osc_mobile')?></a>
                            <?php } ?>
                        </li>
                        <?php if( !osc_is_web_user_logged_in() ) { ?>
                        <li>
                            <?php if(osc_user_registration_enabled()) { ?>
                            <a href="<?php echo osc_register_account_url() ; ?>"><?php _e('Register for a free account', 'osc_mobile'); ?></a>
                            <?php }; ?>
                        </li>
                        <?php } ?>
                        <li>
                            <a data-icon="" href="<?php echo osc_item_post_url_in_category() ; ?>"><?php _e("Publish", 'osc_mobile');?></a>
                        </li>
                    </ul>
                </div>
                
            </div><!-- /header -->

            <div data-role="content" style="height: auto;">
                
                <?php if(osc_count_categories () > 0) { ?>
                    <?php while ( osc_has_categories() ) { ?>
                        <div data-role="collapsible" data-collapsed="true">
                            <?php $cat_title =  osc_category_name() ; ?>
                            <?php $cat_link = str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>
                            <h3 style="width: 100%;"><div style="display: block; width: 100%; float: right; height: 17px;"><span class="ui-li-count ui-btn-up-c ui-btn-corner-all left_count"><?php echo osc_category_total_items() ; ?></span></div><?php echo $cat_title ?></h3>
                            <ul data-role="listview" data-inset="true" data-theme="d">
                                <?php if ( osc_count_subcategories() > 0 ) { ?>
                                    <?php while ( osc_has_subcategories() ) { ?>
                                <li><a href="<?php echo str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>"><?php echo osc_category_name() ; ?><span class="ui-li-count"><?php echo osc_category_total_items() ; ?></span></a></li>
                                    <?php } ?>
                                <?php }  else {?>
                                        <li><a href="<?php echo $cat_link ; ?>"><?php echo $cat_title ; ?><span class="ui-li-count"><?php echo osc_category_total_items() ; ?></span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                <?php }?>               
            </div><!-- /content -->

            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
            
        </div>
        
        <div data-theme="c" data-role="dialog" id="search_form" data-title="<?php _e('Search','osc_mobile');?>">
            <div data-role="header">
                <h1><?php echo osc_page_title() ; ?></h1>
            </div>
            <div data-role="content">  
                <?php osc_current_web_theme_path('inc.search.php') ; ?>
            </div>
        </div>
    </body>
</html>
