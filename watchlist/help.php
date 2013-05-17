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
    <div style="padding: 0 20px 20px;">
        <div>
            <fieldset>
                <legend>
                    <h1><?php _e('Watchlist Help', 'watchlist') ; ?></h1>
                </legend>
                <h2>
                    <?php _e('What is Watchlist Plugin?', 'watchlist') ; ?>
                </h2>
                <p>
                    <?php _e('Watchlist plugin allows you to display a link that will allow user to save items on a watchlist page', 'watchlist') ; ?>.
                </p>
                <h2>
                    <?php _e('How does Watchlist plugin work?', 'watchlist') ; ?>
                </h2>
                <p>
                    <?php _e('In order to use Watchlist plugin, you should edit your theme files and add the following line anywhere in the code you want the Watchlist link to appear', 'watchlist') ; ?>:
                </p>
                <pre>
                    &lt;?php watchlist(); ?&gt;
                </pre>
                <h2>
                    <?php _e('Could I cutomize the style of Watchlist plugin?', 'watchlist') ; ?>
                </h2>
                <p>
                    <?php _e("Of course you can. Watchlist display a link only you can use css to make a button or anything else", 'watchlist') ; ?>.
                </p>
                <h2>
                    <?php _e('Did Watchlist plugin work with all version of OSClass?', 'watchlist') ; ?>
                </h2>
                <p>
                    <?php _e("In order to work this pluggin need OSClass v2.2 and up without this version pluggin will crash", 'watchlist') ; ?>.
                </p>
                <p>
                    <?php printf(__('You have %s version', 'watchlist'), OSCLASS_VERSION); ?>.
                </p>
            </fieldset>
        </div>
    </div>
</div>
