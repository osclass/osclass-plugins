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

     $conn = getConnection();
     $stats = $conn->osc_dbFetchResults("SELECT * FROM %st_item_dg_files f, %st_item_dg_downloads d WHERE d.fk_i_file_id = f.pk_i_id ORDER BY f.fk_i_item_id ASC, f.pk_i_id ASC", DB_TABLE_PREFIX, DB_TABLE_PREFIX);
?>
<div style="border: 1px solid #ccc; background: #eee; ">
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <table style="width:600px;">
                <thead>
                    <tr>
                       <th style="width:40px;"><?php _e('Ad', 'digitalgoods'); ?></th>
                       <th><?php _e('File', 'digitalgoods'); ?></th>
                       <th style="width:100px;"><?php _e('Total downloads', 'digitalgoods'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats as $stat) { ?>
                    <tr>
                        <td><?php echo $stat['fk_i_item_id'];?></td>
                        <td><?php echo $stat['s_code']."_".$stat['fk_i_item_id']."_".$stat['s_name'];?></td>
                        <td style="text-align: center;"><?php echo $stat['i_downloads'];?></td>
                    </tr>
                    <?php }; ?>
                </tbody>
            </table>
        </div>
        <div style="clear: both;"></div>										
    </div>
</div>
