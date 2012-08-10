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
<?php
require_once('ModelJB.php');

if(Params::getParam('plugin_action')=='done') {
    osc_set_preference('allow_cv_upload', (Params::getParam('allow_cv_upload')!=1)?0:1, 'jobboard_plugin', 'INTEGER');
    osc_reset_preferences();
    osc_add_flash_ok_message( __('Settings updated', 'jobboard_plugin'), 'admin');
    
}

?>
<?php osc_show_flash_message('admin') ; ?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Jobs Options', 'jobs_attributes'); ?></legend>
                    <form name="jobs_form" id="jobs_form" action="<?php echo osc_admin_base_url(true);?>" method="GET" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__);?>conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_cv_upload', 'jobboard_plugin') ? 'checked="true"' : ''); ?> name="allow_cv_upload" id="allow_cv_upload" value="1" />
                    <label for="enabled_comments"><?php _e('Allow upload resumes', 'jobs_attributes'); ?></label>
                    <br/>

                    <button type="submit"><?php _e('Update', 'jobs_attributes'); ?></button>
                    </form>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Help', 'jobs_attributes'); ?></legend>
                <p>
                    <label>
                        <?php _e('Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. ', 'jobs_attributes'); ?>.
                    </label>
                </p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
