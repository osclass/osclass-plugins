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
?>
<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div>
            <fieldset>
                <legend>
                    <h1><?php _e("Ads for OSClass Help", 'ads4osc'); ?></h1>
                </legend>
                <h2><?php _e("What is Ads for OSClass Plugin?", 'ads4osc'); ?></h2>
                <p>
                    <?php _e("Ads for OSClass -also known as Ads4OSClass- plugin allows you to manage and show ads block from several adsvertishing networks", 'ads4osc'); ?>
                </p>
                <h2><?php _e("How does Ads4OSClass plugin work?"); ?></h2>
                <p>
                    <?php _e("You need to import the HTML code from your ad using the admin-menu. Some advertishing network are supported and will offer you more options of customization", 'ads4osc'); ?>.
                </p>
                <h2><?php _e("How could I show some ads on my website"); ?></h2>
                <p>
                    <?php _e("First, create some ad in the admin-menu. then, you should edit your theme files and add the following line anywhere in the code you want an ad to appear", 'ads4osc'); ?>:
                </p>
                <pre>
                &lt;?php show_ads('title_of_the_ad'); ?&gt;
                </pre>
                <p>
                    <?php _e("Where 'title_of_the_ad' is the name of the ad(s) you want to show there. If you have several ads with the same title, they will rotate depending on their weight", 'ads4osc'); ?>.
                </p>
            </fieldset>
        </div>
    </div>
</div>
