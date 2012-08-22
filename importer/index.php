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

function adimporter_admin_menu() {

    osc_add_admin_submenu_page(
        'plugins',
        __('Ad Importer', 'adimporter'),
        osc_admin_render_plugin_url(osc_plugin_folder(__FILE__)."importer.php"),
        'importer',
        'moderator'
    );
    
}


function adimporter_readxml($file) {
    
    $xml = new DOMDocument();
    $xml->load($file);

    $listings = $xml->getElementsByTagName('listing');
    $mItems = new ItemActions(true);
    
    
    $errormsg = '';
    foreach($listings as $klisting => $listing) {
        $catId = @$listing->getElementsByTagName("catehgoryid")->item(0)->nodeValue;

        Params::setParam("country", @$listing->getElementsByTagName("country")->item(0)->nodeValue);
        Params::setParam("region", @$listing->getElementsByTagName("region")->item(0)->nodeValue);
        Params::setParam("city", @$listing->getElementsByTagName("city")->item(0)->nodeValue);
        Params::setParam("cityArea", @$listing->getElementsByTagName("cityarea")->item(0)->nodeValue);
        Params::setParam("address", @$listing->getElementsByTagName("address")->item(0)->nodeValue);
        Params::setParam("price", @$listing->getElementsByTagName("price")->item(0)->nodeValue);
        Params::setParam("currency", @$listing->getElementsByTagName("currency")->item(0)->nodeValue);
        Params::setParam("contactName", @$listing->getElementsByTagName("contactname")->item(0)->nodeValue);
        Params::setParam("contactEmail", @$listing->getElementsByTagName("contactemail")->item(0)->nodeValue);

        if($catId==null) {
            $cats = $listing->getElementsByTagName("category");
            $cat_insert = true;
            $catId = 0;
            if($cats->length>0) {
                foreach($cats as $cat) {
                    $lang = osc_language();
                    if($cat->hasAttributes()) {
                        $attrs = $cat->attributes;
                        foreach($attrs as $a) {
                            if($a->name=='lang') {
                                $lang = $a->value;
                                break;
                            }
                        }
                        $categoryDescription[$lang] = array('s_name' => $cat->nodeValue);
                        if($catId==0) {
                            $exists = Category::newInstance()->listWhere("b.fk_c_locale_code = '".$lang."' AND b.s_name = '".$cat->nodeValue."'");
                            if(isset($exists[0]) && isset($exists[0]['pk_i_id'])) {
                                $cat_insert = false;
                                $catId = $exists[0]['pk_i_id'];
                                break;
                            }
                        }
                    }
                }
                $category = array();
                $category['fk_i_parent_id'] = NULL;
                $category['i_expiration_days'] = 0;
                $category['i_position'] = 0;
                $category['b_enabled'] = 1;
                if($cat_insert) {
                    $catId = Category::newInstance()->insert($category, $categoryDescription);
                }

            }
        }
        Params::setParam("catId", $catId);
        

        $title_list = $listing->getElementsByTagName("title");
        $content_list = $listing->getElementsByTagName("content");
        $image_list = $listing->getElementsByTagName("image");
        
        $title = array();
        $content = array();
        $photos = '';
        
        $l = $title_list->length;
        for($k = 0; $k<$l;$k++) {
            $lang = osc_language();
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
            $lang = osc_language();
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
        
        /*
        foreach($image_list as $image) {
            $tmp_name = "adimporterimage_".time();
            $image_ok = osc_downloadFile($image->nodeValue, $tmp_name);
            if($image_ok) {
                $photos['error'][] = 0;
                $photos['size'][] = 100;
                $photos['type'][] = 'image/jpeg';
                $photos['tmp_name'][] = osc_content_path()."downloads/".$tmp_name;
            }
        }

        $_FILES['photos'] = $photos;
        */
        Params::setParam("title", $title);
        Params::setParam("description", $content);
        
        $mItems->prepareData(true);
        $success = $mItems->add();
        if($success!=2) { //2 is the success code for active ads & 1 for inactive
            $errormsg .= sprintf(__("%s (Item %d)", "adimporter"), $success, $klisting)."<br/>";
        }

        $delete_images = glob(osc_content_path()."downloads/adimporterimage_*");
        foreach($delete_images as $img) {
            @unlink($img);
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
