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

<?php osc_show_widgets('footer'); ?>
<?php $url = osc_base_url(true).'?desktop=1'; ?>
<p><a href="<?php echo $url; ?>" data-role="none" class="ui-link"><?php _e('Switch to Desktop version','osc_mobile');?></a></p>
<p>
    <a data-role="none" class="ui-link" href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'osc_mobile') ; ?></a> |
    <?php osc_reset_static_pages() ; ?>
    <?php while( osc_has_static_pages() ) { ?>
        <a data-role="none" class="ui-link" href="<?php echo osc_static_page_url() ; ?>"><?php echo osc_static_page_title() ; ?></a> |
    <?php } ?>
    <?php _e('This website is proudly using the <a title="OSClass web" data-role="none" class="ui-link" href="http://osclass.org/">open source classifieds</a> software <strong>OSClass</strong>', 'osc_mobile'); ?>.
</p>

