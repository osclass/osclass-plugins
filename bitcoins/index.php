<?php
/*
Plugin Name: Bitcoins
Plugin URI: http://www.osclass.org/
Description: Display the price of an ad in bitcoins
Version: 1.0
Author: OSClass
Author URI: http://www.osclass.org/
Short Name: bitcoins
Plugin update URI: bitcoins
*/


    function bitcoins_install() {
        osc_set_preference("rates", '{"JPY": {"7d": "644.91", "30d": "543.95", "24h": "677.26"}, "USD": {"7d": "7.97", "30d": "7.06", "24h": "8.65"}, "AUD": {"7d": "7.89", "30d": "7.16", "24h": "8.51"}, "CHF": {"7d": "7.21", "30d": "6.43"}, "RUB": {"7d": "244.91", "30d": "244.40"}, "SGD": {"30d": "8.39"}, "THB": {"30d": "225.68"}, "CNY": {"7d": "49.87", "30d": "43.60", "24h": "54.44"}, "SLL": {"7d": "2053.24", "30d": "1843.36", "24h": "2288.82"}, "BRL": {"7d": "17.50", "30d": "16.01", "24h": "18.64"}, "timestamp": 1342600202, "GBP": {"7d": "5.11", "30d": "4.54", "24h": "5.64"}, "NZD": {"7d": "10.49", "30d": "9.64", "24h": "11.73"}, "PLN": {"7d": "26.57", "30d": "23.41", "24h": "29.08"}, "CAD": {"7d": "7.76", "30d": "6.91", "24h": "8.38"}, "SEK": {"7d": "58.53", "30d": "51.15", "24h": "65.69"}, "DKK": {"7d": "51.78", "30d": "44.95", "24h": "52.57"}, "HKD": {"7d": "65.49", "30d": "65.49", "24h": "70.05"}, "EUR": {"7d": "6.50", "30d": "5.70", "24h": "7.05"}}', "bitcoins");
        bitcoins_get_data();
    }

    function bitcoins_uninstall() {
        Preference::newInstance()->delete(array("s_section" => "bitcoins"));
    }

    function bitcoins_get_data() {
        $rates = osc_file_get_contents("http://bitcoincharts.com/t/weighted_prices.json");
        osc_set_preference("rates", $rates, "bitcoins");
    }
    
    function bitcoins_price() {
        if(osc_item_price()!=NULL && osc_item_price()!='' && osc_item_price()!=0) {
            $rates = json_decode(osc_get_preference("rates", "bitcoins"), true);
            if(isset($rates[osc_item_currency()])) {
                $btc = 0;
                if(isset($rates[osc_item_currency()]["24h"])) {
                    $btc = $rates[osc_item_currency()]["24h"];
                } else if(isset($rates[osc_item_currency()]["7d"])) {
                    $btc = $rates[osc_item_currency()]["7d"];
                } else if(isset($rates[osc_item_currency()]["30d"])) {
                    $btc = $rates[osc_item_currency()]["30d"];
                }
                if($btc!=0) {
                    $price = osc_item_price()/(1000000*$btc);

                    $currencyFormat = osc_locale_currency_format();
                    $currencyFormat = str_replace('{NUMBER}', number_format($price, osc_locale_num_dec(), osc_locale_dec_point(), osc_locale_thousands_sep()), $currencyFormat);
                    $currencyFormat = str_replace('{CURRENCY}', 'BTC', $currencyFormat);
                    echo '<span class="bitcoin_price" >( '.$currencyFormat.' )</span>';
                }
            }
        }
    }
    
    
    /**
     * ADD HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), 'bitcoins_install');
    osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'bitcoins_uninstall');

    osc_add_hook('cron_hourly', 'bitcoins_get_data');
    
    
?>
