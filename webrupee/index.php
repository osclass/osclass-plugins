<?php
/*
Plugin Name: Replace Indian currency
Plugin URI: http://www.osclass.org/
Description: This plugin implements webrupee.com web API
Version: 1.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: webrupee
Plugin update URI: web-rupee
*/

    function webrupee_css() {
        echo '<!-- WebRupee plugin -->' ;
        echo '<link rel="stylesheet" type="text/css" href="http://cdn.webrupee.com/font">' ;
        echo '<!-- /WebRupee plugin -->' ;
    }

    function webrupee_price($price) {
        $webrupee = '<span class="WebRupee">Rs.</span>' ;
        if( osc_theme() == 'modern' ) {
            $webrupee = '<span class="WebRupee" style="border:0 ; margin:0 ; padding: 0;">Rs.</span>' ;
        }

        $price = preg_replace('|RS\.|i', $webrupee, $price) ;
        $price = preg_replace('|INR|i', $webrupee, $price) ;

        return $price ;
    }

    // add CSS from WebRupee in the header
    osc_add_hook( 'header', 'webrupee_css' ) ;
    
    osc_add_filter( 'item_price', 'webrupee_price' ) ;

    /* file end: ./webrupee/index.php */
?>
