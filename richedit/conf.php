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
        osc_set_preference('theme', Params::getParam('theme'), 'richedit', 'STRING');
        osc_set_preference('skin', Params::getParam('skin'), 'richedit', 'STRING');
        osc_set_preference('width', Params::getParam('width'), 'richedit', 'STRING');
        osc_set_preference('height', Params::getParam('height'), 'richedit', 'STRING');
        osc_set_preference('skin_variant', Params::getParam('skin_variant'), 'richedit', 'STRING');
        osc_set_preference('buttons1', Params::getParam('buttons1'), 'richedit', 'STRING');
        osc_set_preference('buttons2', Params::getParam('buttons2'), 'richedit', 'STRING');
        osc_set_preference('buttons3', Params::getParam('buttons3'), 'richedit', 'STRING');
        osc_set_preference('plugins', Params::getParam('plugins'), 'richedit', 'STRING');
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'richedit') . '.</p></div>' ;
        osc_reset_preferences();
    }
?>
<script type="text/javascript" src="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>tiny_mce/tiny_mce.js"></script>
<script type="text/javascript"> 
    tinyMCE.init({
        mode : "none",
        theme : "<?php echo osc_get_preference('theme', 'richedit'); ?>",
        skin: "<?php echo osc_get_preference('skin', 'richedit'); ?>",
        width: "<?php echo osc_get_preference('width', 'richedit'); ?>",
        height: "<?php echo osc_get_preference('height', 'richedit'); ?>",
        skin_variant : "<?php echo osc_get_preference('skin_variant', 'richedit'); ?>",
        theme_advanced_buttons1 : "<?php echo osc_get_preference('buttons1', 'richedit'); ?>",
        theme_advanced_buttons2 : "<?php echo osc_get_preference('buttons2', 'richedit'); ?>",
        theme_advanced_buttons3 : "<?php echo osc_get_preference('buttons3', 'richedit'); ?>",
        theme_advanced_toolbar_align : "left",
        theme_advanced_toolbar_location : "top",
        plugins : "<?php echo osc_get_preference('plugins', 'richedit'); ?>"
    });
    $(document).ready(function () {
        $("textarea[id^=description]").each(function(){
            tinyMCE.execCommand("mceAddControl", true, this.id);
        });
    });
</script>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Rich edit options', 'richedit'); ?></legend>
                <form name="richedit_form" id="richedit_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <div style="float: left; width: 100%;">
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />
                        <label><?php _e('Theme', 'richedit'); ?></label><br/><input type="text" name="theme" id="theme" value="<?php echo osc_get_preference('theme', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Skin', 'richedit'); ?></label><br/><input type="text" name="skin" id="skin" value="<?php echo osc_get_preference('skin', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Skin variant', 'richedit'); ?></label><br/><input type="text" name="skin_variant" id="skin_variant" value="<?php echo osc_get_preference('skin_variant', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Width (need units, px or %)', 'richedit'); ?></label><br/><input type="text" name="width" id="width" value="<?php echo osc_get_preference('width', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Height (need units, px or %)', 'richedit'); ?></label><br/><input type="text" name="height" id="height" value="<?php echo osc_get_preference('height', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Line of buttons #1 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons1" id="buttons1" value="<?php echo osc_get_preference('buttons1', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Line of buttons #2 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons2" id="buttons2" value="<?php echo osc_get_preference('buttons2', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Line of buttons #3 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons3" id="buttons3" value="<?php echo osc_get_preference('buttons3', 'richedit'); ?>" />
                        <br/>
                        <label><?php _e('Plugins (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="plugins" id="plugins" value="<?php echo osc_get_preference('plugins', 'richedit'); ?>" />
                        <br/>
                        <label><?php echo sprintf(__('Plugins are located in %s. Feel free to add more plugins if you need it', 'richedit'), osc_plugins_path().osc_plugin_folder(__FILE__).'tiny_mce/plguins'); ?>.</label>
                        <br/>
                        <span style="float:right;"><button type="submit" style="float: right;"><?php _e('Update', 'richedit');?></button></span>
                    </div>
                    <br/>
                    <div style="clear:both;"></div>
                </form>
            </fieldset>
            <br/>
            <fieldset>
                <legend><?php _e('Preview of the editor', 'richedit'); ?></legend>
                <div style="float: left; width: 100%;">
                    <textarea id="description"><?php _e('This is a preview of how the rich editor will look like', 'richedit'); ?>.</textarea>
                </div>
                <div style="clear:both;"></div>
                <div>
                    <?php echo sprintf(__('Learn more about the configuration of TinyMCE at %s', 'richedit'), '<a href="http://tinymce.moxiecode.com/wiki.php/Configuration">TinyMCE Wiki</a>');?>
                </div>
                <div style="clear:both;"></div>
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>