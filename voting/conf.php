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

    if( Params::getParam('plugin_action') == 'post' ) {
    
        /**
         * Save Item form
         */
        $type_vote      = Params::getParam('type_vote');
        $enable_item    = Params::getParam('enable_item');
        if($enable_item == 'on') {
            osc_set_preference('item_voting', '1', 'voting', 'BOOLEAN');
            if($type_vote == 'user') {
                osc_set_preference('open', '0', 'voting', 'BOOLEAN');
                osc_set_preference('user', '1', 'voting', 'BOOLEAN');
            } else if($type_vote == 'open') {
                osc_set_preference('open', '1', 'voting', 'BOOLEAN');
                osc_set_preference('user', '0', 'voting', 'BOOLEAN');
            }
        } else {
            osc_set_preference('item_voting', '0', 'voting', 'BOOLEAN');
        }
        
        /**
         * Save User form
         */
        $enable_user    = Params::getParam('enable_user');
        if($enable_user == 'on') {
            osc_set_preference('user_voting', '1', 'voting', 'BOOLEAN');
        } else {
            osc_set_preference('user_voting', '0', 'voting', 'BOOLEAN');
        }
        echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'voting') . '.</p></div>' ;
        osc_reset_preferences();
    }
?>

<div id="settings_form" style="padding-left: 15px; padding-right: 15px;">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <b style="font-size: 1.5em;"><?php _e('Items', 'voting');?></b>
            <form action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=<?php echo osc_plugin_folder(__FILE__).'conf.php'; ?>" method="POST">
                <input type="hidden" name="plugin_action" value="post" />
                <p><label for="enable_item"><?php _e('Enable for items', 'voting') ; ?></label><input type="checkbox" name="enable_item" <?php if(osc_get_preference('item_voting', 'voting')) echo 'checked="checked"';?>/></p>
                <div style="width: 100%;">
                    <p>
                        <input <?php if(!osc_get_preference('item_voting', 'voting')){ echo 'disabled=""'; }?> name="type_vote" value="open" id="open" type="radio" <?php if(osc_get_preference('open', 'voting')) echo 'checked="checked"'?>/>
                        <label for="open" ><?php _e('Open voting', 'voting'); ?> (<?php _e('All can vote the items', 'voting'); ?>)</label>
                    </p>
                    <p>
                        <input <?php if(!osc_get_preference('item_voting', 'voting')){ echo 'disabled=""'; }?>name="type_vote" value="user" id="user" type="radio" <?php if(osc_get_preference('user', 'voting')) echo 'checked="checked"'?>/>
                        <label for="user"><?php _e('Users voting', 'voting'); ?> (<?php _e("Only registered users can vote", 'voting'); ?>)</label>
                    </p>
                </div>
        </div>
        
        <div style="float: left; width: 100%;">
            <b style="font-size: 1.5em;"><?php _e('Users', 'voting');?></b>
                <p><label for="enable_user"><?php _e('Enable for users', 'voting') ; ?></label><input type="checkbox" name="enable_user" <?php if(osc_get_preference('user_voting', 'voting')) echo 'checked="checked"';?>/></p>
                <input type="submit" value="<?php _e('Save', 'voting'); ?>"/>
            </form>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>
