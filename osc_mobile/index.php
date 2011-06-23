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
Plugin Name: Mobile theme
Plugin URI: http://www.osclass.org/
Description: Mobile theme
Version: 0.2
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: mobile
*/

    function load_mobile_theme( ) {
        require_once dirname( osc_plugin_path(__FILE__) ) . '/UserAgentClass.php' ;
        
        $userAgent = new UserAgent() ;
        
        if ( $userAgent->is_mobile() ) {
            WebThemes::newInstance()->setPath( dirname( osc_plugin_path(__FILE__) ) . '/themes/' ) ;
            WebThemes::newInstance()->setCurrentTheme('mobile') ;
        }
    }
    
    

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '') ;

    osc_add_hook('before_html', 'load_mobile_theme') ;
    
?>