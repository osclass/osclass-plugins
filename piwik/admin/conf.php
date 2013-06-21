<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
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
        osc_set_preference('js_code', Params::getParam('js_code'), 'piwik', 'STRING');
        ob_get_clean();
        osc_add_flash_ok_message(__('Congratulations, the plugin is now configured', 'piwik'), 'admin');
        osc_redirect_to(osc_route_admin_url('piwik-conf'));
    }
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Piwik Settings', 'piwik'); ?></legend>
                <form name="piwik_form" id="piwik_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <div style="float: left; width: 100%;">
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="route" value="piwik-conf" />
                    <input type="hidden" name="plugin_action" value="done" />
                        <label for="js_code"><?php _e('Write here the JS code for Piwik Analytics', 'piwik'); ?></label>
                        <br/>
                        <textarea name="js_code" id="js_code" rows="10" style="width:600px"><?php echo osc_get_preference('js_code', 'piwik'); ?></textarea>
                        <br/>
                        <?php _e("To get the JS code, you should first install piwik on your server. More information on http://piwik.org/",'piwik'); ?>
                        <br/>
                        <span style="float:right;"><button type="submit" style="float: right;"><?php _e('Update', 'piwik');?></button></span>
                    </div>
                    <br/>
                    <div style="clear:both;"></div>
                </form>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
