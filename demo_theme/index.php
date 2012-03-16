<?php
/*
Plugin Name: Demo theme
Plugin URI: http://www.osclass.org/
Description: Rewrite all the urls adding the parameter theme. In addition, it loads the theme passed in the url as parameter. Ideal for showing different themes.
Version: 1.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: demo_theme
*/

    function change_theme() {
        if( Params::getParam('theme') == '' ) {
            return false ;
        }

        $theme_path = osc_themes_path() . Params::getParam('theme') . '/';
        if( file_exists($theme_path) ) {
            WebThemes::newInstance()->setCurrentTheme( Params::getParam('theme') ) ;
            $functions_path = WebThemes::newInstance()->getCurrentThemePath() . 'functions.php';
            if ( file_exists($functions_path) ) {
                require_once $functions_path;
            }
        }

        return true ;
    }

    function urls_theme_parameter() {
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

    function theme_selector_css() {
        echo '<link href="' . osc_base_url() . 'oc-content/plugins/demo_theme/custom.css" media="screen" rel="stylesheet" type="text/css" />' ;
    }

    function theme_selector_top() {
        $themes         = array();
        $selected_theme = ( Params::getParam("theme") != '' ) ? Params::getParam("theme") : osc_theme( ) ;
        $aThemes        = WebThemes::newInstance()->getListThemes();
        foreach($aThemes as $theme) {
            $theme_info = WebThemes::newInstance()->loadThemeInfo($theme) ;
            $themes[]   = array('name' => $theme_info['name'], 'theme' => $theme) ;
        }

        echo '<script type="text/javascript">' . PHP_EOL ;
        echo '    $(document).ready(function () {' . PHP_EOL ;
        echo '        var theme = {' ;
        foreach($themes as $t) {
            echo '        ' . $t['theme'] . ': "' . $t['name'] . '", ' ;
        }
        echo '        } ;' . PHP_EOL;
        echo '        $("body").prepend( $("<div>").attr("id", "theme_header") ) ;' . PHP_EOL ;
        echo '        $("#theme_header").append( $("<div>").attr("id", "theme_selector") ) ;' . PHP_EOL ;
        echo '        $("#theme_selector").append( $("<label>").html("' . __('Choose a theme', 'demo_theme') . '") ) ;' . PHP_EOL ;
        echo '        $("#theme_selector").append( $("<div>").attr("class", "select") ) ;' . PHP_EOL ;
        echo '        $("#theme_selector .select").append( $("<select>").attr("id", "select_theme") ) ;' . PHP_EOL ;
        echo '        $.each(theme, function(key, value) {' . PHP_EOL ;
        echo '            $("#select_theme").prepend( $("<option>").html(value).attr("value", key) ) ;' . PHP_EOL ;
        echo '        }) ;' . PHP_EOL ;
        echo '        $("#select_theme option[value=\'' . $selected_theme .'\']").attr(\'selected\', \'selected\') ;' . PHP_EOL ;
        echo '        $("#select_theme").change(function () {' . PHP_EOL ;
        echo "            url = window.location.href.replace(/[\?&]theme=[\w]+/, '') ;" . PHP_EOL ;
        echo '            if(/\?/.test(url) ) {' . PHP_EOL ;
        echo "                url = url + '&theme=' + $(this).val() ;" . PHP_EOL ;
        echo "            } else {" . PHP_EOL ;
        echo "                url = url + '?theme=' + $(this).val() ;" . PHP_EOL ;
        echo "            }" . PHP_EOL ;
        echo "            window.location = url ;" ;
        echo "        }) ;" . PHP_EOL ;
        echo '        $("#theme_header").append( $("<div>").css("clear:both;") ) ;' ;
        echo '    }) ;' . PHP_EOL ;
        echo '</script>' . PHP_EOL ;
    }

    osc_add_hook('before_html', 'change_theme') ;

    osc_add_hook('header', 'theme_selector_css') ;

    osc_add_hook('footer', 'urls_theme_parameter') ;
    osc_add_hook('footer', 'theme_selector_top') ;
?>
