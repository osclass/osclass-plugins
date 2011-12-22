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
        <meta name="robots" content="noindex, nofollow" />
        <meta name="googlebot" content="noindex, nofollow" />
    </head>
    <body>
        <div data-role="page">
            <div data-role="header">
                <a data-icon="back" data-inline="true" data-iconpos="notext" data-rel="back" href=""></a>
                <h1><?php _e('Recover your password', 'osc_mobile') ; ?></h1>
                <?php osc_show_flash_message() ; ?>
            </div>

            <div data-role="content" data-theme="c">
                <form action="<?php echo osc_base_url(true) ; ?>" method="post" >
                    <input type="hidden" name="page" value="login" />
                    <input type="hidden" name="action" value="recover_post" />
                    <fieldset data-role="fieldcontain">
                        <label for="email"><?php _e('E-mail', 'osc_mobile') ; ?></label> <?php UserForm::email_text() ; ?><br />
                        <?php osc_show_recaptcha('recover_password'); ?>
                        <button type="submit"><?php _e('Send me a new password', 'osc_mobile') ; ?></button>
                    </fieldset>
                </form>
            </div>
        </div>       
    </body>
</html>