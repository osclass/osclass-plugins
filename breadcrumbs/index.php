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
Plugin Name: Bread crumbs
Plugin URI: http://www.osclass.org/
Description: Breadcrumbs navigation system.
Version: 1.5.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: breadcrumbs
*/

    function breadcrumbs($separator = '/') {
        $separator = ' ' . $separator . ' ';

        $location = Rewrite::newInstance()->get_location();
        $section  = Rewrite::newInstance()->get_section();
        // You DO NOT have to modify anything else
        if($location=='search') {
            $category = osc_search_category_id();
            var_dump($category);
            if(count($category)==1) {
                $category = $category[0];
            }
        } else if($location=='item' && osc_item()!=null) {
            $category = osc_item_category_id();
        }

        $bc_text = "<a href='".osc_base_url()."' ><span class='bc_root'>".osc_page_title()."</span></a>";
        $deep_c = -1;
        if(isset($category)) {
            $cats = Category::newInstance()->toRootTree($category);
            var_dump($cats);
            if(count($cats)>0) {
                foreach($cats as $cat) {
                    $deep_c++;
                    $bc_text .= $separator."<a href='".breadcrumbs_category_url($cat['pk_i_id'])."' ><span class='bc_level_".$deep_c."'>".$cat['s_name']."</span></a>";
                }
            }
        } else if($location!='index' && $location!='') {
            $bc_text .= $separator."<span class='bc_location'>".$location."</span>";
        }

        if($location=='item' && osc_item()!=null) {
            $bc_text .= $separator."<a href='".osc_item_url()."' ><span class='bc_last'>".osc_item_title()."</span></a>";
        } else if($section!='') {
            $bc_text .= $separator."<span class='bc_last'>".$section."</span>";
        } else {
            $bc_text = str_replace('bc_level_'.$deep_c, 'bc_last', str_replace('bc_location', 'bc_last', $bc_text));
        }

        echo $bc_text;

    }


    function breadcrumbs_category_url($category_id) {
        $path = '' ;
        if ( osc_rewrite_enabled() ) {
            if ($category_id != '') {
                $category = Category::newInstance()->hierarchy($category_id) ;
                $sanitized_category = "" ;
                for ($i = count($category); $i > 0; $i--) {
                    $sanitized_category .= $category[$i - 1]['s_slug'] . '/' ;
                }
                $path = osc_base_url() . $sanitized_category ;
            }
        } else {
            $path = sprintf( osc_base_url(true) . '?page=search&sCategory=%d', $category_id ) ;
        }
        return $path ;
    }


    function breadcrumbs_admin_menu() {
        echo '<h3><a href="#">Breadcrumbs</a></h3>
        <ul> 
            <li><a href="'.osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/help.php').'">&raquo; '.__('F.A.Q. / Help', 'breadcrumbs').'</a></li>
        </ul>';
    }

    function breadcrumbs_help() {
        osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php') ;
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '');
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'breadcrumbs_help');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');
    // Add the help to the menu
    osc_add_hook('admin_menu', 'breadcrumbs_admin_menu');

?>