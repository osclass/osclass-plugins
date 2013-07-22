<?php if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

/**
 *  Recive and save votes from frontend.
 */

$votedUserId = (Params::getParam("userId") == '')  ? null : Params::getParam("userId");
$itemId      = (Params::getParam("itemId") == '')  ? null : Params::getParam("itemId");
$iVote       = (Params::getParam("vote") == '')  ? null : Params::getParam("vote");

$userId = osc_logged_user_id();
$hash   = '';

// Vote Users
if(isset($iVote) && is_numeric($iVote) && isset($votedUserId) && is_numeric($votedUserId) )
{
    if( $iVote<=5 && $iVote>=1){
        if(can_vote_user($votedUserId, $userId)) {
            ModelVoting::newInstance()->insertUserVote($votedUserId, $userId, $iVote);
        }
    }
    // return updated voting
    $aux_vote  = ModelVoting::newInstance()->getUserAvgRating($votedUserId);
    $aux_count = ModelVoting::newInstance()->getUserNumberOfVotes($votedUserId);
    $vote['vote']  = $aux_vote['vote'];
    $vote['total'] = $aux_count['total'];
    $vote['userId'] = $votedUserId;

    $vote['can_vote'] = true;
    if(!osc_is_web_user_logged_in() || !can_vote_user($votedUserId, $userId) ) {
        $vote['can_vote'] = false;
    }

    require 'view_votes_user.php';
}

// Vote Items
if(isset($iVote) && is_numeric($iVote) && isset($itemId) && is_numeric($itemId) )
{
    if( $iVote<=5 && $iVote>=1){
        if( $userId == 0 ) {
            $userId = 'NULL';
            $hash   = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
            $hash = sha1($hash);
        } else {
            $hash = null;
        }

        $open = osc_get_preference('open', 'voting');
        $user = osc_get_preference('user', 'voting');
        if($open == 1) {
            if(can_vote($itemId, $userId, $hash)) {
                ModelVoting::newInstance()->insertItemVote($itemId, $userId, $iVote, $hash);
            }
        } else if($user == 1 && is_null($hash) ) {
            if(can_vote($itemId, $userId, $hash)) {
                ModelVoting::newInstance()->insertItemVote($itemId, $userId, $iVote, $hash);
            }
        }
    }
    // return updated voting
    $item = Item::newInstance()->findByPrimaryKey($itemId);
    View::newInstance()->_exportVariableToView('item', $item);
    if (osc_is_this_category('voting', osc_item_category_id())) {
        $aux_vote  = ModelVoting::newInstance()->getItemAvgRating(osc_item_id());
        $aux_count = ModelVoting::newInstance()->getItemNumberOfVotes(osc_item_id());
        $vote['vote']  = $aux_vote['vote'];
        $vote['total'] = $aux_count['total'];

        $vote['can_vote'] = true;
        if(osc_get_preference('user', 'voting') == 1) {
            if(!osc_is_web_user_logged_in()) {
                $vote['can_vote'] = false;
            }
        }
        if(!can_vote(osc_item_id(), osc_logged_user_id(), $hash) ){
            $vote['can_vote'] = false;

        }

        require 'view_votes.php';
    }
}
?>
