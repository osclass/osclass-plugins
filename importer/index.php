<?php
/*
Plugin Name: Ad Importer
Plugin URI: http://www.osclass.org/
Description: Import ads easily from other sources.
Version: 0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: ad_importer
Plugin update URI: ad-importer
*/

function adimporter_admin_menu() { ?>
<style type="text/css" media="screen">
    .ico-adimporter{
        background-image: url('<?php printf('%soc-content/plugins/%simg/icon.png', osc_base_url(), osc_plugin_folder(__FILE__)); ?>') !important;
    }
    body.compact .ico-adimporter{
        background-image: url('<?php printf('%soc-content/plugins/%simg/iconCompact.png', osc_base_url(), osc_plugin_folder(__FILE__)); ?>') !important;
    }
</style>
<?php
    osc_add_admin_menu_page(
        __('Ad importer', 'adimporter'),
        '#',
        'adimporter',
        'moderator'
    );

    osc_add_admin_submenu_page(
        'adimporter',
        __('Importer', 'adimporter'),
        osc_admin_render_plugin_url(osc_plugin_folder(__FILE__)."importer.php"),
        'adimporter_tool',
        'moderator'
    );
    
}


function adimporter_readxml($file) {
    
    $xml = new DOMDocument();
    $xml->load($file);//osc_plugins_path().osc_plugin_folder(__FILE__).$file);

    $listings = $xml->getElementsByTagName('listing');
    $mItems = new ItemActions(true);
    
    
    $errormsg = '';
    foreach($listings as $klisting => $listing) {
        Params::setParam("catId", @$listing->getElementsByTagName("categoryid")->item(0)->nodeValue);
        Params::setParam("country", @$listing->getElementsByTagName("country")->item(0)->nodeValue);
        Params::setParam("region", @$listing->getElementsByTagName("region")->item(0)->nodeValue);
        Params::setParam("city", @$listing->getElementsByTagName("city")->item(0)->nodeValue);
        Params::setParam("cityArea", @$listing->getElementsByTagName("cityarea")->item(0)->nodeValue);
        Params::setParam("address", @$listing->getElementsByTagName("address")->item(0)->nodeValue);
        Params::setParam("price", @$listing->getElementsByTagName("price")->item(0)->nodeValue);
        Params::setParam("currency", @$listing->getElementsByTagName("currency")->item(0)->nodeValue);
        Params::setParam("contactName", @$listing->getElementsByTagName("contactname")->item(0)->nodeValue);
        Params::setParam("contactEmail", @$listing->getElementsByTagName("contactemail")->item(0)->nodeValue);

        $title_list = $listing->getElementsByTagName("title");
        $content_list = $listing->getElementsByTagName("content");
        
        $title = array();
        $content = array();
        
        $l = $title_list->length;
        for($k = 0; $k<$l;$k++) {
            $lang = osc_locale();
            if($title_list->item($k)->hasAttributes()) {
                $attrs = $title_list->item($k)->attributes;
                foreach($attrs as $a) {
                    if($a->name=='lang') {
                        $lang = $a->value;
                        break;
                    }
                }
            }
            $title[$lang] = $title_list->item($k)->nodeValue;
        }
        
        $l = $content_list->length;
        for($k = 0; $k<$l;$k++) {
            $lang = osc_locale();
            if($content_list->item($k)->hasAttributes()) {
                $attrs = $content_list->item($k)->attributes;
                foreach($attrs as $a) {
                    if($a->name=='lang') {
                        $lang = $a->value;
                        break;
                    }
                }
            }
            $content[$lang] = $content_list->item($k)->nodeValue;
        }
        
        Params::setParam("title", $title);
        Params::setParam("description", $content);
        
        $mItems->prepareData(true);
        $success = $mItems->add();
        if($success!=2) { //2 is the success code for active ads & 1 for inactive
            $errormsg .= sprintf(__("%s (Item %d)", "adimporter"), $success, $klisting)."<br/>";
        }
    }

    if($errormsg!='') {
        osc_add_flash_error_message($errormsg, 'admin');
    } else {
        osc_add_flash_ok_message(__('All ads were imported correctly', 'adimporter'), 'admin');
    }
    
    
}



// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), '');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');


osc_add_hook('admin_header','adimporter_admin_menu');

?>
