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
        $type_vote = Params::getParam('type_vote');
        if($type_vote == 'user') {
            osc_set_preference('open', '0', 'voting', 'BOOLEAN');
            osc_set_preference('user', '1', 'voting', 'BOOLEAN');
            echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'voting') . '.</p></div>' ;
        } else if($type_vote == 'open') {
            osc_set_preference('open', '1', 'voting', 'BOOLEAN');
            osc_set_preference('user', '0', 'voting', 'BOOLEAN');
            echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'voting') . '.</p></div>' ;
        }
        osc_reset_preferences();
    }
?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Voting options', 'voting'); ?></legend>
                <form name="voting_form" id="richedit_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
                    <input type="hidden" name="page" value="plugins" />
                    <input type="hidden" name="action" value="renderplugin" />
                    <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
                    <input type="hidden" name="plugin_action" value="done" />
                    <div style="width: 100%;">
                        <p>
                            <input name="type_vote" value="open" id="open" type="radio" <?php if(osc_get_preference('open', 'voting')) echo 'checked="checked"'?>/>
                            <label for="open" ><?php _e('Open voting', 'voting'); ?> (<?php _e('All can vote the items', 'voting'); ?>)</label>
                        </p>
                        <p>
                            <input name="type_vote" value="user" id="user" type="radio" <?php if(osc_get_preference('user', 'voting')) echo 'checked="checked"'?>/>
                            <label for="user"><?php _e('Users voting', 'voting'); ?> (<?php _e("Only registered users can vote", 'voting'); ?>)</label>
                        </p>
                    </div>
                    <input type="submit" value="SAVE"/>
                </form>
            </fieldset>
        </div>
        <div style="clear:both;"></div>
    </div>
</div>