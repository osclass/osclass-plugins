<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');

    require_once 'Ads.php';
    $ads_action = Params::getParam('ads-action');

    switch($ads_action) {
        case 'import':
            $ads = new Ads;
            $ad = $ads->load_defaults(Params::getParam('ads-code', false, false));
            $ad['pk_i_id'] = $ads->create_ad(Params::getParam('ads-code', false, false));
            $ads->update_ad($ad);
            require 'edit.php';
            break;
        case 'save-settings':
            $ads = new Ads;
            $ad = array();
            $ad['pk_i_id'] = Params::getParam('ads-id');
            $ad['s_title'] = Params::getParam('ads-title');
            $ad['s_network'] = Params::getParam('ads-network');
            $ad['s_account_id'] = Params::getParam('ads-account-id');
            $ad['s_partner_id'] = Params::getParam('ads-partner');
            $ad['s_slot_id'] = Params::getParam('ads-slot');
            $ad['i_max_ads_per_page'] = Params::getParam('ads-counter');
            $ad['e_ad_type'] = Params::getParam('ads-adtype');
            $ad['i_ad_width'] = Params::getParam('ads-width');
            $ad['i_ad_height'] = Params::getParam('ads-height');
            if($ad['e_ad_type']=='ALL') {
                $ad['s_ad_format'] = Params::getParam('ads-format-all');
            } else if($ad['e_ad_type']=='TEXT') {
                $ad['s_ad_format'] = Params::getParam('ads-format-text');
            } else if($ad['e_ad_type']=='LINKS') {
                $ad['s_ad_format'] = Params::getParam('ads-format-link');
            } else if($ad['e_ad_type']=='IMAGES') {
                $ad['s_ad_format'] = Params::getParam('ads-format-image');
            } else if($ad['e_ad_type']=='VIDEO') {
                $ad['s_ad_format'] = Params::getParam('ads-format-video');
            } else {
                $ad['s_ad_format'] = $ad['i_ad_width'].'x'.$ad['i_ad_height'];
            }
            $ad['f_weight'] = Params::getParam('ads-weight');
            $pages = Params::getParam('ads-show-pagetype[]');
            if(is_array($pages)) {
                $ad['s_display_pages'] = implode(",", $pages);
            } else {
                $ad['s_display_pages'] = '';
            }
            $categories = Params::getParam('ads-show-category[]');
            if(is_array($categories)) {
                $ad['s_display_categories'] = implode(",", $categories);
            } else {
                $ad['s_display_categories'] = '';
            }
            $ad['s_html_before'] = Params::getParam('ads-html-before');
            $ad['s_code'] = Params::getParam('ads-code', false, false);
            $ad['s_html_after'] = Params::getParam('ads-html-after');

            if($ad['s_network']=='adsense') {
                $ad['s_code'] = preg_replace('|google_ad_client = "([^"]+)|', 'google_ad_client = "'.$ad['s_account_id'], $ad['s_code']);
                $ad['s_code'] = preg_replace('|google_ad_slot = "([^"]+)|', 'google_ad_slot = "'.$ad['s_slot_id'], $ad['s_code']);
                $ad['s_code'] = preg_replace('|google_ad_width = ([0-9]+)|', 'google_ad_width = '.$ad['i_ad_width'], $ad['s_code']);
                $ad['s_code'] = preg_replace('|google_ad_height = ([0-9]+)|', 'google_ad_height = '.$ad['i_ad_height'], $ad['s_code']);
            }
            
            $ads->update_ad($ad);
            osc_add_flash_message(__('Settings saved', 'ads4osc'), 'admin');
            // HACK TO DO A REDIRECT
            echo "<script>location.href='".osc_admin_render_plugin_url("ads4osc/launcher.php")."?ads-action=list'</script>";
            exit;
            break;
        case 'edit':
            $ad = Ads::newInstance()->get_ad_admin(Params::getParam('ads-id'));
            require 'edit.php';
            break;
        case 'list':
            $ads = Ads::newInstance()->get_ads();
            require 'list.php';
            break;
        case 'delete':
            $return = Ads::newInstance()->delete_ad(Params::getParam('ads-id'));
            if($return) {
                osc_add_flash_message(__('Ad deleted correctly', 'ads4osc'), 'admin');
            } else {
                osc_add_flash_message(__('There was a problem deleting the ad', 'ads4osc'), 'admin');
            }
            // HACK TO DO A REDIRECT
            echo "<script>location.href='".osc_admin_render_plugin_url("ads4osc/launcher.php")."?ads-action=list'</script>";
            exit;
            break;
        case 'help':
            require 'help.php';
            break;
        default:
            require 'create.php';
    }

?>
