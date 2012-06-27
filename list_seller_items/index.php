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

/*
Plugin Name: List seller items
Plugin URI: http://www.osclass.org/
Description: Display all seller items.
Version: 1.0.3
Author: Richard Martin
Author URI: http://www.proodi.ca/
Short Name: list_seller_items
Plugin update URI: list-seller-items
*/
 
    function seller_post() {
        if ( osc_item_user_id() == 0 ) return false ;
        
        echo '<a href="' . osc_base_url(true) .  '?page=search&seller_post=' . osc_item_user_id() . '">' ;
		_e('List all items from this seller', 'list_seller_items') ; 
		echo '</a>' ;
    }
   
    function seller_post_admin_menu() {
        echo '<h3><a href="#">' . __('Seller post', 'list_seller_items') . '</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/help.php') . '">&raquo; ' . __('F.A.Q. / Help', 'list_seller_items') . '</a></li>
        </ul>' ;
    }

    function display_search($params = null){
        if ($params == null) {
            return false ;
        }
		
		foreach ($params as $key => $value) {
            if ($value != '') {
                // We may want to have param-specific searches
                switch ($key) {
                    case 'seller_post':
                        Search::newInstance()->addConditions(sprintf("%st_item.fk_i_user_id = %d ", DB_TABLE_PREFIX, $value)) ;
				    break ;
                }
			}
		}
	}

    function seller_post_help() {
        osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php') ;
    }

    // When searching, add some conditions
    osc_add_hook('search_conditions', 'display_search') ;
    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '') ;
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'seller_post_help') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '') ;
    // Add the help to the menu
    osc_add_hook('admin_menu', 'seller_post_admin_menu') ;

?>
