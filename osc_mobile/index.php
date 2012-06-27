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
Version: 0.3
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: mobile
Plugin update URI: osc-mobile
*/

    function load_mobile_theme( ) 
    {
        
        if(Params::getParam('desktop') != '') {
            if(Params::getParam('desktop') == 1) {
                Cookie::newInstance()->push('osc-mobile-desktop', 'desktop') ;
                Cookie::newInstance()->set() ;
                if(isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                } 
                header('Location: ' . osc_base_url());
                exit;
            } else {
                Cookie::newInstance()->pop('osc-mobile-desktop', 'mobile') ;
                Cookie::newInstance()->set() ;
                if(isset($_SERVER['HTTP_REFERER'])) {
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                } 
                header('Location: ' . osc_base_url());
                exit;
            }
        }
        
        require_once dirname( osc_plugin_path(__FILE__) ) . '/UserAgentClass.php' ;
        $userAgent = new UserAgent() ;
        
        $desktopVersion = Cookie::newInstance()->get_value('osc-mobile-desktop');
        
        if ( $userAgent->is_mobile() && $desktopVersion != 'desktop' ) {
            WebThemes::newInstance()->setPath( dirname( osc_plugin_path(__FILE__) ) . '/themes/' ) ;
            WebThemes::newInstance()->setCurrentTheme('mobile') ;
            $functions_path = WebThemes::newInstance()->getCurrentThemePath() . 'functions.php';
            if(file_exists($functions_path)) {
                require_once $functions_path;
            }
        }
    }
    
    function show_switch() 
    {
        require_once dirname( osc_plugin_path(__FILE__) ) . '/UserAgentClass.php' ;
        $userAgent = new UserAgent() ;
        
        if ( $userAgent->is_mobile() ) {
            $url = osc_base_url(true).'?desktop=mobile'; 
            echo '<a href="'.$url.'" data-role="none" class="ui-link">'. __('Switch to Mobile version','mobile').'</a>';
        }   
    }    

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), '') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '') ;

    osc_add_hook('before_html', 'load_mobile_theme') ;
    
?>
