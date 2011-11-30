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

    if(Params::getParam('plugin_action')=='done') {
        osc_set_preference('default_premium_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_premium_cost") : '1.0', 'paypal', 'STRING');
        osc_set_preference('allow_premium', Params::getParam("allow_premium") ? Params::getParam("allow_premium") : '0', 'paypal', 'BOOLEAN');
        osc_set_preference('default_publish_cost', Params::getParam("default_premium_cost") ? Params::getParam("default_publish_cost") : '1.0', 'paypal', 'STRING');
        osc_set_preference('pay_per_post', Params::getParam("pay_per_post") ? Params::getParam("pay_per_post") : '0', 'paypal', 'BOOLEAN');
        osc_set_preference('premium_days', Params::getParam("premium_days") ? Params::getParam("premium_days") : '7', 'paypal', 'INTEGER');
        osc_set_preference('currency', Params::getParam("currency") ? Params::getParam("currency") : 'USD', 'paypal', 'STRING');
        osc_set_preference('api_username', paypal_crypt(Params::getParam("api_username")), 'paypal', 'STRING');
        osc_set_preference('api_password', paypal_crypt(Params::getParam("api_password")), 'paypal', 'STRING');
        osc_set_preference('api_signature', paypal_crypt(Params::getParam("api_signature")), 'paypal', 'STRING');
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'paypal') . '.</p></div>' ;
        osc_set_preference('pack_price_1', Params::getParam("pack_price_1"), 'paypal', 'STRING');
        osc_set_preference('pack_price_2', Params::getParam("pack_price_2"), 'paypal', 'STRING');
        osc_set_preference('pack_price_3', Params::getParam("pack_price_3"), 'paypal', 'STRING');
        osc_set_preference('email', Params::getParam("email"), 'paypal', 'STRING');
        //osc_set_preference('pdt', Params::getParam("pdt"), 'paypal', 'STRING');
        osc_set_preference('standard', Params::getParam("standard_payment") ? Params::getParam("standard_payment") : '0', 'paypal', 'BOOLEAN');
        osc_set_preference('sandbox', Params::getParam("sandbox") ? Params::getParam("sandbox") : '0', 'paypal', 'BOOLEAN');
        osc_reset_preferences();
    }
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Paypal Options', 'paypal'); ?></legend>
                <form name="paypal_form" id="paypal_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />
                    <div style="float: left; width: 50%;">
                        <label><?php _e('API username', 'paypal'); ?></label><input type="text" name="api_username" id="api_username" value="<?php echo paypal_decrypt(osc_get_preference('api_username', 'paypal')); ?>" />
                        <br/>
                        <label><?php _e('API password', 'paypal'); ?></label><input type="password" name="api_password" id="api_password" value="<?php echo paypal_decrypt(osc_get_preference('api_password', 'paypal')); ?>" />
                        <br/>
                        <label><?php _e('API signature', 'paypal'); ?></label><input type="text" name="api_signature" id="api_signature" value="<?php echo paypal_decrypt(osc_get_preference('api_signature', 'paypal')); ?>" />
                        <br/>
                        <label><?php _e('Paypal email', 'paypal'); ?></label><input type="text" name="email" id="email" value="<?php echo osc_get_preference('email', 'paypal'); ?>" />
                        <br/>
                        <?php /*<label><?php _e('PDT', 'paypal'); ?></label><input type="text" name="pdt" id="pdt" value="<?php echo osc_get_preference('pdt', 'paypal'); ?>" />
                        <br/> */ ?>
                        <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('standard', 'paypal') ? 'checked="true"' : ''); ?> name="standard_payment" id="standard_payment" value="1" />
                        <label for="standard_payment"><?php _e('Use standard payment', 'paypal'); ?></label>
                        <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('sandbox', 'paypal') ? 'checked="true"' : ''); ?> name="sandbox" id="sandbox" value="1" />
                        <label for="sandbox"><?php _e('Sandbox environment', 'paypal'); ?></label>
                        <br/>
                    </div>
                    <div style="float: left; width: 50%;">
                        <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_premium', 'paypal') ? 'checked="true"' : ''); ?> name="allow_premium" id="allow_premium" value="1" />
                        <label for="allow_premium"><?php _e('Allow premium ads', 'paypal'); ?></label>
                        <br/>
                        <label><?php _e('Default premium cost', 'paypal'); ?></label><input type="text" name="default_premium_cost" id="default_premium_cost" value="<?php echo osc_get_preference('default_premium_cost', 'paypal'); ?>" />
                        <br/>
                        <label><?php _e('Premium days', 'paypal'); ?></label><input type="text" name="premium_days" id="premium_days" value="<?php echo osc_get_preference('premium_days', 'paypal'); ?>" />
                        <br/>
                        <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('pay_per_post', 'paypal') ? 'checked="true"' : ''); ?> name="pay_per_post" id="pay_per_post" value="1" />
                        <label for="pay_per_post"><?php _e('Pay per post ads', 'paypal'); ?></label>
                        <br/>
                        <label><?php _e('Default publish cost', 'paypal'); ?></label><input type="text" name="default_publish_cost" id="default_publish_cost" value="<?php echo osc_get_preference('default_publish_cost', 'paypal'); ?>" />
                        <br/>
                        <label><?php _e('Currency (3-character code)', 'paypal'); ?></label>
                        <select name="currency" id="currency">
                            <option value="AUD" <?php if(osc_get_preference('currency', 'paypal')=="AUD") { echo 'selected="selected"';}; ?> >AUD</option>
                            <option value="CAD" <?php if(osc_get_preference('currency', 'paypal')=="CAD") { echo 'selected="selected"';}; ?> >CAD</option>
                            <option value="CHF" <?php if(osc_get_preference('currency', 'paypal')=="CHF") { echo 'selected="selected"';}; ?> >CHF</option>
                            <option value="CZK" <?php if(osc_get_preference('currency', 'paypal')=="CZK") { echo 'selected="selected"';}; ?> >CZK</option>
                            <option value="DKK" <?php if(osc_get_preference('currency', 'paypal')=="DKK") { echo 'selected="selected"';}; ?> >DKK</option>
                            <option value="EUR" <?php if(osc_get_preference('currency', 'paypal')=="EUR") { echo 'selected="selected"';}; ?> >EUR</option>
                            <option value="GBP" <?php if(osc_get_preference('currency', 'paypal')=="GBP") { echo 'selected="selected"';}; ?> >GBP</option>
                            <option value="HKD" <?php if(osc_get_preference('currency', 'paypal')=="HKD") { echo 'selected="selected"';}; ?> >HKD</option>
                            <option value="HUF" <?php if(osc_get_preference('currency', 'paypal')=="HUF") { echo 'selected="selected"';}; ?> >HUF</option>
                            <option value="JPY" <?php if(osc_get_preference('currency', 'paypal')=="JPY") { echo 'selected="selected"';}; ?> >JPY</option>
                            <option value="NOK" <?php if(osc_get_preference('currency', 'paypal')=="NOK") { echo 'selected="selected"';}; ?> >NOK</option>
                            <option value="NZD" <?php if(osc_get_preference('currency', 'paypal')=="NZD") { echo 'selected="selected"';}; ?> >NZD</option>
                            <option value="PLN" <?php if(osc_get_preference('currency', 'paypal')=="PLN") { echo 'selected="selected"';}; ?> >PLN</option>
                            <option value="SEK" <?php if(osc_get_preference('currency', 'paypal')=="SEK") { echo 'selected="selected"';}; ?> >SEK</option>
                            <option value="SGD" <?php if(osc_get_preference('currency', 'paypal')=="SGD") { echo 'selected="selected"';}; ?> >SGD</option>
                            <option value="USD" <?php if(osc_get_preference('currency', 'paypal')=="USD") { echo 'selected="selected"';}; ?> >USD</option>
                        </select>
                        <br/>
                    </div>
                    <br/>
                    <div style="clear:both;"></div>
                    <br/>
                    <div style="float: left; width: 50%;">
                        <p>
                            <?php _e("You could specify up to 3 'packs' that users can buy, so they don't need to pay each time they publish an ad. The credit from the pack will be stored for later uses.",'paypal'); ?>
                        </p>
                        <br/>
                    </div>
                    <div style="float: left; width: 50%;">

                        <label><?php echo sprintf(__('Price of pack #%d', 'paypal'), '1'); ?></label><input type="text" name="pack_price_1" id="pack_price_1" value="<?php echo osc_get_preference('pack_price_1', 'paypal'); ?>" />
                        <br/>
                        <label><?php echo sprintf(__('Price of pack #%d', 'paypal'), '2'); ?></label><input type="text" name="pack_price_2" id="pack_price_2" value="<?php echo osc_get_preference('pack_price_2', 'paypal'); ?>" />
                        <br/>
                        <label><?php echo sprintf(__('Price of pack #%d', 'paypal'), '3'); ?></label><input type="text" name="pack_price_3" id="pack_price_3" value="<?php echo osc_get_preference('pack_price_3', 'paypal'); ?>" />
                        <br/>
                        <button type="submit" style="float: right;"><?php _e('Update', 'paypal');?></button>
                    </div>
                </form>
            </fieldset>
        </div>
        <div style="clear:both;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Help', 'paypal'); ?></legend>
                <h3><?php _e('API or Standard Payments?', 'paypal'); ?></h3>
                <p>
                    <?php _e('API payments give you more control over the payment process, it\'s required for digital goods & micropayments (Note: Not all countries are allowed to have digital goods & micropayments processes). On the other side standard payments are simple, less customizable but works everywhere.', 'paypal'); ?>.
                    <br/>
                    <?php _e('Micropayments offers a reduction on the fee to pay Paypal for orders under 4$ (or equivalent), around 5cents + 5% while standard payments have a fee around 30cents + 5%. Due the nature of OSClass is recommended to use micropayments, but we\'re aware that they\'re not available worldwide. Please check with Paypal the avalaibility of the service in your area.', 'paypal'); ?>.
                    <br/>
                </p>
                <h3><?php _e('Setting up your Paypal account for Standard Payments', 'paypal'); ?></h3>
                <p>
                    <?php _e('Introduce your paypal email and check the "Use Standard Payment" option here.', 'paypal'); ?>.
                    <br/>
                    <?php _e('You need Paypal API credentials (before entering here your API credentials, MODIFY index.php file of this plugin and change the value of PAYPAL_CRYPT_KEY variable to make your API more secure)', 'paypal'); ?>.
                    <br/>
                    <?php _e('You need to tell Paypal where is your IPN file', 'paypal'); ?>
                </p>
                <h3><?php _e('Setting up your Paypal account for micropayments/API', 'paypal'); ?></h3>
                <p>
                    <?php _e('Before being able to use Paypal plugin, you need to set up some configuration at your Paypal account', 'paypal'); ?>.
                    <br/>
                    <?php _e('Your Paypal account has to be set as Business or Premier, you could change that at Your Profile, under My Settings', 'paypal'); ?>.
                    <br/>
                    <?php echo sprintf( __('You need to sign in up for micropayments/digital good <a href="%s">from here</a>.', 'paypal'), 'https://merchant.paypal.com/cgi-bin/marketingweb?cmd=_render-content&content_ID=merchant/digital_goods'); ?>.
                    <br/>
                    <?php _e('You need Paypal API credentials (before entering here your API credentials, MODIFY index.php file of this plugin and change the value of PAYPAL_CRYPT_KEY variable to make your API more secure)', 'paypal'); ?>.
                    <br/>
                    <?php _e('You need to tell Paypal where is your IPN file', 'paypal'); ?>
                </p>
                <h3><?php _e('Setting up your IPN', 'paypal'); ?></h3>
                <p>
                    <?php _e('Click Profile on the My Account tab', 'paypal'); ?>.
                    <br/>
                    <?php _e('Click Instant Payment Notification Preferences in the Selling Preferences column', 'paypal'); ?>.
                    <br/>
                    <?php _e("Click Choose IPN Settings to specify your listener’s URL and activate the listener (usually is http://www.yourdomain.com/oc-content/plugins/paypal/notify_url.php)", 'paypal'); ?>.
                </p>
                <h3><?php _e('How to obtain API credentials', 'paypal'); ?></h3>
                <p>
                    <?php _e('In order to use the Paypal plugin you will need Paypal API credentials, you could obtain them for free following theses steps', 'paypal'); ?>:
                    <br/>
                    <?php _e('Verify your account status. Go to your PayPal Profile under My Settings and verify that your Account Type is Premier or Business, or upgrade your account', "paypal"); ?>.
                    <br/>
                    <?php _e('Verify your API settings. Click on My Selling Tools. Click Selling Online and verify your API access. Click Update to view or set up your API signature and credentials', 'paypal'); ?>.
                </p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>
    </div>
</div>