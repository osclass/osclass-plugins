<?php

$itemId = (Params::getParam("itemId") == '')  ? null : Params::getParam("itemId");
$userId = osc_logged_user_id();
$vote   = (Params::getParam("vote") == '')  ? null : Params::getParam("vote");
$hash   = '';

if(isset($vote) && is_numeric($vote) && isset($itemId) && is_numeric($itemId) ) {
    if( $vote<=5 && $vote>=1){
        if( $userId == 0 ) {
            $userId = 'NULL';
            $hash   = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
            $hash = sha1($hash);
        } else {
            $hash = null;
        }
        
        $conn = getConnection();
        $open = osc_get_preference('open', 'voting');
        $user = osc_get_preference('user', 'voting');
        if($open == 1) {
            if(can_vote($itemId, $userId, $hash)) {
                $conn->osc_dbExec("INSERT INTO %st_voting_item (fk_i_item_id, fk_i_user_id, i_vote, s_hash) VALUES (%s, %s, %s, '%s')",DB_TABLE_PREFIX, $itemId, $userId, $vote, $hash);
            }
        } else if($user == 1 && is_null($hash) ) {
            if(can_vote($itemId, $userId, $hash)) {
                $conn->osc_dbExec("INSERT INTO %st_voting_item (fk_i_item_id, fk_i_user_id, i_vote, s_hash) VALUES (%s, %s, %s, '%s')",DB_TABLE_PREFIX, $itemId, $userId, $vote, $hash);
            }
        }
    }
    // return updated voting
    $item = Item::newInstance()->findByPrimaryKey($itemId);
    View::newInstance()->_exportVariableToView('item', $item);
    if (osc_is_this_category('voting_plugin', osc_item_category_id())) {
        $conn = getConnection();
        $aux_vote  = $conn->osc_dbFetchResult('SELECT format(avg(i_vote),1) as vote FROM %st_voting_item WHERE fk_i_item_id = %s', DB_TABLE_PREFIX, osc_item_id());
        $aux_count = $conn->osc_dbFetchResult('SELECT count(*) as total FROM %st_voting_item WHERE fk_i_item_id = %s', DB_TABLE_PREFIX, osc_item_id());
        $vote_['vote']  = $aux_vote['vote'];
        $vote_['total'] = $aux_count['total'];
        
        $vote_['can_vote'] = true;
        if(osc_get_preference('user', 'voting') == 1) {
            if(!osc_is_web_user_logged_in()) {
                $vote_['can_vote'] = false;
            }
        }
        if(!can_vote(osc_item_id(), osc_logged_user_id(), $hash) ){
            $vote_['can_vote'] = false;
            
        }
        
        require_once 'view_votes.php';
    }
}
?>
