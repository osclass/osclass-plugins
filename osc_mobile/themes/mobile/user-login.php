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
    </head>
    <body>
        <div data-role="page">
            <div data-role="header" data-theme="">
                <a data-icon="back" data-inline="true" data-iconpos="notext" data-rel="back" href=""></a>
                <h1><?php _e('Log in','mobile');?></h1>
                <?php osc_show_flash_message() ; ?>
            </div>

            <div data-role="content" data-theme="">
                <form action="<?php echo osc_base_url(true); ?>" method="post">
                    <input type="hidden" name="page" value="login"  data-role="none" />
                    <input type="hidden" name="action" value="login_post"  data-role="none" />
                    <fieldset data-role="fieldcontain">
                        <label for="email"><?php _e('E-mail', 'mobile'); ?></label>
                        <input type="text" name="email" id="email" value=""  />
                        <label for="password"><?php _e('Password', 'mobile'); ?></label>
                        <input type="password" name="password" id="password" value="" />
                        <input type="checkbox" name="rememberMe" id="rememberMe" class="custom" />
                        <label for="rememberMe"><?php _e('Remember me', 'mobile') ; ?></label>                            
                        <button style="width:99%" type="submit" name="submit" value="submit-value" data-icon="check" data-role="button" data-inline="false"><?php _e("Log in", 'mobile');?></button>
                    </fieldset>
                </form>
                <a href="<?php echo osc_recover_user_password_url() ; ?>" data-role="button"><?php _e("Forgot password?", 'mobile') ; ?></a>
            </div>
        </div>       
    </body>
</html>
