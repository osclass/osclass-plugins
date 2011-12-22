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
                <h1><?php _e('Recover your password', 'osc_mobile') ; ?></h1>
                <?php osc_show_flash_message() ; ?>                
            </div><!-- /header -->

            <div data-role="content" style="height: auto;">
                <form action="<?php echo osc_base_url(true) ; ?>" method="post" >
                    <input type="hidden" name="page" value="login" />
                    <input type="hidden" name="action" value="forgot_post" />
                    <input type="hidden" name="userId" value="<?php echo Params::getParam('userId'); ?>" />
                    <input type="hidden" name="code" value="<?php echo Params::getParam('code'); ?>" />
                    <fieldset>
                        <p>
                            <label for="new_email"><?php _e('New pasword', 'osc_mobile') ; ?></label>
                            <input type="password" name="new_password" value="" /><br />
                        </p>
                        <p>
                            <label for="new_email"><?php _e('Repeat new pasword', 'osc_mobile') ; ?></label>
                            <input type="password" name="new_password2" value="" /><br />
                        </p>
                        <button type="submit"><?php _e('Change password', 'osc_mobile') ; ?></button>
                    </fieldset>
                </form>
            </div><!-- /content -->
            
            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
        </div>
        
        
    </body>
</html>
