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
Version: 1.6.6
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: breadcrumbs
Plugin update URI: breadcrumbs
*/

    function breadcrumbs($separator = '/') {
        $text       = '';
        $location   = Rewrite::newInstance()->get_location();
        $section    = Rewrite::newInstance()->get_section();
        $separator  = ' ' . trim($separator) . ' ';
        $page_title = '<a href="' . osc_base_url() .  '"><span class="bc_root">' . osc_page_title() . '</span></a>';

        switch ($location) {
            case ('item'):
                switch ($section) {
                    case 'item_add':    break;
                    default :           $aCategories = Category::newInstance()->toRootTree( (string) osc_item_category_id() );
                                        $category    = '';
                                        if(count($aCategories) == 0) {
                                            break;
                                        }

                                        $deep = 1;
                                        foreach ($aCategories as $aCategory) {
                                            $list[] = '<a href="' . breadcrumbs_category_url($aCategory['pk_i_id']) . '"><span class="bc_level_' . $deep . '">' . $aCategory['s_name']. '</span></a>';
                                            $deep++;
                                        }
                                        $category = implode($separator, $list) . $separator;
                                        $category = preg_replace('|' . trim($separator) . '\s*$|', '', $category);
                                        break;
                }

                switch ($section) {
                    case 'item_add':    $text = $page_title . $separator . '<span class="bc_last">' . __('Publish an item', 'breadcrumbs'); break;
                    case 'item_edit':   $text = $page_title . $separator . $category . $separator . '<a href="' . osc_item_url() . '"><span class="bc_item">' . osc_item_title() . '</span></a>' . $separator .  '<span class="bc_last">' . __('Edit your item', 'breadcrumbs') . '</span>'; break;
                    case 'send_friend': $text = $page_title . $separator . $category . $separator . '<a href="' . osc_item_url() . '"><span class="bc_item">' . osc_item_title() . '</span></a>' . $separator .  '<span class="bc_last">' . __('Send to a friend', 'breadcrumbs') . '</span>'; break;
                    case 'contact':     $text = $page_title . $separator . $category . $separator . '<a href="' . osc_item_url() . '"><span class="bc_item">' . osc_item_title() . '</span></a>' . $separator .  '<span class="bc_last">' . __('Contact seller', 'breadcrumbs') . '</span>'; break;
                    default:            $text = $page_title . $separator . $category . $separator . '<span class="bc_last">' . osc_item_title() . '</span>'; break;
                }
            break;
            case('page'):
                $text = $page_title . $separator . '<span class="bc_last">' . osc_static_page_title() . '</span>';
            break;
            case('search'):
                $region     = osc_search_region();
                $city       = osc_search_city();
                $pattern    = osc_search_pattern();
                $category   = osc_search_category_id();
                $category   = ((count($category) == 1) ? $category[0] : '');

                $b_show_all = ($pattern == '' && $category == '' && $region == '' && $city == '');
                $b_category = ($category != '');
                $b_pattern  = ($pattern != '');
                $b_region   = ($region != '');
                $b_city     = ($city != '');
                $b_location = ($b_region || $b_city);

                if($b_show_all) {
                    $text = $page_title . $separator . '<span class="bc_last">' . __('Search', 'breadcrumbs') . '</span>' ;
                    break; 
                }

                // init
                $result = $page_title . $separator;

                if($b_category) {
                    $list        = array();
                    $aCategories = Category::newInstance()->toRootTree($category);
                    if(count($aCategories) > 0) {
                        $deep = 1;
                        foreach ($aCategories as $single) {
                            $list[] = '<a href="' . breadcrumbs_category_url($single['pk_i_id']) . '"><span class="bc_level_' . $deep . '">' . $single['s_name']. '</span></a>';
                            $deep++;
                        }
                        // remove last link
                        if( !$b_pattern && !$b_location ) {
                            $list[count($list) - 1] = preg_replace('|<a href.*?>(.*?)</a>|', '$01', $list[count($list) - 1]);
                        }
                        $result .= implode($separator, $list) . $separator;
                    }
                }

                if( $b_location ) {
                    $list   = array();
                    $params = array();
                    if($b_category) $params['sCategory'] = $category;

                    if($b_city) {
                        $aCity = City::newInstance()->findByName($city);
                        if( count($aCity) == 0 ) {
                            $params['sCity'] = $city;
                            $list[] = '<a href="' . osc_search_url($params) . '"><span class="bc_city">' . $city . '</span></a>';
                        } else {
                            $aRegion = Region::newInstance()->findByPrimaryKey($aCity['fk_i_region_id']);

                            $params['sRegion'] = $aRegion['s_name'];
                            $list[] = '<a href="' . osc_search_url($params) . '"><span class="bc_region">' . $aRegion['s_name'] . '</span></a>';

                            $params['sCity'] = $aCity['s_name'];
                            $list[] = '<a href="' . osc_search_url($params) . '"><span class="bc_city">' . $aCity['s_name'] . '</span></a>';
                        }

                        if( !$b_pattern ) {
                            $list[count($list) - 1] = preg_replace('|<a href.*?>(.*?)</a>|', '$01', $list[count($list) - 1]);
                        }
                        $result .= implode($separator, $list) . $separator;
                    } else if( $b_region ) {
                        $params['sRegion'] = $region;
                        $list[]  = '<a href="' . osc_search_url($params) . '"><span class="bc_region">' . $region . '</span></a>';

                        if( !$b_pattern ) {
                            $list[count($list) - 1] = preg_replace('|<a href.*?>(.*?)</a>|', '$01', $list[count($list) - 1]);
                        }
                        $result .= implode($separator, $list) . $separator;
                    }
                }

                if($b_pattern) {
                    $result .= '<span class="bc_last">' . __('Search Results', 'breadcrumbs') . ': ' . $pattern  . '</span>'. $separator;
                }

                // remove last separator
                $result = preg_replace('|' . trim($separator) . '\s*$|', '', $result);
                $text   = $result;
            break;
            case('login'):
                switch ($section) {
                    case('recover'): $text = $page_title . $separator . '<span class="bc_last">' . __('Recover your password', 'breadcrumbs') . '</span>';
                    default:         $text = $page_title . $separator . '<span class="bc_last">' . __('Login', 'breadcrumbs') . '</span>';
                }
            break;
            case('register'):
                $text = $page_title . $separator . '<span class="bc_last">' . __('Create a new account', 'breadcrumbs') . '</span>';
            break;
            case('user'):
                $user_dashboard = '<a href="' . osc_user_dashboard_url() . '"><span class="bc_user">' . __('My account', 'breadcrumbs') . '</span></a>';
                switch ($section) {
                    case('dashboard'):       $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Dashboard', 'breadcrumbs') . '</span>'; break;
                    case('items'):           $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Manage my items', 'breadcrumbs') . '</span>'; break;
                    case('alerts'):          $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Manage my alerts', 'breadcrumbs') . '</span>'; break;
                    case('profile'):         $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Update my profile', 'breadcrumbs') . '</span>'; break;
                    case('change_email'):    $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Change my email', 'breadcrumbs') . '</span>'; break;
                    case('change_password'): $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Change my password', 'breadcrumbs') . '</span>'; break;
                    case('forgot'):          $text = $page_title . $separator . $user_dashboard . $separator . '<span class="bc_last">' . __('Recover my password', 'breadcrumbs') . '</span>'; break;
                }
            break;
            case('contact'):
                $text = $page_title . $separator . '<span class="bc_last">' . __('Contact', 'breadcrumbs') . '</span>';
            break;
            default:
            break;
        }

        echo $text;

        return true;
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
        return rtrim($path, "/");
    }

    function breadcrumbs_admin_menu() {
        if(osc_version()<320) {
            echo '<h3><a href="#">Breadcrumbs</a></h3>
            <ul>
                <li><a href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/help.php') . '">&raquo; ' . __('F.A.Q. / Help', 'breadcrumbs') . '</a></li>
            </ul>';
        } else {
            osc_add_admin_submenu_page('plugins', __('Breadcrumbs F.A.Q. / Help', 'breadcrumbs'), osc_route_admin_url('breadcrumbs-admin-help'), 'breadcrumbs_help', 'administrator');
        }
    }
    
    if(osc_version()>=320) {
        /**
         * ADD ROUTES (VERSION 3.2+)
         */
        osc_add_route('breadcrumbs-admin-help', 'breadcrumbs/admin/help', 'breadcrumbs/admin/help', osc_plugin_folder(__FILE__).'help.php');
    }


    function breadcrumbs_help() {
        if(osc_version()<320) {
            osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php') ;
        } else {
            osc_redirect_to(osc_route_admin_url('breadcrumbs-admin-help'));
        }
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '');
    // This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'breadcrumbs_help');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '');
    // Add the help to the menu
    if(osc_version()<320) {
        osc_add_hook('admin_menu', 'breadcrumbs_admin_menu');
    } else {
        osc_add_hook('admin_menu_init', 'breadcrumbs_admin_menu');
    }

?>
