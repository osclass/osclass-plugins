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
        <div data-theme="c" data-role="page">
            <div data-role="header">
                <a data-rel="back" data-icon="back"  data-iconpos="notext"></a>
                <h1><?php _e('Register an account for free', 'osc_mobile') ; ?></h1>
                <?php osc_show_flash_message() ; ?>
            </div>

            <div data-role="content" data-theme="c">
                <form name="register" id="register" action="<?php echo osc_base_url(true) ; ?>" method="post" >
                    <input type="hidden" name="page" value="register" />
                    <input type="hidden" name="action" value="register_post" />

                        <div data-role="fieldcontain">
                            <label for="name" class="ui-input-text"><?php _e('Name', 'osc_mobile') ; ?></label> <?php UserForm::name_text(); ?><br />
                            <label for="password" class="ui-input-text"><?php _e('Password', 'osc_mobile') ; ?></label> <?php UserForm::password_text(); ?><br />
                            <label for="password" class="ui-input-text"><?php _e('Re-type password', 'osc_mobile') ; ?></label> <?php UserForm::check_password_text(); ?><br />
                            <p id="password-error" style="display:none;">
                                <?php _e('Passwords don\'t match', 'osc_mobile') ; ?>.
                            </p>
                            <label for="email" class="ui-input-text"><?php _e('E-mail', 'osc_mobile') ; ?></label> <?php UserForm::email_text() ; ?><br />
                            </div>
                            <?php osc_show_recaptcha('register'); ?>
                            <button type="submit"><?php _e('Create', 'osc_mobile') ; ?></button>
                            <?php osc_run_hook('user_register_form') ; ?>
                        </div>
                    </div>	
                </form>
            </div>
        </div>       
    </body>
</html>