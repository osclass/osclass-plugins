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
Plugin Name: Specific theme language
Plugin URI: http://www.osclass.org/
Description: Load an specify theme for each language in case that the theme exists
Version: 0.9.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: theme_language
Plugin update URI: theme-languages
*/

    function load_theme_language( ) {
        $locale            = osc_current_user_locale() ;
        $locale_theme_path = osc_themes_path() . osc_theme() . '-' . $locale . '/';
        // theme structure: theme_name + '-' + locale_code. eg: modern-it_IT
        $locale_theme_name = osc_theme() . '-' . $locale; 
        
        if ( file_exists($locale_theme_path) ) {
            WebThemes::newInstance()->setCurrentTheme( $locale_theme_name ) ;
        }
    }
    
    
    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '') ;

    osc_add_hook('before_html', 'load_theme_language') ;
    
?>
