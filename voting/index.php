<?php
/*
Plugin Name: Voting
Plugin URI: http://www.osclass.org/
Description: Voting system
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: voting_plugin
*/

    /**
     * Set plugin preferences 
     */
    function voting_install() 
    {
        $conn = getConnection();
        $conn->autocommit(false) ;
        try {
            $path = osc_plugin_resource('voting/struct.sql');
            $sql = file_get_contents($path);
            $conn->osc_dbImportSQL($sql);
            
            osc_set_preference('open', '1', 'voting', 'BOOLEAN');
            osc_set_preference('user', '0', 'voting', 'BOOLEAN');
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    /**
     * Delete plugin preferences 
     */
    function voting_uninstall() 
    {
        $conn = getConnection();
        $conn->autocommit(false);
        
        try {
            $conn->osc_dbExec("DELETE FROM %st_plugin_category WHERE s_plugin_name = 'voting_plugin'", DB_TABLE_PREFIX);
            $conn->osc_dbExec('DROP TABLE %st_voting_item', DB_TABLE_PREFIX);
            
            osc_delete_preference('open', 'voting');
            osc_delete_preference('user', 'voting');
            
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }
    
    /**
     * Admin panel menu
     */
    function voting_admin_menu() 
    {
        echo '<h3><a href="#">' . __('Voting options', 'voting') . '</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'voting') . '</a></li>
        </ul>';
    }
    
    function voting_item_detail()
    {
        if (osc_is_this_category('voting_plugin', osc_item_category_id())) {
            // obtener el avg de las votaciones
            $conn = getConnection();
            $aux_vote  = $conn->osc_dbFetchResult('SELECT format(avg(i_vote),1) as vote FROM %st_voting_item WHERE fk_i_item_id = %s', DB_TABLE_PREFIX, osc_item_id());
            $aux_count = $conn->osc_dbFetchResult('SELECT count(*) as total FROM %st_voting_item WHERE fk_i_item_id = %s', DB_TABLE_PREFIX, osc_item_id());
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
     * Return layout optimized for sidebar at main web page, with the best items voted with item limit
     *
     * @param int $num number of items 
     */
    function echo_voted_better($num = 5){
        $filter = array(
            'order'       => 'desc',
            'num_items'   => $num
        );
        $results = get_votes($filter);
        if(count($results) > 0 ) {
            $locale  = osc_current_user_locale();
            require 'set_results.php';
        }
    }
    
    /**
     * Return an array of votes with given filters
     * <code>
     * array(   
     * 'category_id' => (integer_category_id),
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
        $order       = null;
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
        
        $sql  = 'SELECT fk_i_item_id as item_id, format(avg(i_vote),1) as avg_vote, count(*) as num_votes, '.DB_TABLE_PREFIX.'t_item.fk_i_category_id as category_id ';
        if(!is_null($category_id)) {
            $sql .= ', '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id as parent_category_id ';
        }
        $sql .= 'FROM '.DB_TABLE_PREFIX.'t_voting_item ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_item ON '.DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_voting_item.fk_i_item_id ';
        $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_category ON '.DB_TABLE_PREFIX.'t_category.pk_i_id = '.DB_TABLE_PREFIX.'t_item.fk_i_category_id ';
        if(!is_null($category_id)) {
            $sql .= 'WHERE '.DB_TABLE_PREFIX.'t_item.fk_i_category_id = '.$category_id.' ';
            $sql .= 'OR '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id = '.$category_id.' ';
            $sql .= ' AND ';
        }else{
            $sql .= 'WHERE ';
        }
        $sql .= ''.DB_TABLE_PREFIX.'t_item.b_active = 1 ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_enabled = 1 ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_spam = 0 ';
        $sql .= 'AND ('.DB_TABLE_PREFIX.'t_item.b_premium = 1 || '.DB_TABLE_PREFIX.'t_category.i_expiration_days = 0 ||DATEDIFF(\''.date('Y-m-d H:i:s').'\','.DB_TABLE_PREFIX.'t_item.dt_pub_date) < '.DB_TABLE_PREFIX.'t_category.i_expiration_days) ';
        $sql .= 'AND '.DB_TABLE_PREFIX.'t_category.b_enabled = 1 ';
        $sql .= 'GROUP BY item_id ORDER BY avg_vote '.$order.', num_votes '.$order.' LIMIT 0, '.$num;
        
        $conn = getConnection();
        return $conn->osc_dbFetchResults($sql);
    }
    
    function voting_admin_configuration() 
    {
        // Standard configuration page for plugin which extend item's attributes
        osc_plugin_configure_view(osc_plugin_path(__FILE__));
    }
    
    /**
     * Check if item has been voted
     *
     * @param string $itemId
     * @param string $userId
     * @param string $hash
     * @return bool
     */
    function can_vote($itemId, $userId, $hash)
    {
        $conn = getConnection();
        if( $userId == 'NULL' ) {
            $result = $conn->osc_dbFetchResult("SELECT i_vote FROM %st_voting_item WHERE fk_i_item_id = %s AND fk_i_user_id IS NULL AND s_hash = '%s'", DB_TABLE_PREFIX, $itemId, $hash);
        } else {
            $result = $conn->osc_dbFetchResult("SELECT i_vote FROM %st_voting_item WHERE fk_i_item_id = %s AND fk_i_user_id = %s AND s_hash = '%s'", DB_TABLE_PREFIX, $itemId, $userId, $hash);
        }
        
        if( count($result) > 0 ) 
            return false;
        else 
            return true;
    }
    
    /**
     *
     * @param type $star
     * @param type $avg_vote
     * @return type 
     */
    function voting_star($star, $avg_vote){
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
    
    function voting_delete ($itemID) {
        $conn = getConnection();
        $conn->osc_dbExec("DELETE FROM %st_voting_item WHERE fk_i_item_id = '%d'", DB_TABLE_PREFIX, $itemID);
    }
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'voting_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'voting_admin_configuration');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'voting_uninstall');
    
    osc_add_hook('item_detail', 'voting_item_detail');
    osc_add_hook('delete_item', 'voting_delete');

    osc_add_hook('admin_menu', 'voting_admin_menu');
?>
