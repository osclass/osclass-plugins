<?php
/*
Plugin Name: Sitemap Generator
Plugin URI: http://www.osclass.org/
Description: Sitemap Generator
Version: 1.2.4
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: sitemap_generator
Plugin update URI: sitemap-generator
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
    $min = 1;

    $locales = osc_get_locales();

    $filename = osc_base_path() . 'sitemap.xml';
    @unlink($filename);
    $start_xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    file_put_contents($filename, $start_xml);

    // INDEX
    sitemap_add_url(osc_base_url(), date('Y-m-d'), 'always');

    $categories = Category::newInstance()->listAll(false);
    $countries = Country::newInstance()->listAll();
    foreach($categories as $c) {
        $search = new Search();
        $search->addCategory($c['pk_i_id']);
        if($search->count()>=$min) {
            sitemap_add_url(osc_search_url(array('sCategory' => $c['s_slug'])), date('Y-m-d'), 'hourly');
            foreach($countries as $country) {
                if(count($countries)>1) {
                    $search = new Search();
                    $search->addCategory($c['pk_i_id']);
                    $search->addCountry($country['pk_c_code']);
                    if($search->count()>$min) {
                        sitemap_add_url(osc_search_url(array('sCategory' => $c['s_slug'], 'sCountry' => $country['s_name'])), date('Y-m-d'), 'hourly');
                    }
                }
                $regions = Region::newInstance()->findByCountry($country['pk_c_code']);
                foreach($regions as $region) {
                    $search = new Search();
                    $search->addCategory($c['pk_i_id']);
                    $search->addCountry($country['pk_c_code']);
                    $search->addRegion($region['pk_i_id']);
                    if($search->count()>$min) {
                        sitemap_add_url(osc_search_url(array('sCategory' => $c['s_slug'], 'sCountry' => $country['s_name'], 'sRegion' => $region['s_name'])), date('Y-m-d'), 'hourly');
                        $cities = City::newInstance()->findByRegion($region['pk_i_id']);
                        foreach($cities as $city) {
                            $search = new Search();
                            $search->addCategory($c['pk_i_id']);
                            $search->addCountry($country['pk_c_code']);
                            $search->addRegion($region['pk_i_id']);
                            $search->addCity($city['pk_i_id']);
                            if($search->count()>$min) {
                                sitemap_add_url(osc_search_url(array('sCategory' => $c['s_slug'], 'sCountry' => $country['s_name'], 'sRegion' => $region['s_name'], 'sCity' => $city['s_name'])), date('Y-m-d'), 'hourly');
                            }
                        }
                    }
                }
            }
        }
    }

    foreach($countries as $country) {
        $regions = Region::newInstance()->findByCountry($country['pk_c_code']);
        foreach($regions as $region) {
            $cities = CityStats::newInstance()->listCities($region['pk_i_id']);
            $l = min(count($cities), 30);
            for($k=0;$k<$l;$k++) {
                if($cities[$k]['items']>$min) {
                    sitemap_add_url(osc_search_url(array('sCountry' => $country['s_name'], 'sRegion' => $region['s_name'], 'sCity' => $cities[$k]['city_name'])), date('Y-m-d'), 'hourly');
                }
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
    if(osc_version()<311) {
        echo '<h3><a href="#">' . __('Sitemap Generator', 'sitemap_generator') . '</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/sitemap.php') . '">&raquo; ' . __('Sitemap Help', 'sitemap_generator') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/generate.php') . '">&raquo; ' . __('Generate sitemap', 'sitemap_generator') . '</a></li>
        </ul>';
    } else {
        osc_add_admin_submenu_page('plugins', __('Sitemap Help', 'sitemap_generator'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/sitemap.php'), 'sitemap_help', 'administrator');
        osc_add_admin_submenu_page('plugins', __('Generate sitemap', 'sitemap_generator'), osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . '/generate.php'), 'sitemap_generate', 'administrator');
    }
}

function sitemap_help() {
    sitemap_generator();
    osc_admin_render_plugin(osc_plugin_path(osc_plugin_folder(__FILE__)) . '/sitemap.php') ;
}

// This is needed in order to be able to activate the plugin
// osc_register_plugin(osc_plugin_path(__FILE__), 'sitemap_help');
// This is a hack to show a Configure link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'sitemap_help');
// This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');
// Add the help to the menu
if(osc_version()<311) {
osc_add_hook('admin_menu', 'sitemap_admin_menu');
} else {
    osc_add_hook('admin_menu_init', 'sitemap_admin_menu');
}

// Generate sitemap every day
// CHANGE THIS LINE TO  'cron_hourly' or 'cron_daily' to modify the frequent of running it
// REMOVE IT if you want to generate the sitemap manually
osc_add_hook('cron_weekly', 'sitemap_generator');

/* end of file */
