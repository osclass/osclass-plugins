<?php
    /*
     * functions.php
     */

    function payment_path() {
        return osc_base_path() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__);
    }

    function payment_url() {
        return osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__);
    }

    function payment_crypt($string)
    {
        $cypher = MCRYPT_RIJNDAEL_256;
        $mode = MCRYPT_MODE_ECB;
        return base64_encode(mcrypt_encrypt($cypher, PAYMENT_CRYPT_KEY, $string, $mode,
            mcrypt_create_iv(mcrypt_get_iv_size($cypher, $mode), MCRYPT_RAND)
            ));
    }

    function payment_decrypt($string)
    {
        $cypher = MCRYPT_RIJNDAEL_256;
        $mode = MCRYPT_MODE_ECB;
        return str_replace("\0", "", mcrypt_decrypt($cypher, PAYMENT_CRYPT_KEY,  base64_decode($string), $mode,
            mcrypt_create_iv(mcrypt_get_iv_size($cypher, $mode), MCRYPT_RAND)
            ));
    }

    function payment_format_btc($btc, $symbol = "BTC") {
        if($btc<0.00001) {
            return ($btc*1000000).'µ'.$symbol;
        } else if($btc<0.01) {
            return ($btc*1000).'m'.$symbol;
        }
        return $btc.$symbol;
    }

    function payment_prepare_custom($extra_array = null) {
        if($extra_array!=null) {
            if(is_array($extra_array)) {
                $extra = '';
                foreach($extra_array as $k => $v) {
                    $extra .= $k.",".$v."|";
                }
            } else {
                $extra = $extra_array;
            }
        } else {
            $extra = "";
        }
        return $extra;
    }

    function payment_get_custom($custom) {
        $tmp = array();
        if(preg_match_all('@\|?([^,]+),([^\|]*)@', $custom, $m)){
            $l = count($m[1]);
            for($k=0;$k<$l;$k++) {
                $tmp[$m[1][$k]] = $m[2][$k];
            }
        }
        return $tmp;
    }

    /**
     * Create and print a "Wallet" button
     *
     * @param float $amount
     * @param string $description
     * @param string $rpl custom variables
     * @param string $itemnumber (publish fee, premium, pack and which category)
     */
    function wallet_button($amount = '0.00', $description = '', $product = '101', $extra = '||') {
        echo '<a href="'.osc_route_url('payment-wallet', array('a' => $amount, 'desc' => $description, 'extra' => implode("|", $extra), 'product' => $product)).'"><button>'.__("Pay with your credit", "payment").'</button></a>';
    }

    /**
     * Redirect to function via JS
     *
     * @param string $url
     */
    function payment_js_redirect_to($url) { ?>
        <script type="text/javascript">
            window.top.location.href = "<?php echo $url; ?>";
        </script>
    <?php }

    /**
     * Send email to un-registered users with payment options
     *
     * @param integer $item
     * @param float $category_fee
     */
    function payment_send_email($item, $category_fee) {

        if(osc_is_web_user_logged_in()) {
            return false;
        }

        $mPages = new Page() ;
        $aPage = $mPages->findByInternalName('email_payment') ;
        $locale = osc_current_user_locale() ;
        $content = array();
        if(isset($aPage['locale'][$locale]['s_title'])) {
            $content = $aPage['locale'][$locale];
        } else {
            $content = current($aPage['locale']);
        }

        $item_url    = osc_item_url( ) ;
        $item_url    = '<a href="' . $item_url . '" >' . $item_url . '</a>';
        $publish_url = osc_route_url('payment-publish', array('itemId' => $item['pk_i_id']));
        $premium_url = osc_route_url('payment-premium', array('itemId' => $item['pk_i_id']));

        $words   = array();
        $words[] = array('{ITEM_ID}', '{CONTACT_NAME}', '{CONTACT_EMAIL}', '{WEB_URL}', '{ITEM_TITLE}',
            '{ITEM_URL}', '{WEB_TITLE}', '{PUBLISH_LINK}', '{PUBLISH_URL}', '{PREMIUM_LINK}', '{PREMIUM_URL}',
            '{START_PUBLISH_FEE}', '{END_PUBLISH_FEE}', '{START_PREMIUM_FEE}', '{END_PREMIUM_FEE}');
        $words[] = array($item['pk_i_id'], $item['s_contact_name'], $item['s_contact_email'], osc_base_url(), $item['s_title'],
            $item_url, osc_page_title(), '<a href="' . $publish_url . '">' . $publish_url . '</a>', $publish_url, '<a href="' . $premium_url . '">' . $premium_url . '</a>', $premium_url, '', '', '', '') ;

        if($category_fee==0) {
            $content['s_text'] = preg_replace('|{START_PUBLISH_FEE}(.*){END_PUBLISH_FEE}|', '', $content['s_text']);
        }

        $premium_fee = ModelPayment::newInstance()->getPremiumPrice($item['fk_i_category_id']);

        if($premium_fee==0) {
            $content['s_text'] = preg_replace('|{START_PREMIUM_FEE}(.*){END_PREMIUM_FEE}|', '', $content['s_text']);
        }

        $title = osc_mailBeauty($content['s_title'], $words) ;
        $body  = osc_mailBeauty($content['s_text'], $words) ;

        $emailParams =  array('subject'  => $title
        ,'to'       => $item['s_contact_email']
        ,'to_name'  => $item['s_contact_name']
        ,'body'     => $body
        ,'alt_body' => $body);

        osc_sendMail($emailParams);
    }


?>