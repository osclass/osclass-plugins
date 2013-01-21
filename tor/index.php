<?php
/*
Plugin Name: Tor plugin
Plugin URI: http://www.osclass.org/
Description: Only allows tor connection to access your website
Version: 1.0
Author: Osclass
Author URI: http://www.osclass.org/
Plugin update URI: tor
*/

    function tor_init() {

        include 'tor.class.php';

        $tor = Tor::getInstance();
        if(!$tor->isTorActive() && !OC_ADMIN) {
            require_once LIB_PATH . 'osclass/helpers/hErrors.php' ;
            osc_die(__("You could only access using TOR networks", "tor"), __("You could only access using TOR networks", "tor"));
        }

    }

    osc_add_hook('init', 'tor_init');

?>
