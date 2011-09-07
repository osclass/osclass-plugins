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
Plugin Name: Demo hteme
Plugin URI: http://www.osclass.org/
Description: Rewrite all the urls adding the parameter theme. In addition, it loads the theme passed in the url as parameter. Ideal for showing different themes.
Version: 0.9
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: demo_theme
*/

    function change_theme ( ) {
        $theme_path = osc_themes_path() . Params::getParam('theme') . '/';
        if ( file_exists($theme_path) ) {
            WebThemes::newInstance()->setCurrentTheme( Params::getParam('theme') ) ;        
        }
    }

    function urls_theme_parameter ( ) {
        if( ( Params::getParam('theme') != '' ) || ( Params::getParam('theme') != osc_theme() ) ) {
            $theme = Params::getParam('theme') ;
            $js    = <<<JAVASCRIPT
<script type="text/javascript">
    var theme = '$theme' ;
    \$.each(\$("a"), function (index, value) {
        if(/\?/.test(value) ) {
            \$(this).attr('href', $(this).attr('href') + "&theme=" + theme);
        } else {
            \$(this).attr('href', $(this).attr('href') + "?theme=" + theme);
        }
    });
</script>

JAVASCRIPT;
            echo $js ;
        }
    }

    osc_add_hook('before_html', 'change_theme') ;

    osc_add_hook('footer', 'urls_theme_parameter') ;

?>