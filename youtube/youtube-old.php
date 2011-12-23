<?php

    function youtube_call_after_install() {
        $conn = getConnection() ;
        $path = osc_plugin_resource( 'youtube/struct.sql' ) ;
        $sql  = file_get_contents( $path ) ;
        $conn->osc_dbImportSQL( $sql ) ;
    }

    function youtube_call_after_uninstall() {
        $conn = getConnection() ;
        $conn->osc_dbExec( 'DROP TABLE %st_item_youtube', DB_TABLE_PREFIX ) ;
    }

    function youtube_form_post($catID = null, $itemID = null)  {
        $youtube_video = addslashes( Params::getParam( 's_youtube' ) ) ;
        $youtube_video = convert_youtube_url( $youtube_video ) ;
        if( empty( $youtube_video ) ) return false ;
        
        $conn = getConnection() ;
        $conn->osc_dbExec( "INSERT INTO %st_item_youtube (fk_i_item_id, s_youtube) VALUES (%d, '%s')", DB_TABLE_PREFIX, $itemID, $youtube_video ) ;
    }
    
    function youtube_item_detail() {
        $conn   = getConnection() ;
        $detail = $conn->osc_dbFetchResult( "SELECT * FROM %st_item_youtube WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, osc_item_id() ) ;

        require_once( YOUTUBE_PATH . 'item_detail.php' ) ;
    }

    function youtube_item_edit($catID = null, $itemID = null) {
        $conn   = getConnection() ;
        $detail = $conn->osc_dbFetchResult( "SELECT * FROM %st_item_youtube WHERE fk_i_item_id = %d", DB_TABLE_PREFIX, $itemID ) ;

        require_once( YOUTUBE_PATH . 'item_form.php' ) ;
    }

    function youtube_item_edit_post($catID = null, $itemID = null) {
        $youtube_video = addslashes( Params::getParam( 's_youtube' ) ) ;
        $youtube_video = convert_youtube_url( $youtube_video ) ;
        
        $conn = getConnection() ;
        $conn->osc_dbExec( "REPLACE INTO %st_item_youtube (fk_i_item_id, s_youtube) VALUES (%d, '%s')", DB_TABLE_PREFIX, $itemID, $youtube_video ) ;
    }

    function youtube_delete_item($item) {
        $conn = getConnection() ;
        $conn->osc_dbExec( "DELETE FROM %st_item_youtube WHERE fk_i_item_id = '$item'", DB_TABLE_PREFIX ) ;
    }

    /* file end: ./youtube/youtube-old.php */
?>