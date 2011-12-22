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
        <meta name="robots" content="noindex, nofollow" />
        <meta name="googlebot" content="noindex, nofollow" />
    </head>
    <body>
        <div data-theme="c" data-role="page" data-title="<?php echo osc_page_title() ; ?>">
            <div data-role="header">
                <a data-icon="back" data-rel="back" data-iconpos="notext" href=""></a>
                <h1><?php echo osc_page_title() ; ?></h1>
                <a data-icon="home" data-inline="true" data-iconpos="notext" href="<?php echo osc_base_url(true); ?>"></a>
                <?php osc_show_flash_message() ; ?>
            </div><!-- /header -->
            
            <div data-role="content" style="padding-top: 0px;">
                <h1><?php echo osc_static_page_title() ; ?></h1>
                <div><?php echo osc_static_page_text() ; ?></div>
            </div>
            
            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
        </div>
    </body>
</html>