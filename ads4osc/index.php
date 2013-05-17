<?php
/*
Plugin Name: Ads 4 OSClass
Plugin URI: http://www.osclass.org/
Description: Manage your advertising strategy.
Version: 0.9.5
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: ads4osc
Plugin update URI: ads-4-osclass
*/

    function ads_call_after_install() {
        // Insert here the code you want to execute after the plugin's install
        // for example you might want to create a table or modify some values

        // In this case we'll create a table to store the Example attributes
        $conn = getConnection();

        $conn->autocommit(false);
        try {
            $path = osc_plugin_resource('ads4osc/struct.sql');
            $sql = file_get_contents($path);
            $conn->osc_dbImportSQL($sql);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function ads_call_after_uninstall() {
        // Insert here the code you want to execute after the plugin's uninstall
        // for example you might want to drop/remove a table or modify some values

        // In this case we'll remove the table we created to store Example attributes
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $conn->osc_dbExec('DROP TABLE %st_ads4osc_ads', DB_TABLE_PREFIX);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function ads_admin_menu() {
        echo '<h3><a href="#">' . __('Advertising', 'ads4osc') . '</a></h3>
        <ul>
            <li><a href="' . osc_admin_render_plugin_url("ads4osc/launcher.php") . '?ads-action=create">&raquo; ' . __('Create New Ad', 'ads4osc') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url("ads4osc/launcher.php") . '?ads-action=list">&raquo; ' . __('Edit Ads', 'ads4osc') . '</a></li>
            <li><a href="' . osc_admin_render_plugin_url("ads4osc/launcher.php") . '?ads-action=help">&raquo; ' . __('F.A.Q. / Help', 'ads4osc') . '</a></li>
        </ul>';
    }

    function show_ads($name = '') {
        require_once 'Ads.php';

        if($name=='') {
            $ad = Ads::newInstance()->get_default();
        } else {
            $ads = Ads::newInstance()->get_ad($name);
            if(isset($ads[0])) {
                // Sum of all weights
                $weights = 0;
                $var_l = count($ads);
                if($var_l==1) {
                    $ad = $ads[0];
                } else {
                    for($var_k=0;$var_k<$var_l;$var_k++) {
                        $weights += $ads[$var_k]['f_weight'];
                        $ads[$var_k]['weight_limit'] = $weights;
                    }
                    $random_float = $weights*(mt_rand()/mt_getrandmax());
                    foreach($ads as $_ad) {
                        if($random_float<=$_ad['weight_limit']) {
                            $ad = $_ad;
                            break;
                        }
                    }
                }
            } else {
                $ad = Ads::newInstance()->get_default();
            }
        }

        // Space for updating stats for the ads
        Ads::newInstance()->increase_stats($ad['pk_i_id']);
        // Print the ad
        echo $ad['s_html_before'];
        echo $ad['s_code'];
        echo $ad['s_html_after'];
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'ads_call_after_install');
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'ads_call_after_uninstall');

    osc_add_hook('admin_menu', 'ads_admin_menu');

?>
