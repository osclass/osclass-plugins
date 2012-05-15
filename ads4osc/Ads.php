<?php

    class Ads
    {
        private static $instance ;

        public static function newInstance()
        {
            if(!self::$instance instanceof self) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        public function __construct() { }

        public function create_ad($code = '')
        {
            $conn = getConnection();
            $conn->osc_dbExec(sprintf("INSERT INTO  %st_ads4osc_ads (`s_code`) VALUES ('".$code."')", DB_TABLE_PREFIX));
            $ad = $conn->osc_dbFetchResult(sprintf("SELECT pk_i_id FROM %st_ads4osc_ads ORDER BY pk_i_id DESC LIMIT 1", DB_TABLE_PREFIX));
            return $ad['pk_i_id'];
        }

        public function update_ad($ad = null)
        {
            if($ad!=null) {
                $conn = getConnection();
                $conn->osc_dbExec(sprintf("UPDATE  %st_ads4osc_ads SET  `s_network` =  '%s', `s_title` =  '%s', `s_account_id` =  '%s', `s_partner_id` =  '%s', `s_slot_id` =  '%s', `i_max_ads_per_page` =  %d, `e_ad_type` =  '%s', `f_weight` = %f, `i_ad_width` =  %d, `i_ad_height` =  %d, `s_ad_format` = '%s', `s_html_before` =  '%s', `s_code` =  '%s', `s_html_after` =  '%s' WHERE  pk_i_id = %d LIMIT 1 ;", DB_TABLE_PREFIX, $ad['s_network'], $ad['s_title'], $ad['s_account_id'], $ad['s_partner_id'], $ad['s_slot_id'], $ad['i_max_ads_per_page'], $ad['e_ad_type'], $ad['f_weight'], $ad['i_ad_width'], $ad['i_ad_height'], $ad['s_ad_format'], $ad['s_html_before'], $ad['s_code'], $ad['s_html_after'], $ad['pk_i_id']));
            }
        }

        public function get_ad_admin($id = null)
        {
            if($id!=null) {
                $conn = getConnection();
                return $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_ads4osc_ads WHERE pk_i_id = %d LIMIT 1", DB_TABLE_PREFIX, $id));
            }
            return null;
        }

        public function get_ad($title = null)
        {
            if($title!=null) {
                $conn = getConnection();
                $ads = $conn->osc_dbFetchResults(sprintf("SELECT * FROM %st_ads4osc_ads WHERE f_weight >0 AND s_title = '%s' AND b_active = 1", DB_TABLE_PREFIX, $title));
                if(isset($ads[0])) {
                    return $ads;
                }
            }
            return $this->get_default();
        }

        public function get_default()
        {
            $conn = getConnection();
            return $conn->osc_dbFetchResults(sprintf("SELECT * FROM %st_ads4osc_ads WHERE b_active = 1 LIMIT 1", DB_TABLE_PREFIX));
        }

        public function get_ads()
        {
            $conn = getConnection();
            return $conn->osc_dbFetchResults(sprintf("SELECT * FROM %st_ads4osc_ads ORDER BY pk_i_id ASC", DB_TABLE_PREFIX));
        }

        public function delete_ad($id = '')
        {
            $conn = getConnection();
            return $conn->osc_dbExec(sprintf("DELETE FROM %st_ads4osc_ads WHERE pk_i_id = %d LIMIT 1", DB_TABLE_PREFIX, $id));
        }

        public function detect_network($code = '')
        {
            if(strpos($code,'google_ad_client')!==false) {
                return 'adsense';
            } else {
                return 'html';
            }
        }

        public function increase_stats($id = 0)
        {
            if($id!=0) {
                $conn = getConnection();
                $ad = $conn->osc_dbFetchResult(sprintf("SELECT i_num_views FROM %st_ads4osc_ads WHERE pk_i_id = %d", DB_TABLE_PREFIX, $id));
                $conn->osc_dbExec(sprintf("UPDATE %st_ads4osc_ads SET i_num_views = %d WHERE pk_i_id = %d", DB_TABLE_PREFIX, ($ad['i_num_views']+1), $id));
            }
        }

        public function load_defaults($code = '')
        {
            $network = $this->detect_network($code);
            $defaults = array();
            $defaults['s_code'] = $code;
            $defaults['s_partner_id'] = '';
            $defaults['s_account_id'] = '';
            $defaults['s_slot_id'] = '';
            $defaults['i_max_ads_per_page'] = 0;
            $defaults['e_ad_type'] = 'DEFAULT';
            $defaults['i_ad_width'] = '100';
            $defaults['i_ad_height'] = '100';
            $defaults['s_ad_format'] = '100x100';
            $defaults['s_display_pages'] = '';
            $defaults['s_display_categories'] = '';
            $defaults['f_weight'] = 1;
            $defaults['s_html_before'] = '';
            $defaults['s_html_after'] = '';
            $defaults['s_network'] = $network;
            switch($network) {
                case 'adsense':
                    if(preg_match('|google_ad_client = "([^"]+)|', $code, $match)) {
                        $defaults['s_account_id'] =$match[1];
                    }
                    if(preg_match('|google_ad_slot = "([^"]+)|', $code, $match)) {
                        $defaults['s_slot_id'] =$match[1];
                    }
                    if(preg_match('|google_ad_width = ([0-9]+)|', $code, $match)) {
                            $defaults['i_ad_width'] = $match[1];
                    }
                    if(preg_match('|google_ad_height = ([0-9]+)|', $code, $match2)) {
                            $defaults['i_ad_height'] = $match2[1];
                    }
                    $conn = getConnection();
                    $ad = 1;
                    while(true) {
                        $data = $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_ads4osc_ads WHERE s_title = 'Google Adsense %d' ", DB_TABLE_PREFIX, $ad));
                        if(isset($data['pk_i_id'])) {
                            $ad++;
                        } else {
                            $defaults['s_title'] = 'Google Adsense '.$ad;
                            break;
                        }
                    }
                    break;
                default:
                    $conn = getConnection();
                    $ad = 1;
                    while(true) {
                        $data = $conn->osc_dbFetchResult(sprintf("SELECT * FROM %st_ads4osc_ads WHERE s_title = 'HTML Ad %d' ", DB_TABLE_PREFIX, $ad));
                        if(isset($data['pk_i_id'])) {
                            $ad++;
                        } else {
                            $defaults['s_title'] = 'HTML Ad '.$ad;
                            break;
                        }
                    }
                    break;
            }
            return $defaults;
        }
    }

?>