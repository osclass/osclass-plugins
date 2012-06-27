<?php
/*
Plugin Name: Rich edit
Plugin URI: http://www.osclass.org/
Description: Add a WYSIWYG editor when publishing an ad
Version: 1.0.7
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: richedit
Plugin update URI: rich-edit
*/

    function richedit_install() {
        $conn = getConnection();
        osc_set_preference('theme', 'advanced', 'richedit', 'STRING');
        osc_set_preference('skin', 'o2k7', 'richedit', 'STRING');
        osc_set_preference('width', '600px', 'richedit', 'STRING');
        osc_set_preference('height', '240px', 'richedit', 'STRING');
        osc_set_preference('skin_variant', 'silver', 'richedit', 'STRING');
        osc_set_preference('buttons1', 'bold,italic,underline,forecolor,separator,undo,redo,separator,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,link,unlink,separator,code', 'richedit', 'STRING');
        osc_set_preference('buttons2', '', 'richedit', 'STRING');
        osc_set_preference('buttons3', '', 'richedit', 'STRING');
        osc_set_preference('plugins', '', 'richedit', 'STRING');
        $conn->autocommit(true);
    }

    function richedit_uninstall() {
        $conn = getConnection();
        osc_delete_preference('theme', 'richedit');
        osc_delete_preference('skin', 'richedit');
        osc_delete_preference('width', 'richedit');
        osc_delete_preference('height', 'richedit');
        osc_delete_preference('skin_variant', 'richedit');
        osc_delete_preference('buttons1', 'richedit');
        osc_delete_preference('buttons2', 'richedit');
        osc_delete_preference('buttons3', 'richedit');
        osc_delete_preference('plugins', 'richedit');
        $conn->autocommit(true);
    }

    function richedit_load_js() {
        $location = Rewrite::newInstance()->get_location();
        $section = Rewrite::newInstance()->get_section();
        if(isset($location)){
            $location = Params::getParam('page', false, false) ;
            $section = Params::getParam('action', false, false) ;
        }

        if(($location=='item' && ($section=='item_add' || $section=='item_edit')) || ($location=='items' && ($section=='post' || $section=='item_edit'))) {
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

    function richedit_admin_menu() {
        echo '<h3><a href="#">' . __('Rich edit options', 'richedit') . '</a></h3>
        <ul> 
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'conf.php') . '">&raquo; ' . __('Settings', 'richedit') . '</a></li>
        </ul>';
    }
    if(!function_exists('do_not_clean_items')) {
        function do_not_clean_items($catID, $itemID) {
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

    osc_add_hook('admin_menu', 'richedit_admin_menu');
    osc_add_hook('header', 'richedit_load_js');
    osc_add_hook('admin_header', 'richedit_load_js');

    osc_add_hook('item_form_post', 'do_not_clean_items');
    osc_add_hook('item_edit_post', 'do_not_clean_items');
?>
