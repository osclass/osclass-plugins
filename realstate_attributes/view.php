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
if(Params::getParam('plugin_action')!='') {
    if(Params::getParam('plugin_action')=="insertion") {
        osc_set_preference('insertion',Params::getParam('insertion'),'realstate_attributes');
        osc_add_flash_warning_message(__('Settings saved!','realstate_attributes'),'realstate_attributes');
    }
    osc_reset_preferences();
    $insertion = osc_get_preference('insertion','realstate_attributes');
}
?>
<?php
osc_show_flash_message('realstate_attributes');
?>
<form name="propertys_form"  action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="realstate_attributes/view.php" />
    <input type="hidden" name="plugin_action" value="insertion" />
    <h2><?php _e('Insertion','realstate_attributes'); ?></h2>
    <strong><?php _e('Display realstate attributtes','realstate_attributes'); ?></strong>
    <p><input type="radio" name="insertion" value="auto" <?php if($insertion == 'auto' || $insertion == ''){ echo 'checked="checked"'; } ?>> <?php _e('After item description','realstate_attributes'); ?></p>
    <p><input type="radio" name="insertion" value="manual" <?php if($insertion == 'manual'){ echo 'checked="checked"'; } ?>> <?php _e('Manually','realstate_attributes'); ?></p>
    <button type="submit" ><?php _e('Add new', 'realstate_attributes'); ?></button>
</form>