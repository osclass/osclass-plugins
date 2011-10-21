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
             
            //
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
             
             require_once 'item_detail.php';
         }
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
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'voting_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'voting_admin_configuration');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'voting_uninstall');
    
    osc_add_hook('item_detail', 'voting_item_detail');

    osc_add_hook('admin_menu', 'voting_admin_menu');
?>
