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
                <legend><?php _e('QR Code Help', 'qrcode'); ?></legend>
                <h3><?php _e('What does QR Code plugin do?', 'qrcode');?></h3>
                <p><?php _e('It display a QR code with the URL of the item. Useful to quick share items with other people, print it and place anywhere.', 'qrcode');?></p>
                <br/>
                <h3><?php _e('IMPORTANT', 'qrcode');?></h3>
                <p><?php _e('In order to work, you will need to place the following lines whereever you want to display the QR. The QR code will be placed inside a <img/> tag at that place.', 'qrcode');?></p>
                <pre>&lt;?php show_qrcode(); ?&gt;</pre>
                <br />
                <p><?php _e('It is also important that you have the GD extension enabled in your server.', 'qrcode');?></p>
                <br />
            </fieldset>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>