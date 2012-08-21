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
    $file = Params::getFiles('xml');
    if(isset($file['error']) && $file['error']==0 && isset($file['size']) && $file['size']>0) {
        adimporter_readxml($file['tmp_name']);
    } else {
        osc_add_flash_error_message(__('File uploaded was not valid', 'adimporter'), 'admin');
    }
    

}


?>
<?php osc_show_flash_message('admin') ; ?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Ad importer', 'adimporter'); ?></legend>
                    <form name="jobs_form" id="jobs_form" action="<?php echo osc_admin_base_url(true);?>" method="post" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__);?>importer.php" />
                    <input type="hidden" name="plugin_action" value="done" />

                    <input type="file" name="xml" id="xml" />
                    <label for="upload_xml"><?php _e('Upload XML', 'adimporter'); ?></label>
                    <br/>

                    <button type="submit"><?php _e('Upload', 'adimporter'); ?></button>
                    </form>
            </fieldset>
        </div>
        <div style="float: left; width: 50%;">
            <fieldset>
                <legend><?php _e('Help', 'adimporter'); ?></legend>
                <p>
                    <label>
                        <?php _e('Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. Some text with help. ', 'adimporter'); ?>.
                    </label>
                </p>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
