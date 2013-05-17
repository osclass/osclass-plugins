<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
    /*
     *      OSCLass - software for creating and publishing online classified
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

<?php sitemap_generator(); ?>

<div id="settings_form" style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div>
            <fieldset>
                <legend>
                    <h1><?php _e('Sitemap Generator Help', 'sitemap_generator'); ?></h1>
                </legend>
                <?php _e('Sitemap.xml generated correctly', 'sitemap_generator'); ?>.
            </fieldset>
        </div>
    </div>
</div>
