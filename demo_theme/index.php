<?php
/*
Plugin Name: Demo theme
Plugin URI: http://www.osclass.org/
Description: Rewrite all the urls adding the parameter theme. In addition, it loads the theme passed in the url as parameter. Ideal for showing different themes.
Version: 1.2.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: demo_theme
Plugin update URI: demo-theme
*/

    /**
     * Set the theme is going to be loaded. It can be passed by parameter or get from the Cookie
     */
    if( !function_exists('change_theme') ) {
        function change_theme() {
            $theme = '' ;
            // check first if it has been set in the cookie
            if( Cookie::newInstance()->get_value('demo_theme') != '' ) {
                $theme = Cookie::newInstance()->get_value('demo_theme') ;
            }

            if( Params::getParam('theme') != '' ) {
                $theme = Params::getParam('theme') ;
                Cookie::newInstance()->set_expires( 86400 * 30 ) ;
                Cookie::newInstance()->push('demo_theme', $theme) ;
                Cookie::newInstance()->set() ;
            }

            if( $theme == '' ) {
                return false ;
            }

            $theme_path = osc_themes_path() . $theme . '/';
            if( file_exists($theme_path) ) {
                WebThemes::newInstance()->setCurrentTheme($theme) ;
                $functions_path = WebThemes::newInstance()->getCurrentThemePath() . 'functions.php';
                if ( file_exists($functions_path) ) {
                    require_once $functions_path ;
                }
            }

            return true ;
        }
    }

    /**
     * Echo the link used in the header
     */
    function theme_selector_css() {
        echo '<link href="' . osc_base_url() . 'oc-content/plugins/demo_theme/custom.css" media="screen" rel="stylesheet" type="text/css" />' ;
    }

    /**
     * Get the actual theme. There are three options (in order of preference):
     *     - Theme via parameter
     *     - Theme via Cookie
     *     - Default theme
     *
     * @return string The name of the selected theme
     */
    function selected_theme() {
        $theme = osc_theme() ;

        if( Cookie::newInstance()->get_value('demo_theme') != '' ) {
            $theme = Cookie::newInstance()->get_value('demo_theme') ;
        }

        if( Params::getParam('theme') != '' ) {
            $theme = Params::getParam('theme') ;
        }

        return $theme ;
    }

    /**
     * HTML showed in the top of the page
     */
    function theme_selector_top() {
        $themes              = array();
        $selected_theme      = selected_theme() ;
        $info_selected_theme = WebThemes::newInstance()->loadThemeInfo($selected_theme) ;
        $aThemes             = WebThemes::newInstance()->getListThemes();
        foreach($aThemes as $theme) {
            $theme_info = WebThemes::newInstance()->loadThemeInfo($theme) ;
            $themes[]   = array('name' => $theme_info['name'], 'theme' => $theme) ;
        }

        echo '<script type="text/javascript">' . PHP_EOL ;
        echo '    $(document).ready(function () {' . PHP_EOL ;
        echo '        var theme = { ' ;
        for($i = 0; $i < count($themes); $i++) {
            echo $themes[$i]['theme'] . ': "' . $themes[$i]['name'] . '"' ;
            if( $i < (count($themes) - 1) ) {
                echo ', ' ;
            }
        }
        echo ' } ;' . PHP_EOL;
        echo '        $("body").prepend( $("<div>").attr("id", "theme_header") ) ;' . PHP_EOL ;




        // wrapper_demo
        echo '        var wrapper = $("<div>").attr("class", "wrapper_demo") ;' . PHP_EOL ;

        // logo
        echo '        var part_logo = $("<div>").attr("class", "logo_demo") ;' . PHP_EOL ;
        echo '        var link_logo = $("<a>").attr("href", "http://osclass.org") ;' . PHP_EOL ;
        echo '        var img_logo = $("<img>").attr("src", "'.osc_base_url().'oc-content/plugins/demo_theme/default-logo.png") ;' . PHP_EOL ;
        echo '        $(link_logo).prepend(img_logo) ;' . PHP_EOL ;
        echo '        $(part_logo).prepend(link_logo) ;' . PHP_EOL ;

        // oc-admin link
        echo '        var part_ocadmin = $("<div>").attr("class", "ocadmin_demo") ;' . PHP_EOL ;
        echo '        var link_ocadmin = $("<a>").attr("href", "'.osc_admin_base_url(true).'") ;' . PHP_EOL ;
        echo '        $(link_ocadmin).attr("class", "button-link") ;' . PHP_EOL ;
        echo '        $(link_ocadmin).text("'.__('Admin panel', 'demo_theme').'") ;' . PHP_EOL ;
        echo '        $(part_ocadmin).prepend(link_ocadmin) ;' . PHP_EOL ;

        echo '        var part_selector = $("<div>").attr("id", "theme_selector") ;' . PHP_EOL ;

        echo '        $(part_selector).append( $("<label>").html("' . __('Choose a theme', 'demo_theme') . '") ) ;' . PHP_EOL ;
        echo '        $(part_selector).append( $("<div>").attr("class", "select") ) ;' . PHP_EOL ;

        echo '        $(wrapper).append(part_logo) ;' . PHP_EOL ;
        echo '        $(wrapper).append( part_selector ) ;' . PHP_EOL ;
        echo '        $(wrapper).append( part_ocadmin ) ;' . PHP_EOL ;
        echo '        $(wrapper).append( $("<div>").attr("class", "clear") ) ;' . PHP_EOL ;

        echo '        $("#theme_header").append(wrapper) ; ';
        echo '        $("#theme_header").after( $("<div>").attr("class", "clear") ) ;' . PHP_EOL ;

        echo '        var select = $("<select>").attr("id", "select_theme") ; ';
        echo '        $(part_selector).find(".select").append( select ) ;' . PHP_EOL ;

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
        if( @$info_selected_theme['theme_update_uri'] != "") {
            echo "        $('#theme_selector .select').append( $('<span>').html( $('<a>').attr('href', 'http://market.osclass.org/api/view/" . @$info_selected_theme['theme_update_uri'] . "').attr('target', '_blank').html('Download " . $info_selected_theme['name'] . " theme') ) ) ; " . PHP_EOL ;
        }



        echo '        $("#theme_header").append( $("<div>").css("clear:both;") ) ;' ;
        echo '    }) ;' . PHP_EOL ;
        echo '</script>' . PHP_EOL ;

    }

    osc_add_hook('before_html', 'change_theme') ;

    osc_add_hook('header', 'theme_selector_css') ;

    osc_add_hook('footer', 'theme_selector_top') ;

?>
