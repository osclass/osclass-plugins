<?php
/*
Plugin Name: Bread crumbs
Plugin URI: http://www.osclass.org/
Description: Breadcrumbs navigation system.
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: breadcrumbs
*/




function breadcrumbs() {

    // You could modify the separator
    $separator = " / ";

    $location = Rewrite::newInstance()->get_location();
    $section = Rewrite::newInstance()->get_section();
    // You DO NOT have to modify anything else
    if($location=='search') {
        if(Params::getParam('catId')!='') {
            $category = Params::getParam('catId');
        } else if(Params::getParam('category')!='') {
            $category = urldecode(Params::getParam('category'));
            $category = preg_replace('|/$|','',$category);
            $slug_categories = explode('/', $category);
            $category = $slug_categories[count($slug_categories) - 1];
        }
    } else if($location=='item' && osc_item()!=null) {
        $category = osc_item_category_id();
    }
    
    $bc_text = "<a href='".ABS_WEB_URL."' ><span class='bc_root'>".$preferences['pageTitle']."</span></a>";
    $deep_c = -1;
    if(isset($category)) {
        $cats = Category::newInstance()->toRootTree($category);
        foreach($cats as $cat) {
            $deep_c++;
            $bc_text .= $separator."<a href='".osc_createCategoryURL($cat)."' ><span class='bc_level_".$deep_c."'>".$cat['s_name']."</span></a>";
        }
    } else if($location!='index' && $location!='') {
        $bc_text .= $separator."<span class='bc_location'>".$location."</span>";
    }

    if(isset($section) && $section!='') {
        if($location=='item' && osc_item()!=null) {
            $bc_text .= $separator."<a href='".osc_createItemURL(osc_item())."' ><span class='bc_last'>".$section."</span></a>";
        } else {
            $bc_text .= $separator."<span class='bc_last'>".$section."</span>";
        }
    } else {
        $bc_text = str_replace('bc_level_'.$deep_c, 'bc_last', str_replace('bc_location', 'bc_last', $bc_text));
    }

    echo $bc_text;

}


function breadcrumbs_help() {
    osc_admin_render_plugin(dirname(__FILE__) . '/help.php') ;
}




// This is needed in order to be able to activate the plugin
osc_register_plugin(__FILE__, '');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(__FILE__."_configure", 'breadcrumbs_help');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(__FILE__."_uninstall", '');


?>
