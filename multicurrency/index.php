<?php
/*
Plugin Name: Multi currency
Plugin URI: http://www.osclass.org/
Description: Display the price of an ad in several currencies
Version: 1.0.1
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: multicurrency
Plugin update URI: multicurrency
*/


    function multicurrency_install() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $path = osc_plugin_resource(osc_plugin_folder(__FILE__).'struct.sql');
            $sql = file_get_contents($path);
            $conn->osc_dbImportSQL($sql);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
        multicurrency_get_data();
    }

    function multicurrency_uninstall() {
        $conn = getConnection();
        $conn->autocommit(false);
        try {
            $conn->osc_dbExec('DROP TABLE %st_multicurrency', DB_TABLE_PREFIX);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            echo $e->getMessage();
        }
        $conn->autocommit(true);
    }

    function multicurrency_get_data() {
        if (extension_loaded('curl')) {
            $data = array();
            $conn = getConnection();
            $currencies = $conn->osc_dbFetchResults("SELECT pk_c_code FROM %st_currency", DB_TABLE_PREFIX);
            foreach ($currencies as $from) {
                foreach ($currencies as $to) {
                    if($from['pk_c_code']!=$to['pk_c_code']) {
                        $data[] = $from['pk_c_code'].$to['pk_c_code'].'=X';
                    }
                };
            }	

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $content = curl_exec($ch);

            curl_close($ch);

            $lines = explode("\n", trim($content));

            foreach ($lines as $line) {
                if(preg_match('|([A-Z]{3})([A-Z]{3})=X",([0-9\.]+)|', $line, $m)) {
                    $conn->osc_dbExec("REPLACE INTO %st_multicurrency (s_from, s_to, f_rate, dt_date) VALUES ('%s', '%s', %f, '%s')", DB_TABLE_PREFIX, $m[1], $m[2], $m[3], date('Y-m-d H:i:s'));
                }
            }
        }
    }
    
    function multicurrency_add_prices() {
        if(osc_item_price()!=NULL && osc_item_price()!='' && osc_item_price()!=0) {
            $conn = getConnection();
            $rates = $conn->osc_dbFetchResults("SELECT * FROM %st_multicurrency WHERE s_from = '%s'", DB_TABLE_PREFIX, osc_item_currency());
            $data = array();
            foreach($rates as $r) {
                $data[] = sprintf("%0.2f %s", (osc_item_price()/1000000)*$r['f_rate'], $r['s_to']);
            }
            echo '<a class=MCtooltip href="#">'.__('Other currencies', 'multicurrency').'<span>'.implode("<br />", $data).'</span></a>';
        }
    }
    
    function multicurrency_header() {
        ?>
        <style>
            .MCtooltip {
                position: relative;
                text-decoration: none !important;
            }

            a.MCtooltip:hover {
                z-index:999;
            }

            a.MCtooltip span {
                display: none;
            }

            a.MCtooltip:hover span {
                display: block;
                position: absolute;
                top:1em; left:1em;
                width:100%;
                padding:5px;
                background-color: #ffffff;
            }
        </style>
        <?php
    }
    
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'multicurrency_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'multicurrency_uninstall');

    osc_add_hook('cron_hourly', 'multicurrency_get_data');
    osc_add_hook('header', 'multicurrency_header');
    
?>
