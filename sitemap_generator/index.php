<?php
/*
Plugin Name: Sitemap Generator
Plugin URI: http://www.osclass.org/
Description: Sitemap Generator
Version: 1.0.5
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: sitemap_generator
*/

if( !function_exists('osc_plugin_path') ) {
    function osc_plugin_path($file) {
        $file = preg_replace('|/+|','/', str_replace('\\','/',$file));
        $plugin_path = preg_replace('|/+|','/', str_replace('\\','/', PLUGINS_PATH));
        $file = $plugin_path . preg_replace('#^.*oc-content\/plugins\/#','',$file);
        return $file;
    }
}

function sitemap_generator() {

    $locales = osc_get_locales();

    $filename = osc_base_path() . 'sitemap.xml';
    @unlink($filename);
    $start_xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    file_put_contents($filename, $start_xml);
    
    // INDEX
    sitemap_add_url(osc_base_url(), date('Y-m-d'), 'always');

    // CATEGORIES 
    // TO-DO: Add language support (OSClass doesn't have it yet)
    if(osc_count_categories () > 0) {
        while ( osc_has_categories() ) {
            sitemap_add_url(osc_search_category_url(), date('Y-m-d'), 'hourly');
            if ( osc_count_subcategories() > 0 ) {
                while ( osc_has_subcategories() ) {
                    sitemap_add_url(osc_search_category_url(), date('Y-m-d'), 'hourly');
                }
            }
        }
    }
    
    // PAGES
    if(osc_count_static_pages() > 0) {
        while(osc_has_static_pages()) {
            sitemap_add_url(osc_static_page_url(), substr(osc_static_page_mod_date()!=''?osc_static_page_mod_date():osc_static_page_pub_date(), 0, 10), 'yearly');
        }
    }
    
    // ITEMS
    View::newInstance()->_exportVariableToView('items', Item::newInstance()->listLatest( 10000 ) ) ;
    if(osc_count_items() > 0) {
        while(osc_has_items()) {
            foreach($locales as $locale) {
                // Check for non-empty item's descriptions
                if(osc_item_description($locale['pk_c_code'])!='') {
                    sitemap_add_url(osc_item_url($locale['pk_c_code']), substr(osc_item_mod_date()!=''?osc_item_mod_date():osc_item_pub_date(), 0, 10), 'daily');
                }
            }
        }
    }
    
    // COUNTRIES
    $countries = Country::newInstance()->listAll();
    foreach($countries as $country) {
        sitemap_add_url(osc_search_url(array('sCountry' => $country['s_name'])), date('Y-m-d'), 'hourly');
        // REGIONS
        $regions = Region::newInstance()->getByCountry($country['pk_c_code']);
        foreach($regions as $region) {
            sitemap_add_url(osc_search_url(array('sRegion' => $region['s_name'])), date('Y-m-d'), 'hourly');
            // CITIES
            $cities = City::newInstance()->getByRegion($region['pk_i_id']);
            foreach($cities as $city) {
                sitemap_add_url(osc_search_url(array('sCity' => $city['s_name'])), date('Y-m-d'), 'hourly');
            }
        }
    }
    
    
    $end_xml = '</urlset>';
    file_put_contents($filename, $end_xml, FILE_APPEND);
    
    // PING SEARCH ENGINES
    sitemap_ping_engines();

}

function sitemap_add_url($url = '', $date = '', $freq = 'daily') {
    if( preg_match('|\?(.*)|', $url, $match) ) {
        $sub_url = $match[1];
        $param = explode('&', $sub_url);
        foreach($param as &$p) {
            list($key, $value) = explode('=', $p);
            $p = $key . '=' . urlencode($value);
        }
        $sub_url = implode('&', $param);
        $url = preg_replace('|\?.*|', '?' . $sub_url, $url);
    }

    $filename = osc_base_path() . 'sitemap.xml';
    $xml  = '    <url>' . PHP_EOL;
    $xml .= '        <loc>' . htmlentities($url, ENT_QUOTES, "UTF-8") . '</loc>' . PHP_EOL;
    $xml .= '        <lastmod>' . $date . '</lastmod>' . PHP_EOL;
    $xml .= '        <changefreq>' . $freq . '</changefreq>' . PHP_EOL;
    $xml .= '    </url>' . PHP_EOL;
    file_put_contents($filename, $xml, FILE_APPEND);
}

function sitemap_ping_engines() {
    // GOOGLE
    osc_doRequest( 'http://www.google.com/webmasters/sitemaps/ping?sitemap='.urlencode(osc_base_url() . 'sitemap.xml'), array());
    // BING
    osc_doRequest( 'http://www.bing.com/webmaster/ping.aspx?siteMap='.urlencode(osc_base_url() . 'sitemap.xml'), array());
    // YAHOO!
    osc_doRequest( 'http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid='.osc_page_title().'&url='.urlencode(osc_base_url() . 'sitemap.xml'), array());
}

function sitemap_admin_menu() {
    echo '<h3><a href="#">' . __('Sitemap Generator', 'sitemap_generator') . '</a></h3>
    <ul> 
        <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/sitemap.php') . '">&raquo; ' . __('Sitemap Help', 'sitemap_generator') . '</a></li>
        <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/generate.php') . '">&raquo; ' . __('Generate sitemap', 'sitemap_generator') . '</a></li>
    </ul>';
}

function sitemap_help() {
    sitemap_generator();
    osc_admin_render_plugin(osc_plugin_path(osc_plugin_folder(__FILE__)) . '/sitemap.php') ;
}

// This is needed in order to be able to activate the plugin
osc_register_plugin(osc_plugin_path(__FILE__), 'sitemap_help');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'sitemap_help');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');
// Add the help to the menu
osc_add_hook('admin_menu', 'sitemap_admin_menu');

// Generate sitemap every hour
osc_add_hook('cron_hourly', 'sitemap_generator');

?>