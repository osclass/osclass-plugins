<?php
/*
Plugin Name: Embed Youtube videos
Plugin URI: http://www.osclass.org/
Description: This plugin extends the item to embed youtube videos.
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: youtube
Plugin update URI: 
*/

    define( 'YOUTUBE_PATH', dirname( __FILE__) . '/' ) ;
    define( 'YOUTUBE_TABLE', DB_TABLE_PREFIX . 't_item_youtube' ) ;

    // use old functions if version is previous to 2.3
    if ( version_compare( OSCLASS_VERSION, '2.3', '<' ) ) {
        require_once( YOUTUBE_PATH . 'youtube-old.php' ) ;
    }

    if( !function_exists( 'youtube_call_after_install' ) ) {
        function youtube_call_after_install() {
            $conn = DBConnectionClass::newInstance() ;
            $c_db = $conn->getOsclassDb() ;
            $comm = new DBCommandClass( $c_db ) ;

            $path = osc_plugin_resource( 'youtube/struct.sql' ) ;
            $sql  = file_get_contents( $path ) ;
            $comm->importSQL( $sql ) ;
        }
    }

    if( !function_exists( 'youtube_call_after_uninstall' ) ) {
        function youtube_call_after_uninstall() {
            $conn = DBConnectionClass::newInstance() ;
            $c_db = $conn->getOsclassDb() ;
            $comm = new DBCommandClass( $c_db ) ;

            $comm->query( sprintf( 'DROP TABLE %s', YOUTUBE_TABLE ) ) ;
        }
    }

    function youtube_form($catID = null) {
        require_once( YOUTUBE_PATH . 'item_form.php' ) ;
    }

    if( !function_exists( 'youtube_form_post' ) ) {
        function youtube_form_post($catID = null, $itemID = null)  {
            $youtube_video = Params::getParam( 's_youtube' ) ;
            $youtube_video = convert_youtube_url( $youtube_video ) ;
            if( empty($youtube_video) ) return false ;

            $conn = DBConnectionClass::newInstance() ;
            $c_db = $conn->getOsclassDb() ;
            $comm = new DBCommandClass( $c_db ) ;

            $values = array(
                'fk_i_item_id' => $itemID,
                's_youtube'    => $youtube_video
            ) ;
            $comm->insert( YOUTUBE_TABLE, $values ) ;
        }
    }

    function youtube_get_row($itemID) {
        $conn = DBConnectionClass::newInstance() ;
        $c_db = $conn->getOsclassDb() ;
        $comm = new DBCommandClass( $c_db ) ;

        $comm->select() ;
        $comm->from( YOUTUBE_TABLE ) ;
        $comm->where( 'fk_i_item_id', $itemID ) ;
        $rs = $comm->get() ;

        if( $rs === false ) {
            return false ;
        }

        if( $rs->numRows() != 1 ) {
            return false ;
        }

        return $detail = $rs->row() ;
    }

    if( !function_exists( 'youtube_item_detail' ) ) {
        function youtube_item_detail() {
            $detail = youtube_get_row( osc_item_id() ) ;
            if( $detail ) {
                require_once( YOUTUBE_PATH . 'item_detail.php' ) ;
            }
        }
    }

    if( !function_exists( 'youtube_item_edit' ) ) {
        function youtube_item_edit($catID = null, $itemID = null) {
            $detail = array( 's_youtube' => '' ) ;
            $row    = youtube_get_row( $itemID ) ;
            if( $row ) {
                $detail = $row ;
            }

            require_once( YOUTUBE_PATH . 'item_form.php' ) ;
        }
    }

    if( !function_exists( 'youtube_item_edit_post' ) ) {
        function youtube_item_edit_post($catId = null, $itemID = null) {
            $youtube_video = addslashes(Params::getParam('s_youtube'));
            $youtube_video = convert_youtube_url($youtube_video);

            $conn = DBConnectionClass::newInstance() ;
            $c_db = $conn->getOsclassDb() ;
            $comm = new DBCommandClass( $c_db ) ;

            $values = array(
                'fk_i_item_id' => $itemID,
                's_youtube'    => $youtube_video
            ) ;
            $comm->replace( YOUTUBE_TABLE, $values ) ;
        }
    }

    if( !function_exists( 'youtube_delete_item' ) ) {
        function youtube_delete_item($itemID) {
            $conn = DBConnectionClass::newInstance() ;
            $c_db = $conn->getOsclassDb() ;
            $comm = new DBCommandClass( $c_db ) ;

            $where = array(
                'fk_i_item_id' => $itemID
            ) ;
            $comm->delete( YOUTUBE_TABLE, $where ) ;
        }
    }

    function convert_youtube_url($url) {
        $youtube_url = '' ;
        if( preg_match('|.*?youtube.*?v[\?/=](.{11})|', $url) ) {
            $youtube_url = preg_replace( '|.*?youtube.*?v[\?/=](.{11}).*|', 'http://www.youtube.com/v/$01?fs=1', $url ) ;
            return $youtube_url;
        }

        if( preg_match('|.*?youtu\.be\/(.{11})|', $url) ) {
            $youtube_url = preg_replace( '|.*?youtu\.be\/(.{11}).*|', 'http://www.youtube.com/v/$01?fs=1', $url ) ;
            return $youtube_url ;
        }

        return '' ;
    }

    // create the youtube table when the plugin is installed
    osc_register_plugin( osc_plugin_path(__FILE__), 'youtube_call_after_install' ) ;
    // drop youtube table when the plugin is uninstalled
    osc_add_hook( osc_plugin_path(__FILE__). '_uninstall', 'youtube_call_after_uninstall' ) ;

    // show field in item post layout
    osc_add_hook( 'item_form', 'youtube_form' ) ;
    // insert youtube string
    osc_add_hook( 'item_form_post', 'youtube_form_post' ) ;

    // show video in item detail layout
    osc_add_hook( 'item_detail', 'youtube_item_detail' ) ;

    // show field in item edit layout
    osc_add_hook( 'item_edit', 'youtube_item_edit' ) ;
    // update youtube string after edit POST
    osc_add_hook( 'item_edit_post', 'youtube_item_edit_post' ) ;

    // delete youtube video of the deleted item
    osc_add_hook( 'delete_item', 'youtube_delete_item' ) ;

    /* file end: ./youtube/index.php */
?>