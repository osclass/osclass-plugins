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
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Simle Cache Help', 'simplecache'); ?></legend>
                <h3><?php _e('What does Simple Cache plugin do?', 'simplecache');?></h3>
                <p><?php _e('It is a very simple cache system to improce the speed of your server allowing you to server more pages with less resources.', 'simplecache');?></p>
                <br />
                <h3><?php _e('I am using Simple Cache and my site is still slow, what should do I do?', 'simplecache');?></h3>
                <p><?php _e('Simple Cache will improve the speed of your server, it is a very simple cache system, so do not expect for any miracles. If your site is still slow, maybe it is time to invert some money in an upgrade.', 'simplecache');?></p>
                <br />
                <h3><?php _e('Advanced configuration of Simple Cache', 'simplecache');?></h3>
                <p><?php _e('Simple Cache will run fine with the hourly cron, if for some reason you need to run it more often, then you should remove/comment the last line of index.php', 'simplecache');?></p>
                <pre>osc_add_hook('cron_hourly', 'simplecache_cron');</pre>
                <p><?php _e('And add oc-content/plugins/simplecache/manual_cron.php to your system\'s cron.', 'simplecache');?></p>
                <br />
                <h3><?php _e('Changes on theme are not reflected, why?', 'simplecache');?></h3>
                <p><?php _e('Simple Cache is a cache system, that means it make a copy of the pages on the hard-drive, so next time it will load faster. The copy is delete and re-created on set intervals as well as on specific actions (you edit and item for example). Individual changes made to files can not be controlled, and that is the reason why you are still viewing an old copy. To avoid it, clean the cache after the modifications. If you plan to work on the theme for a while, clear the cache, turn it off and work. After you are done with your modifications. Enable the plugin again.', 'simplecache');?></p>
                <br />
                <h3><?php _e('Important', 'simplecache');?></h3>
                <p><?php _e('Simple Cache NEEDS some kind of cron running, it could be the automatic cron OSClass offers, or a manual cron system set up pointing to either OSClass\' cron files or Simple Cache\'s cron files. Cron is a program that will runs some files in set intervals, allowing us to clear the cache and regenerate again.');?></p>
                <br />
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>