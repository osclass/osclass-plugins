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
        osc_set_preference('insertion',Params::getParam('insertion'),'realestate_attributes');
        osc_add_flash_warning_message(__('Settings saved!','realestate_attributes'),'realestate_attributes');
    }
    if(Params::getParam('plugin_action')=="show_filters") {
        osc_set_preference('show_filters',Params::getParam('show_filters'),'realestate_attributes');
        if(Params::getParam('show_filters')=="custom") {
            osc_set_preference('custom-filter',serialize(Params::getParam('custom-filter')),'realestate_attributes');
        }
        osc_add_flash_warning_message(__('Settings saved!','realestate_attributes'),'realestate_attributes');
    }
    osc_reset_preferences();
    $insertion = osc_get_preference('insertion','realestate_attributes');
    $show_filters = osc_get_preference('show_filters','realestate_attributes');
    $custom = unserialize(osc_get_preference('custom-filter','realestate_attributes'));
}
?>
<?php
osc_show_flash_message('realestate_attributes');
?>
<style>
.custom-filters{
    padding-left:30px;
    margin:10px 0;
}
</style>
<form name="propertys_form"  action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="realestate_attributes/view.php" />
    <input type="hidden" name="plugin_action" value="insertion" />
    <h2><?php _e('Display options','realestate_attributes'); ?></h2>
    <strong><?php _e('Display realestate attributes in listing page...','realestate_attributes'); ?></strong>
    <p><input type="radio" name="insertion" value="auto" <?php if($insertion == 'auto' || $insertion == ''){ echo 'checked="checked"'; } ?>> <?php _e('After item description','realestate_attributes'); ?></p>
    <p><input type="radio" name="insertion" value="manual" <?php if($insertion == 'manual'){ echo 'checked="checked"'; } ?>> <?php _e('Manually','realestate_attributes'); ?></p>
    <button type="submit" ><?php _e('Update options', 'realestate_attributes'); ?></button>
</form>

<form name="propertys_form"  action="<?php echo osc_admin_base_url(true); ?>" method="GET" enctype="multipart/form-data" >
    <input type="hidden" name="page" value="plugins" />
    <input type="hidden" name="action" value="renderplugin" />
    <input type="hidden" name="file" value="realestate_attributes/view.php" />
    <input type="hidden" name="plugin_action" value="show_filters" />
    <h2><?php _e('Filters','realestate_attributes'); ?></h2>
    <strong><?php _e('Show filters in search page','realestate_attributes'); ?></strong>
    <p><input type="radio" name="show_filters" value="auto" <?php if($show_filters == 'auto' || $show_filters == ''){ echo 'checked="checked"'; } ?>> <?php _e('Show all filters','realestate_attributes'); ?></p>
    <p><input type="radio" name="show_filters" value="hide" <?php if($show_filters == 'hide'){ echo 'checked="checked"'; } ?>> <?php _e('Hide all filters','realestate_attributes'); ?></p>
    <p><input type="radio" name="show_filters" value="custom" <?php if($show_filters == 'custom'){ echo 'checked="checked"'; } ?>> <?php _e('Show only specified filters','realestate_attributes'); ?></p>
    <div class="custom-filters" id="custom-filters">
        <ul>
            <li><input type="checkbox" name="custom-filter[property_type]" <?php if(isset($custom['property_type'])){ echo 'checked="checked"'; }?>><?php _e('Type', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[p_type]" <?php if(isset($custom['p_type'])){ echo 'checked="checked"'; }?>><?php _e('Property type', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[numFloor]" <?php if(isset($custom['numFloor'])){ echo 'checked="checked"'; }?>><?php _e('Num. Floors Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[rooms]" <?php if(isset($custom['rooms'])){ echo 'checked="checked"'; }?>><?php _e('Rooms Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[bathrooms]" <?php if(isset($custom['bathrooms'])){ echo 'checked="checked"'; }?>><?php _e('Bathrooms Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[garages]" <?php if(isset($custom['garages'])){ echo 'checked="checked"'; }?>><?php _e('Garages Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[year]" <?php if(isset($custom['year'])){ echo 'checked="checked"'; }?>><?php _e('Construction year Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[sq]" <?php if(isset($custom['sq'])){ echo 'checked="checked"'; }?>><?php _e('Square Meters Range', 'realestate_attributes'); ?></li>
            <li><input type="checkbox" name="custom-filter[other]" <?php if(isset($custom['other'])){ echo 'checked="checked"'; }?>><?php _e('Other characteristics', 'realestate_attributes'); ?></li>
        </ul>
    </div> 
    <button type="submit" ><?php _e('Update options', 'realestate_attributes'); ?></button>
    <script type="text/javascript">
    $('input[name="show_filters"]').click(function(){
        if($(this).val() == 'custom'){
            $('#custom-filters').show(); 
        } else {
            $('#custom-filters').hide(); 
        }
    });
    $('input[name="show_filters"]:checked').trigger('click'); 
    </script>
</form>