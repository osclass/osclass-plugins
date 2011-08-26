<?php

    $id     = Params::getParam('id') ;
    $item   = Params::getParam('item') ;
    $code   = Params::getParam('code') ;
    $secret = Params::getParam('secret') ;
    $json = array();

    if( Session::newInstance()->_get('userId') != '' ){
        $userId = Session::newInstance()->_get('userId');
        $user = User::newInstance()->findByPrimaryKey($userId);
    }else{
        $userId = null;
        $user = null;
    }

    // Check for required fields
    if ( !( is_numeric($id) && is_numeric($item) ) ) {
        $json['success'] = false;
        $json['msg'] = __("The selected file couldn't be deleted, the url doesn't exist", "digitalgoods");
        echo json_encode($json);
        return false;
    }

    $aItem = Item::newInstance()->findByPrimaryKey($item);

    // Check if the item exists
    if(count($aItem) == 0) {
        $json['success'] = false;
        $json['msg'] = __('The item doesn\'t exist', "digitalgoods");
        echo json_encode($json);
        return false;
    }

    // Check if the item belong to the user
    if($userId != null && $userId != $aItem['fk_i_user_id']) {
        $json['success'] = false;
        $json['msg'] = __('The item doesn\'t belong to you', "digitalgoods");
        echo json_encode($json);
        return false;
    }

    // Check if the secret passphrase match with the item
    if($userId == null && $aItem['fk_i_user_id']==null && $secret != $aItem['s_secret']) {
        $json['success'] = false;
        $json['msg'] = __('The item doesn\'t belong to you', "digitalgoods");
        echo json_encode($json);
        return false;
    }

    $conn = getConnection();
    $result = $conn->osc_dbFetchResult("SELECT * FROM %st_item_dg_files WHERE pk_i_id = %d AND s_name = '%s'", DB_TABLE_PREFIX, $id, $code);

    if (isset($result['pk_i_id'])) {
        
        $conn->osc_dbExec("DELETE FROM %st_item_dg_files WHERE pk_i_id = %d AND s_name = '%s'", DB_TABLE_PREFIX, $id, $code);
        @unlink($result['s_code']."_".$result['fk_i_item_id']."_".$result['s_name']);

        $json['msg'] =  __('The selected file has been successfully deleted', "digitalgoods") ;
        $json['success'] = 'true';
    } else {
        $json['msg'] = __("The selected file couldn't be deleted", "digitalgoods") ;
        $json['success'] = 'false';
    }

    echo json_encode($json);
    return true;


?>
