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
            <div data-role="header" data-position="inline">
                <a data-icon="back" data-rel="back" data-iconpos="notext" href=""></a>
                <h1><?php echo osc_page_title() ; ?></h1>
                <a data-icon="home" data-inline="true" data-iconpos="notext" href="<?php echo osc_base_url(true); ?>"></a>
                <?php osc_show_flash_message() ; ?>
                <div data-role="navbar">
                    <ul>
                        <?php $i = 0 ; ?>
                        <?php $orders = osc_list_orders();
                        foreach($orders as $label => $params) {
                            $orderType = ($params['iOrderType'] == 'asc') ? '0' : '1'; ?>
                            <?php if(osc_search_order() == $params['sOrder'] && osc_search_order_type() == $orderType) { ?>
                        <li><a class="ui-btn-active" href="<?php echo osc_update_search_url($params) ; ?>"><?php echo $label; ?></a></li>
                            <?php } else { ?>
                        <li><a href="<?php echo osc_update_search_url($params) ; ?>"><?php echo $label; ?></a></li>
                            <?php } ?>

                            <?php $i++ ; ?>
                        <?php } ?>
                    </ul>
                </div><!-- /navbar -->
            </div><!-- /header -->

            <div data-role="content" style="padding-top: 0px;">
                
                <div class="ui-block">
                    <h1>
                    <?php
                        $id = osc_search_category();
                        if( count($id) == 1 ){
                            echo osc_category_name();
                            $category  = Category::newInstance()->findByPrimaryKey($id[0]);
                            echo $category['s_name'];
                        }
                    ?>
                    </h1>
                </div>
                
                <div class="ui-block" style="padding-top:15px;padding-bottom: 15px;">
                    <?php if(osc_count_items() == 0) { ?>
                        <p class="empty" ><?php printf(__('There are no results matching "%s"', 'osc_mobile'), osc_search_pattern()) ; ?></p>
                    <?php } else { ?>
                        <?php require(osc_search_show_as() == 'list' ? 'search_list.php' : 'search_gallery.php') ; ?>
                    <?php } ?>
                </div>
                <div class="paginate" >
                    <?php echo mbl_search_pagination(); ?>
                </div>
            </div><!-- /content -->

            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
        </div>
    </body>
</html>
