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
Version: 1.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: demo_theme
*/

    function change_theme ( ) {
        if ( Params::getParam('theme') == '' ) {
            return false ;
        }

        $theme_path = osc_themes_path() . Params::getParam('theme') . '/';
        if ( file_exists($theme_path) ) {
            WebThemes::newInstance()->setCurrentTheme( Params::getParam('theme') ) ;
            $functions_path = WebThemes::newInstance()->getCurrentThemePath() . 'functions.php';
            if ( file_exists($functions_path) ) {
                require_once $functions_path;
            }
        }

        return true ;
    }

    function urls_theme_parameter ( ) {
        if( ( Params::getParam('theme') != '' ) && ( Params::getParam('theme') != osc_theme() ) ) {
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

    function theme_selector_css ( ) {
        echo '<link href="' . osc_base_url() . 'oc-content/plugins/demo_theme/custom.css" media="screen" rel="stylesheet" type="text/css" />' ;
    }

    function theme_selector_top ( ) {
        $themes         = array();
        $selected_theme = ( Params::getParam("theme") != '' ) ? Params::getParam("theme") : osc_theme( ) ;
        $aThemes        = WebThemes::newInstance()->getListThemes();
        foreach($aThemes as $theme) {
            $theme_info = WebThemes::newInstance()->loadThemeInfo($theme) ;
            $themes[]   = array('name' => $theme_info['name'], 'theme' => $theme) ;
        }

        echo '<script type="text/javascript">' ;
        echo '    $(document).ready(function () {' ;
        echo '        var theme = {' ;
        foreach($themes as $t) {
            echo '        ' . $t['theme'] . ': "' . $t['name'] . '", ' ;
        }
        echo '        } ;';
        echo '        var base_url = "' . osc_base_url() . '?theme=" ;' ;
        echo '        $("body").prepend( $("<div>").attr("id", "theme_selector") ) ;' ;
        echo '        $("#theme_selector").append("' . __('Choose a theme', 'demo_theme') . ' ") ;' ;
        echo '        $("#theme_selector").append( $("<select>").attr("id", "select_theme") ) ;' ;
        echo '        $.each(theme, function(key, value) {' ;
        echo '            $("#select_theme").prepend( $("<option>").html(value).attr("value", key) ) ;' ;
        echo '        }) ;' ;
        echo '        $("#select_theme option[value=\'' . $selected_theme .'\']").attr(\'selected\', \'selected\') ;' ;
        echo '        $("#select_theme").change(function () {' ;
        echo '            window.location = base_url + $(this).val() ;' ;
        echo '        }) ;' ;
        echo '    }) ;' ;
        echo '</script>' ;
    }

    osc_add_hook('before_html', 'change_theme') ;

    osc_add_hook('header', 'theme_selector_css') ;

    osc_add_hook('footer', 'urls_theme_parameter') ;
    osc_add_hook('footer', 'theme_selector_top') ;
?>