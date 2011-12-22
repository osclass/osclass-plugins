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
        <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js') ; ?>"></script>
    </head>
    <body>
        <div data-theme="c" data-role="page" data-title="<?php echo osc_page_title() ; ?>">
            <div data-role="header">
                <h1><?php _e('Contact us', 'osc_mobile') ; ?></h1>
                <?php osc_show_flash_message() ; ?>
                <a data-icon="back" data-iconpos="notext" data-transition="pop" data-rel="back" href="#"></a>
            </div><!-- /header -->

            <div data-role="content" style="height: auto;">
                <div>
                    <form action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact" id="contact-form">
                        <input type="hidden" name="page" value="contact" />
                        <input type="hidden" name="action" value="contact_post" />
                        <fieldset>
                            <label for="subject"><?php _e('Subject', 'osc_mobile') ; ?> (<?php _e('optional', 'osc_mobile'); ?>)</label> <?php ContactForm::the_subject() ; ?><br />
                            <label for="message"><?php _e('Message', 'osc_mobile') ; ?></label> <?php ContactForm::your_message() ; ?><br />
                            <label for="yourName"><?php _e('Your name', 'osc_mobile') ; ?> (<?php _e('optional', 'osc_mobile'); ?>)</label> <?php ContactForm::your_name() ; ?><br />
                            <label for="yourEmail"><?php _e('Your e-mail address', 'osc_mobile') ; ?></label> <?php ContactForm::your_email(); ?><br />
                            <?php osc_show_recaptcha(); ?>
                            <button type="submit"><?php _e('Send', 'osc_mobile') ; ?></button>
                            <?php osc_run_hook('user_register_form') ; ?>
                        </fieldset>
                    </form>
                </div>
            </div>
            
            <div data-role="footer" class="footer-docs" data-theme="a" style="font-size: 12px; text-align: center;">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
        </div>
    </body>
</html>
