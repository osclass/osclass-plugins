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
            <div data-role="header">
                <h1><?php _e('Log in','modern');?></h1>
            </div>

            <div data-role="content" data-theme="c">
                    <form action="<?php echo osc_base_url(true); ?>" method="post" class="ui-body ui-body-a ui-corner-all">
                        <input type="hidden" name="page" value="login"  data-role="none" />
                        <input type="hidden" name="action" value="login_post"  data-role="none" />
                        <div data-role="fieldcontain">
                            <label for="email"><?php _e('E-mail', 'modern'); ?></label>
                            <input type="text" name="email" id="email" value=""  />
                            <br/><br/>
                            <label for="password"><?php _e('Password', 'modern'); ?></label>
                            <input type="password" name="password" id="password" value="" />
                            <br/><br/>
                            <input type="checkbox" name="rememberMe" id="rememberMe" class="custom" />
                            <label for="rememberMe"><?php _e('Remember me', 'modern') ; ?></label>                            
                        </div>
                        <button style="width:99%" type="submit" name="submit" value="submit-value" data-icon="check" data-role="button" data-inline="false"><?php _e("Log in", 'modern');?></button>
                    </form>
                 <a href="<?php echo osc_register_account_url() ; ?>" data-role="button"><?php _e("Register for a free account", 'modern') ; ?></a>
                 <a href="<?php echo osc_recover_user_password_url() ; ?>" data-role="button"><?php _e("Forgot password?", 'modern') ; ?></a>
            </div>
        </div>       
    </body>
</html>
