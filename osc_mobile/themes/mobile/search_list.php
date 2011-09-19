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
    osc_get_premiums();
    if(osc_count_premiums() > 0) {
?>

<ul data-role="listview" data-inset="true" data-theme="b">
    <?php while(osc_has_premiums()) { ?>
    <li>
        <a style="min-height: 30px; height: 30px;" href="<?php echo osc_item_url() ; ?>">
            <?php if(osc_count_item_resources()) { ?>
            <img style="padding-left:4px; float:left;padding-right: 10px;" src="<?php echo osc_resource_thumbnail_url() ; ?>" height="50px" title="" alt="" />
            <?php } else { ?>
            <img style="padding-left:4px;float:left;padding-right: 10px;" height="50px" src="<?php echo osc_current_web_theme_url('images/no_photo.gif') ; ?>" title="" alt="" />
            <?php } ?>

            <p>
                <strong>
                    <?php echo osc_item_title() ; ?>
                </strong>
            </p>
            <p>
                <span style="float:left;"><?php echo osc_item_city();?></span>
                <span style="float:right;padding-top:3px;"><strong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; }?> </strong></span>
            </p>
        </a>
    </li>
    <?php } ?>
</ul>
<?php } ?>

<ul data-role="listview" data-inset="true" data-theme="d">
    <?php while(osc_has_items()) { ?>
    <li>
        <a style="min-height: 30px; height: 30px;" href="<?php echo osc_item_url() ; ?>">
            <?php if(osc_count_item_resources()) { ?>
            <img style="padding-left:4px; float:left;padding-right: 10px;" src="<?php echo osc_resource_thumbnail_url() ; ?>" height="50px" title="" alt="" />
            <?php } else { ?>
            <img style="padding-left:4px;float:left;padding-right: 10px;" height="50px" src="<?php echo osc_current_web_theme_url('images/no_photo.gif') ; ?>" title="" alt="" />
            <?php } ?>

            <p>
                <strong>
                    <?php echo osc_item_title() ; ?>
                </strong>
            </p>
            <p>
                <span style="float:left;"><?php echo osc_item_city();?></span>
                <span style="float:right;padding-top:3px;"><strong><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price() ; }?> </strong></span>
            </p>
        </a>
    </li>
    <?php } ?>
</ul>