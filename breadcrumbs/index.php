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
        $category = osc_search_category_id();
        if(count($category)==1) {
            $category = $category[0];
        }
    } else if($location=='item' && osc_item()!=null) {
        $category = osc_item_category_id();
    }
    
    $bc_text = "<a href='".osc_base_url()."' ><span class='bc_root'>".osc_page_title()."</span></a>";
    $deep_c = -1;
    if(isset($category)) {
        $cats = Category::newInstance()->toRootTree($category);
        foreach($cats as $cat) {
            $deep_c++;
            $bc_text .= $separator."<a href='".breadcrumbs_category_url($cat['pk_i_id'])."' ><span class='bc_level_".$deep_c."'>".$cat['s_name']."</span></a>";
        }
    } else if($location!='index' && $location!='') {
        $bc_text .= $separator."<span class='bc_location'>".$location."</span>";
    }

    if(isset($section) && $section!='') {
        if($location=='item' && osc_item()!=null) {
            $bc_text .= $separator."<a href='".osc_item_url()."' ><span class='bc_last'>".$section."</span></a>";
        } else {
            $bc_text .= $separator."<span class='bc_last'>".$section."</span>";
        }
    } else {
        $bc_text = str_replace('bc_level_'.$deep_c, 'bc_last', str_replace('bc_location', 'bc_last', $bc_text));
    }

    echo $bc_text;

}


function breadcrumbs_category_url($category_id) {
    $path = '' ;
    if ( osc_rewrite_enabled() ) {
        if ($category_id != '') {
            $category = Category::newInstance()->hierarchy($category_id) ;
            $sanitized_category = "" ;
            for ($i = count($category); $i > 0; $i--) {
                $sanitized_category .= $category[$i - 1]['s_slug'] . '/' ;
            }
            $path = osc_base_url() . $sanitized_category ;
        }
    } else {
        $path = sprintf( osc_base_url(true) . '?page=search&sCategory=%d', $category['pk_i_id'] ) ;
    }
    return $path ;
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
