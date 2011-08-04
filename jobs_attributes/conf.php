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

if(Params::getParam('plugin_action')=='done') {
    osc_set_preference('cv_email', Params::getParam('cv_email'), 'jobs_plugin', 'STRING');
    osc_set_preference('allow_cv_upload', (Params::getParam('allow_cv_upload')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_set_preference('allow_cv_unreg', (Params::getParam('allow_cv_unreg')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_set_preference('send_me_cv', (Params::getParam('send_me_cv')!=1)?0:1, 'jobs_plugin', 'BOOLEAN');
    osc_set_preference('salary_min', (Params::getParam('salary_min')!='')?Params::getParam('salary_min'):0, 'jobs_plugin', 'INTEGER');
    osc_set_preference('salary_max', (Params::getParam('salary_max')!='' && Params::getParam('salary_max')!=0)?Params::getParam('salary_max'):80000, 'jobs_plugin', 'INTEGER');
    osc_set_preference('salary_step', (Params::getParam('salary_step')!='' && Params::getParam('salary_step')!=0)?Params::getParam('salary_step'):100, 'jobs_plugin', 'INTEGER');
    osc_reset_preferences();
    osc_add_flash_ok_message( __('Settings updated', 'jobs_attributes'), 'admin');
    
} else if(Params::getParam('plugin_action') == 'recalculate') {
    $conn   = getConnection();
    $aItems = $conn->osc_dbFetchResults("SELECT * FROM %st_item_job_attr", DB_TABLE_PREFIX);
    foreach($aItems as $item) {
        $salaryHour = job_to_salary_hour($item['e_salary_period'], $item['i_salary_min'], $item['i_salary_max']);
        $conn->osc_dbExec("REPLACE INTO %st_item_job_attr (fk_i_item_id, i_salary_min_hour, i_salary_max_hour) VALUES (%d, %d, %d)", DB_TABLE_PREFIX, $item['fk_i_item_id'], $salaryHour['min'], $salaryHour['max'] );
    }
    osc_add_flash_ok_message( __('Recalculation finished', 'jobs_attributes'), 'admin');
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
                    <input type="hidden" name="file" value="jobs_attributes/conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_cv_upload', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="allow_cv_upload" id="allow_cv_upload" value="1" />
                    <label for="enabled_comments"><?php _e('Allow upload of resumes', 'jobs_attributes'); ?></label>
                    <br/>

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('allow_cv_unreg', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="allow_cv_unreg" id="allow_cv_unreg" value="1" />
                    <label for="enabled_comments"><?php _e('Allow unregistered users to upload their resumes', 'jobs_attributes'); ?></label>
                    <br/>

                    <input style="height: 20px; padding-left: 4px;padding-top: 4px;" type="checkbox" <?php echo (osc_get_preference('send_me_cv', 'jobs_plugin') ? 'checked="true"' : ''); ?> name="send_me_cv" id="send_me_cv" value="1" />
                    <label for="enabled_comments"><?php _e('Send all emails to the following email (if not checked the resumes will be send to ad\'s author)', 'jobs_attributes'); ?></label>
                    <br/>

                    <label><?php _e('E-mail', 'jobs_attributes');?></label><input type="text" name="cv_email" id="cv_email" value="<?php echo osc_get_preference('cv_email', 'jobs_plugin'); ?>" />
                    <br/>
                    <br/>
                    <label><?php _e('Salary slider min value', 'jobs_attributes');?></label><input type="text" name="salary_min" id="salary_min" value="<?php echo osc_get_preference('salary_min', 'jobs_plugin'); ?>" />
                    <br/>
                    <label><?php _e('Salary slider max value', 'jobs_attributes');?></label><input type="text" name="salary_max" id="salary_max" value="<?php echo osc_get_preference('salary_max', 'jobs_plugin'); ?>" />
                    <br/>
                    <label><?php _e('Salary slider step value', 'jobs_attributes');?></label><input type="text" name="salary_step" id="salary_step" value="<?php echo osc_get_preference('salary_step', 'jobs_plugin'); ?>" />
                    <br/>

                    <button type="submit"><?php _e('Update', 'jobs_attributes'); ?></button>
                    </form>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Recalculate salary hour', 'jobs_attributes'); ?></legend>
                <p>
                    <label>
                        <?php _e('If you have updated this plugin, you need to recalculate values of salary hour, to obtain correct results in searches', 'jobs_attributes'); ?>.
                        <br/>
                        <form name="jobs_form" id="jobs_form" action="<?php echo osc_admin_base_url(true);?>" method="GET" enctype="multipart/form-data" >
                            <input type="hidden" name="page" value="plugins" />
                            <input type="hidden" name="action" value="renderplugin" />
                            <input type="hidden" name="file" value="jobs_attributes/conf.php" />
                            <input type="hidden" name="plugin_action" value="recalculate" />
                            <button type="submit"><?php _e('Recalculate salary hour', 'jobs_attributes'); ?></button>
                        </form>
                    </label>
                </p>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Help', 'jobs_attributes'); ?></legend>
                <p>
                    <label>
                        <?php _e('You could allow users to send their resumes to a specific email address or to send them to the author of the ad. Also you could specify is unregistered users could or could not upload their resumes', 'jobs_attributes'); ?>.
                        <br/>
                        <?php _e('The salary range will appear as a slider, at the search page and at the publish page. You could modify the minimum and maximum values of that slider as well as the value of the "steps" or increments', 'jobs_attributes'); ?>.
                    </label>
                </p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
