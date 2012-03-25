<?php
/*
Plugin Name: Super ToolBar
Plugin URI: http://www.osclass.org/
Description: Add a toolbar to ads from user
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: supertoolbar
*/

require_once "SuperToolBar.php";

function supertoolbar_show() {

    $toolbar = SuperToolBar::newInstance();
    if(osc_is_web_user_logged_in()) {
        $toolbar->addOption('<a href="'.osc_user_profile_url().'" />'.osc_logged_user_name().'</a>');
        $toolbar->addOption('<a href="'.osc_user_list_items_url().'" />'.__("Your items", "superuser").'</a>');
        $toolbar->addOption('<a href="'.osc_user_alerts_url().'" />'.__("Your alerts", "superuser").'</a>');
        
        
        if(Rewrite::newInstance()->get_location()=='item') {
            if(osc_item_user_id()==  osc_logged_user_id()) {
                $toolbar->addOption('<a href="'.osc_item_edit_url().'" />'.__("Edit this item", "superuser").'</a>');
                $toolbar->addOption('<a onclick="javascript:return confirm(\''.__('This action can not be undone. Are you sure you want to continue?', 'superuser').'\')" href="'.osc_item_delete_url().'" />'.__("Delete this item", "superuser").'</a>');

                if(osc_item_is_inactive()) {
                    $toolbar->addOption('<a href="'.osc_item_activate_url().'" />'.__("Activate this item", "superuser").'</a>');
                }
            }
        }
    }
    osc_run_hook("watchlist_user_menu");
    if(osc_is_web_user_logged_in()) {
        $toolbar->addOption('<a href="' . osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '" >' . __('Your watchlist', 'watchlist') . '</a>');
    }
    osc_run_hook("offer_user_menu");
    if(osc_is_web_user_logged_in()) {
        $toolbar->addOption('<a href="' . osc_render_file_url(osc_plugin_folder(__FILE__) . 'offer_byItem.php') . '" >' . __('Offers', 'offer_button') . '</a>');
		 $toolbar->addOption('<a href="' . osc_render_file_url(osc_plugin_folder(__FILE__) . 'offer_button.php') . '" >' . __('Submitted Offers', 'offer_button') . '</a>');
   
    osc_run_hook("supertoolbar_hook");
    if(osc_is_web_user_logged_in()) {
        $toolbar->addOption('<a href="'.osc_user_logout_url().'" />'.__("Logout", "superuser").'</a>');
    }
    
    $toolbar_opts = $toolbar->getOptions();
    if(!empty($toolbar_opts)) {
?>
    
    <link href="<?php echo osc_base_url()."oc-content/plugins/".osc_plugin_folder(__FILE__)."style.css"; ?>" rel="stylesheet" type="text/css" />
    <div id="supertoolbar_toolbar" name="supertoolbar_toolbar">
        <?php echo implode(" | ", $toolbar_opts);?>
    </div>
<?php
    }
}
function supertoolbar_admin_menu() {
        echo '<h3><a href="#">Super Toolbar</a></h3>
    	<ul>
    		
      	  <li><a href="'.osc_admin_render_plugin_url("supertoolbar/help.php").'?section=types">&raquo; ' . __('F.A.Q. / Help', 'superuser') . '</a></li>
    	</ul>';
	}
	
	
	
	
	
	function supertoolbar_help() {
        osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php') ;
   }

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), '');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'supertoolbar_help');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');

// Admin menu
osc_add_hook('admin_menu', 'supertoolbar_admin_menu');
?>
