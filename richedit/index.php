<?php
/*
Plugin Name: Rich edit
Plugin URI: http://www.osclass.org/
Description: Add a WYSIWYG editor when publishing an ad
Version: 1.1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: richedit
Plugin update URI: rich-edit
*/

function richedit_install()
{
    osc_set_preference('theme', 'advanced', 'richedit', 'STRING');
    osc_set_preference('skin', 'o2k7', 'richedit', 'STRING');
    osc_set_preference('width', '600px', 'richedit', 'STRING');
    osc_set_preference('height', '240px', 'richedit', 'STRING');
    osc_set_preference('skin_variant', 'silver', 'richedit', 'STRING');
    osc_set_preference('buttons1', 'bold,italic,underline,forecolor,separator,undo,redo,separator,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,link,unlink,separator,code', 'richedit', 'STRING');
    osc_set_preference('buttons2', '', 'richedit', 'STRING');
    osc_set_preference('buttons3', '', 'richedit', 'STRING');
    osc_set_preference('plugins', '', 'richedit', 'STRING');
    osc_reset_preferences();
}

function richedit_uninstall()
{
    osc_delete_preference('theme', 'richedit');
    osc_delete_preference('skin', 'richedit');
    osc_delete_preference('width', 'richedit');
    osc_delete_preference('height', 'richedit');
    osc_delete_preference('skin_variant', 'richedit');
    osc_delete_preference('buttons1', 'richedit');
    osc_delete_preference('buttons2', 'richedit');
    osc_delete_preference('buttons3', 'richedit');
    osc_delete_preference('plugins', 'richedit');
    osc_reset_preferences();
}

function richedit_load_js()
{
    $location   = Rewrite::newInstance()->get_location();
    $section    = Rewrite::newInstance()->get_section();
    if(isset($location)){
        $location = Params::getParam('page', false, false) ;
        $section  = Params::getParam('action', false, false) ;
    }

    if( ($location=='item' && ($section=='item_add' || $section=='item_edit')) ||
        ($location=='items' && ($section=='post' || $section=='item_edit'))) {
        if(osc_version()>=310) {
        ?>
        <script type="text/javascript">
            var richedit = new Array();
            richedit.theme  = "<?php echo osc_get_preference('theme', 'richedit'); ?>";
            richedit.skin   = "<?php echo osc_get_preference('skin', 'richedit'); ?>";
            richedit.width  = "<?php echo osc_get_preference('width', 'richedit'); ?>";
            richedit.height = "<?php echo osc_get_preference('height', 'richedit'); ?>";
            richedit.skin_variant = "<?php echo osc_get_preference('skin_variant', 'richedit'); ?>";
            richedit.theme_advanced_buttons1 = "<?php echo osc_get_preference('buttons1', 'richedit'); ?>";
            richedit.theme_advanced_buttons2 = "<?php echo osc_get_preference('buttons2', 'richedit'); ?>";
            richedit.theme_advanced_buttons3 = "<?php echo osc_get_preference('buttons3', 'richedit'); ?>";
            richedit.plugins =  "<?php echo osc_get_preference('plugins', 'richedit'); ?>";
        </script>
        <?php
        } else {
        ?>
        <script type="text/javascript" src="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript">
            tinyMCE.init({
                mode : "none",
                theme : "<?php echo osc_get_preference('theme', 'richedit'); ?>",
                skin: "<?php echo osc_get_preference('skin', 'richedit'); ?>",
                width: "<?php echo osc_get_preference('width', 'richedit'); ?>",
                height: "<?php echo osc_get_preference('height', 'richedit'); ?>",
                skin_variant : "<?php echo osc_get_preference('skin_variant', 'richedit'); ?>",
                theme_advanced_buttons1 : "<?php echo osc_get_preference('buttons1', 'richedit'); ?>",
                theme_advanced_buttons2 : "<?php echo osc_get_preference('buttons2', 'richedit'); ?>",
                theme_advanced_buttons3 : "<?php echo osc_get_preference('buttons3', 'richedit'); ?>",
                theme_advanced_toolbar_align : "left",
                theme_advanced_toolbar_location : "top",
                plugins : "<?php echo osc_get_preference('plugins', 'richedit'); ?>"
            });
            $(document).ready(function () {
                $("textarea[id^=description]").each(function(){
                    tinyMCE.execCommand("mceAddControl", true, this.id);
                });
            });
        </script>
        <?php
        }
    }
}

function richedit_admin_menu() {
    echo '<h3><a href="#">' . __('Rich edit options', 'richedit') . '</a></h3>
    <ul>
        <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/conf.php') . '">&raquo; ' . __('Settings', 'richedit') . '</a></li>
    </ul>';
}

function richedit_init_admin_menu()
{
    osc_add_admin_submenu_divider('plugins', 'Richedit plugin', 'moreedit_divider', 'administrator');
    osc_add_admin_submenu_page('plugins', __('Richedit options', 'richedit'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/conf.php'), 'richedit_settings', 'administrator');
}


if(!function_exists('do_not_clean_items')) {
    function do_not_clean_items($item) {
        $catID  = $item['fk_i_category_id'];
        $itemID = $item['pk_i_id'];
        
        $title       = Params::getParam('title', false, false) ;
        $description = Params::getParam('description', false, false) ;
        $locale      = osc_current_user_locale() ;

        $mItems = Item::newInstance() ;
        $mItems->updateLocaleForce($itemID, $locale, $title[$locale], $description[$locale]) ;
    }
}


/**
 * ADD HOOKS
 */
osc_register_plugin(osc_plugin_path(__FILE__), 'richedit_install');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'richedit_uninstall');

if(osc_version()<310) {
    osc_add_hook('admin_menu', 'richedit_admin_menu');
} else {
    osc_add_hook('admin_menu_init', 'richedit_init_admin_menu');
}

$location   = Rewrite::newInstance()->get_location();
$section    = Rewrite::newInstance()->get_section();
if(isset($location)){
    $location = Params::getParam('page', false, false) ;
    $section  = Params::getParam('action', false, false) ;
}

if(($location=='item' && ($section=='item_add' || $section=='item_edit')) || ($location=='items' && ($section=='post' || $section=='item_edit'))) {
    if(osc_version()>=310) {
        osc_register_script('tiny_mce', osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__).'tiny_mce/tiny_mce.js');
        osc_register_script('rich_edit_js', osc_plugin_url(__FILE__) . 'rich_edit.js', 'jquery');

        osc_enqueue_script('tiny_mce');
        osc_enqueue_script('rich_edit_js' , array('jquery', 'tiny_mce') );

        osc_add_hook('header',       'richedit_load_js', 0);
        osc_add_hook('admin_header', 'richedit_load_js', 0);
    } else {
        osc_add_hook('header',       'richedit_load_js');
        osc_add_hook('admin_header', 'richedit_load_js');
    }
}

osc_add_hook('posted_item', 'do_not_clean_items');
osc_add_hook('edited_item', 'do_not_clean_items');

?>
