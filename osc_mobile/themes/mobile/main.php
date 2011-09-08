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
<!DOCTYPE html>
<html>
    <head>
        <?php osc_current_web_theme_path('head.php') ; ?>
        <meta name="robots" content="index, follow" />
        <meta name="googlebot" content="index, follow" />
    </head>
    <body>
        <div data-role="page" data-title="<?php echo osc_page_title() ; ?>">

            <div data-role="header">
                <?php osc_current_web_theme_path('header.php') ; ?>
                <h1><?php echo osc_page_title() ; ?></h1>
            </div><!-- /header -->

            <div data-role="content">
                <?php if(osc_count_categories () > 0) { ?>
                    <?php while ( osc_has_categories() ) { ?>
                    <div data-role="collapsible" data-collapsed="true">
                        <?php if ( osc_count_subcategories() > 0 ) { ?>
                        <h3><?php echo osc_category_name() ; ?></h3>
                        <ul data-role="listview" data-inset="true" data-theme="d">
                            <?php while ( osc_has_subcategories() ) { ?>
                            <li><a href="<?php echo str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>"><?php echo osc_category_name() ; ?></a></li>
                            <?php } ?>
                        </ul>
                        <?php } else { ?>
                        <h3><a href="<?php echo str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>"><?php echo osc_category_name() ; ?></a></h3>
                        <?php } ?>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div><!-- /content -->

            <div data-role="footer">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
            
        </div>
    </body>
</html>
