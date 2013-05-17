<?php
/*
Plugin Name: Voting
Plugin URI: http://www.osclass.org/
Description: Voting system
Version: 1.1.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: voting
Plugin update URI: voting
*/

    require_once 'ModelVoting.php' ;

    /**
     * Set plugin preferences
     */
    function voting_install()
    {
        ModelVoting::newInstance()->import('voting/struct.sql');
        // vote items
        osc_set_preference('item_voting', '1', 'voting', 'BOOLEAN');
        osc_set_preference('open', '1', 'voting', 'BOOLEAN');
        // vote users
        osc_set_preference('user', '0', 'voting', 'BOOLEAN');
        osc_set_preference('user_voting', '0', 'voting', 'BOOLEAN');
    }

    /**
     * Delete plugin preferences
     */
    function voting_uninstall()
    {
        ModelVoting::newInstance()->uninstall();
        // vote items
        osc_delete_preference('item_voting', 'voting');
        osc_delete_preference('open', 'voting');
        // vote users
        osc_delete_preference('user', 'voting');
        osc_delete_preference('user_voting', 'voting');
    }

    /**
     * Admin panel menu
     */
    function voting_admin_menu()
    {
        echo '<h3><a href="#">' . __('Voting options', 'voting') . '</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'voting') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php') . '">&raquo; ' . __('Help', 'voting') . '</a></li>
        </ul>';
    }

    function voting_admin_configuration()
    {
        // Standard configuration page for plugin which extend item's attributes
        osc_plugin_configure_view(osc_plugin_path(__FILE__));
    }

    /**************************************************************************
     *                          VOTE ITEMS
     *************************************************************************/

    /**
     * Show form to vote an item. (itemDetail)
     */
    function voting_item_detail()
    {
        if (osc_is_this_category('voting', osc_item_category_id()) && osc_get_preference('item_voting', 'voting') == '1' ) {
            $aux_vote  = ModelVoting::newInstance()->getItemAvgRating( osc_item_id() );
            $aux_count = ModelVoting::newInstance()->getItemNumberOfVotes( osc_item_id() );
            $vote['vote']  = $aux_vote['vote'];
            $vote['total'] = $aux_count['total'];

            $hash   = '';
            if( osc_logged_user_id() == 0 ) {
                $hash   = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
                $hash = sha1($hash);
            } else {
                $hash = null;
            }

            $vote['can_vote'] = true;
            if(osc_get_preference('user', 'voting') == 1) {
                if(!osc_is_web_user_logged_in()) {
                    $vote['can_vote'] = false;
                }
            }

            if(!can_vote(osc_item_id(), osc_logged_user_id(), $hash) ){
                $vote['can_vote'] = false;
            }
            require 'item_detail.php';
         }
    }

    /**
     * Check if user can vote an item
     *
     * @param string $itemId
     * @param string $userId
     * @param string $hash
     * @return bool
     */
    function can_vote($itemId, $userId, $hash)
    {
        if( $userId == 'NULL' ) {
            $result = Modelvoting::newInstance()->getItemIsRated($itemId, $hash);
        } else {
            $result = Modelvoting::newInstance()->getItemIsRated($itemId, $hash, $userId);
        }

        if( count($result) > 0 )
            return false;
        else
            return true;
    }

    /**
     * Return layout optimized for sidebar at main web page, with the best items voted with a limit
     *
     * @param int $num number of items
     */
    function echo_best_rated($num = 5)
    {
        if( osc_get_preference('item_voting', 'voting') == 1 ) {
            $filter = array(
                'order'       => 'desc',
                'num_items'   => $num
            );
            $results = get_votes($filter);
            if(count($results) > 0 ) {
                error_log( print_r($results, true) );
                $locale  = osc_current_user_locale();
                require 'set_results.php';
            }
        }
    }

    /**
     * Return an array of item votes with given filters
     * <code>
     * array(
     *          'category_id' => (integer_category_id),
     *          'order'       => ('desc','asc'),
     *          'num_items'   => (integer)
     *      );
     * </code>
     * @param type $array_filters
     * @return array of item votes
     */
    function get_votes($array_filters)
    {
        $category_id = null;
        $order       = 'desc';
        $num         = 5;
        if(isset($array_filters['category_id'])){
            $category_id = $array_filters['category_id'];
        }
        if(isset($array_filters['order'])){
            $order = strtolower($array_filters['order']);
            if( !in_array($order, array('desc', 'asc') ) ){
                $order = 'desc';
            }
        }
        if(isset($array_filters['num_items'])){
            $num = (int)$array_filters['num_items'];
        }

       return ModelVoting::newInstance()->getItemRatings($category_id, $order, $num);
    }

    /**
     * hook delete_item
     * @param type $itemID
     */
    function voting_item_delete($itemId) {
        return ModelVoting::newInstance()->deleteItem($itemId);
    }

    /**************************************************************************
     *                          VOTE USERS
     *************************************************************************/

    /**
     * Show form to vote a seller if item belongs to a registered user. (itemDetail)
     */
    function voting_item_detail_user($userId)
    {
        if( is_array($userId) ) {
            $userId = osc_item_user_id();
        } else if($userId == null && !is_numeric($userId) ) {
            exit;
        }

        if( osc_get_preference('user_voting', 'voting') == 1 && is_numeric($userId) && isset($userId) && $userId > 0) {
            // obtener el avg de las votaciones
            $aux_vote  = ModelVoting::newInstance()->getUserAvgRating($userId);
            $aux_count = ModelVoting::newInstance()->getUserNumberOfVotes($userId);
            $vote['vote']   = $aux_vote['vote'];
            $vote['total']  = $aux_count['total'];
            $vote['userId'] = $userId;

            $vote['can_vote'] = false;
            if(osc_is_web_user_logged_in() && can_vote_user($userId, osc_logged_user_id()) ) {
                $vote['can_vote'] = true;
            }
            require 'item_detail_user.php';
        }
    }

    /**
     * Check if user can vote
     *
     * @param type $userVotedId
     * @param type $userId
     * @return type
     */
    function can_vote_user($userVotedId, $userId)
    {
        $result = array();
        if( isset($userVotedId) && is_numeric($userVotedId) && isset($userId) && is_numeric($userId) && $userId != $userVotedId) {
            $result = ModelVoting::newInstance()->getUserIsRated($userVotedId, $userId);
            if( count($result) > 0 ) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * Return layout optimized for sidebar at main web page, with the best user voted with a limit
     *
     * @param int $num number of users
     */
    function echo_users_best_rated($num = 5)
    {
        if( osc_get_preference('user_voting', 'voting') == 1 ) {
            $filter = array(
                'order'       => 'desc',
                'num_items'   => $num
            );
            $results = get_user_votes($filter);
            if(count($results) > 0 ) {
                $locale  = osc_current_user_locale();
                require 'set_results_user.php';
            }
        }
    }

    /**
     * Return an array of votes with given filters
     * <code>
     * array(
     *          'order'       => ('desc','asc'),
     *          'num_items'   => (integer)
     *      );
     * </code>
     * @param type $array_filters
     * @return type
     */
    function get_user_votes($array_filters)
    {
        $order       = 'desc';
        $num         = 5;
        if(isset($array_filters['order'])){
            $order = strtolower($array_filters['order']);
            if( !in_array($order, array('desc', 'asc') ) ){
                $order = 'desc';
            }
        }
        if(isset($array_filters['num_items'])){
            $num = (int)$array_filters['num_items'];
        }

       return ModelVoting::newInstance()->getUserRatings($order, $num);
    }

    /**
     * hook delete
     * @param type $userID
     */
    function voting_user_delete($userId)
    {
        ModelVoting::newInstance()->deleteUser($userId);
    }

    /**
     * Print star img src
     *
     * @param type $star
     * @param type $avg_vote
     * @return type
     */
    function voting_star($star, $avg_vote)
    {
        $path = osc_base_url().'/oc-content/plugins/'.  osc_plugin_folder(__FILE__);
        $star_ok = $path.'img/ico_vot_ok.gif';
        $star_no = $path.'img/ico_vot_no.gif';
        $star_md = $path.'img/ico_vot_md.gif';

        if( $avg_vote >= $star) {
            echo $star_ok;
        } else {
            $aux = 1+($avg_vote - $star);
            if($aux <= 0){
                echo $star_no;
                return true;
            }
            if($aux >=1) {
                echo $star_no;
            } else {
                if($aux <= 0.5){
                    echo $star_md;
                }else{
                    echo $star_ok;
                }
            }
        }
    }

    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'voting_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'voting_admin_configuration');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'voting_uninstall');

    osc_add_hook('item_detail', 'voting_item_detail');
    osc_add_hook('item_detail', 'voting_item_detail_user');

    osc_add_hook('delete_item', 'voting_item_delete');
    osc_add_hook('delete_user', 'voting_user_delete');

     /**
     * ADMIN MENU
     */
    if(osc_version() >= 300) {
        osc_add_admin_menu_page( __('Voting options', 'voting'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'voting_plugin', 'administrator' );
        osc_add_admin_submenu_page('voting_plugin', __('Settings', 'voting'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php'), 'voting_plugin_settings', 'administrator');
        osc_add_admin_submenu_page('voting_plugin', __('Help', 'voting'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'help.php'), 'voting_plugin_help', 'administrator');
    } else {
        osc_add_hook('admin_menu', 'voting_admin_menu');
    }

    function votingmenu() { ?>
<style>
    .ico-voting_plugin {
        background-image: url('<?php echo osc_base_url();?>oc-content/plugins/<?php echo osc_plugin_folder(__FILE__);?>img/split.png') !important;
        background-position:0px -48px;
    }
    .ico-voting_plugin:hover,
    .current .ico-voting_plugin{
        background-position:0px -0px;
    }




    body.compact .ico-voting_plugin{
            background-position:-48px -48px;
    }
    body.compact .ico-voting_plugin:hover,
    body.compact .current .ico-voting_plugin{
        background-position:-48px 0px;
    }
</style>
    <?php
    }
    osc_add_hook('admin_header','votingmenu');


?>
