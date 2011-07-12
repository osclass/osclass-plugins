<?php
/*
Plugin Name: Social bookmarks
Plugin URI: http://www.osclass.org/
Description: Social bookmarks for item detail page
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: social-bookmarks
*/

    function social_bookmarks($content) {
        $content .= '<div class="social-bookmarks">' ;
        $content .= '<ul>' ;
        // twitter
        $content .= '<li class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></li>' ;
        // facebook
        $content .= '<li class="facebook"><div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="" send="false" layout="button_count" show_faces="false" font=""></fb:like></li>' ;
        // google plus
        $content .= '<li class="google-plus"><script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script><g:plusone size="medium"></g:plusone></li>' ;
        $content .= '</ul>' ;
        // clear
        $content .= '<div class="clear"></div>' ;
        $content .= '</div>';
        return $content ;
    }
    
    function social_bookmarks_header( ) {
        $location   = Rewrite::newInstance()->get_location() ;
        $section    = Rewrite::newInstance()->get_section() ;
        
        if($location == 'item' && $section == '') {
            echo '
            <style type="text/css">
                .social-bookmarks ul { margin: 10px 0; }
                .social-bookmarks ul li { float: left; }
                .social-bookmarks .clear { clear:both; }
            </style>';
        }
    }

    /**
     *  HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), '');
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '');

    osc_add_filter('item_description', 'social_bookmarks');
    osc_add_hook('header', 'social_bookmarks_header');
    
?>