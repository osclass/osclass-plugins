<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    class Paypal
    {

        public function __construct()
        {
        }

        public static function loadJS() {
            echo '<script src="https://www.paypalobjects.com/js/external/dg.js" type="text/javascript"></script>';
        }

        /**
        * Create and print a "Pay with Paypal" button
        *
        * @param float $amount
        * @param string $description
        * @param string $itemnumber (publish fee, premium, pack and which category)
        * @param string $extra custom variables
        */
        public static function button($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {

            if(osc_get_preference('paypal_standard', 'payment')==1) {
                Paypal::standardButton($amount, $description, $itemnumber, $extra_array);
            } else {
                Paypal::dgButton($amount, $description, $itemnumber, $extra_array);
            }
        }

        public static function dgButton($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {

            if($extra_array!=null) {
                $extra = '';
                foreach($extra_array as $k => $v) {
                    $extra .= $k.",".$v."|";
                }
            } else {
                $extra = "";
            }

            $r = rand(0,1000);
            $extra .= 'random,'.$r;

            $APIUSERNAME  = payment_decrypt(osc_get_preference('paypal_api_username', 'payment'));
            $APIPASSWORD  = payment_decrypt(osc_get_preference('paypal_api_password', 'payment'));
            $APISIGNATURE = payment_decrypt(osc_get_preference('paypal_api_signature', 'payment'));

            if(osc_get_preference('paypal_sandbox', 'payment')==1) {
                $ENDPOINT     = 'https://api-3t.sandbox.paypal.com/nvp';
            } else {
                $ENDPOINT     = 'https://api-3t.paypal.com/nvp';
            }

            $VERSION      = '65.1'; // must be >= 65.1
            $REDIRECTURL  = 'https://www.paypal.com/incontext?token=';
            if(osc_get_preference('paypal_sandbox', 'payment')==1) {
                $REDIRECTURL  = "https://www.sandbox.paypal.com/incontext?token=";
            }

            //Build the Credential String:
            $cred_str = 'USER=' . $APIUSERNAME . '&PWD=' . $APIPASSWORD . '&SIGNATURE=' . $APISIGNATURE . '&VERSION=' . $VERSION;
            //For Testing this is hardcoded. You would want to set these variable values dynamically
            $nvp_str  = "&METHOD=SetExpressCheckout"
            . '&RETURNURL=' . osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'return.php?extra=' . $extra //set your Return URL here
            . '&CANCELURL=' . osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'cancel.php?extra=' . $extra //set your Cancel URL here
            . '&PAYMENTREQUEST_0_CURRENCYCODE=' . osc_get_preference('currency', 'payment')
            . '&PAYMENTREQUEST_0_AMT=' . $amount
            . '&PAYMENTREQUEST_0_ITEMAMT=' . $amount
            . '&PAYMENTREQUEST_0_TAXAMT=0'
            . '&PAYMENTREQUEST_0_DESC=' . $description
            . '&PAYMENTREQUEST_0_PAYMENTACTION=Sale'
            . '&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital'
            . '&L_PAYMENTREQUEST_0_NAME0=' . $description
            . '&L_PAYMENTREQUEST_0_NUMBER0=' . $itemnumber
            . '&L_PAYMENTREQUEST_0_QTY0=1'
            . '&L_PAYMENTREQUEST_0_TAXAMT0=0'
            . '&L_PAYMENTREQUEST_0_AMT0=' . $amount
            . '&L_PAYMENTREQUEST_0_DESC0=Download'
            . '&CUSTOM=' . $extra
            . '&useraction=commit';

            //combine the two strings and make the API Call
            $req_str = $cred_str . $nvp_str;
            $response = Paypal::httpPost($ENDPOINT, $req_str);

            //check Response
            if($response['ACK'] == "Success" || $response['ACK'] == "SuccessWithWarning") {
                //setup redirect URL
                $redirect_url = $REDIRECTURL . urldecode($response['TOKEN']);
                ?>
                <a href="<?php echo $redirect_url; ?>" id='paypalBtn_<?php echo $r; ?>'>
                    <img src='<?php echo payment_path(); ?>payments/paypal/paypal.gif' border='0' />
                </a>
                <script>
                    var dg_<?php echo $r; ?> = new PAYPAL.apps.DGFlow({
                        trigger: "paypalBtn_<?php echo $r; ?>"
                    });
                </script><?php
            } else if($response['ACK'] == 'Failure' || $response['ACK'] == 'FailureWithWarning') {
                $redirect_url = ''; //SOMETHING FAILED
            }
        }

        public static function standardButton($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {

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

            $r = rand(0,1000);
            $extra .= 'random,'.$r;

            if(osc_get_preference('paypal_sandbox', 'payment')==1) {
                $ENDPOINT     = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $ENDPOINT     = 'https://www.paypal.com/cgi-bin/webscr';
            }

            $RETURNURL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'return.php?extra=' . $extra;
            $CANCELURL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'cancel.php?extra=' . $extra;
            $NOTIFYURL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'notify_url.php?extra=' . $extra;

            $NOTIFYURL = 'http://95.62.72.123/~conejo/osclass/OSClass/oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'notify_url.php?extra=' . $extra;

            ?>


                <form action="<?php echo $ENDPOINT; ?>" method="post" id="paypal_<?php echo $r; ?>">
                  <input type="hidden" name="cmd" value="_xclick" />
                  <input type="hidden" name="notify_url" value="<?php echo $NOTIFYURL; ?>" />
                  <input type="hidden" name="amount" value="<?php echo $amount; ?>" />
                  <input type="hidden" name="item_name" value="<?php echo $description; ?>" />
                  <input type="hidden" name="item_number" value="<?php echo $itemnumber; ?>" />
                  <input type="hidden" name="quantity" value="1" />
                  <input type="hidden" name="currency_code" value="<?php echo osc_get_preference('currency', 'payment'); ?>" />
                  <input type="hidden" name="custom" value="<?php echo $extra; ?>" />
                  <input type="hidden" name="return" value="<?php echo $RETURNURL; ?>" />
                  <input type="hidden" name="rm" value="2" />
                  <input type="hidden" name="cancel_return" value="<?php echo $CANCELURL; ?>" />
                  <input type="hidden" name="business" value="<?php echo osc_get_preference('paypal_email', 'payment'); ?>" />
                  <input type="hidden" name="upload" value="1" />
                  <input type="hidden" name="no_note" value="1" />
                  <input type="hidden" name="charset" value="utf-8" />
                </form>
                <div class="buttons">
                  <div class="right"><a id="button-confirm" class="button" onclick="$('#paypal_<?php echo $r; ?>').submit();"><span><img src='<?php echo payment_path(); ?>payments/paypal/paypal.gif' border='0' /></span></a></div>
                </div>
            <?php
        }


        public static function processPayment() {
            return Paypal::processStandardPayment();
        }


        public static function processStandardPayment() {
            if (Params::getParam('payment_status') == 'Completed' || Params::getParam('st') == 'Completed') {
                // Have we processed the payment already?
                $tx = Params::getParam('tx')==''?Params::getParam('tx'):Params::getParam('txn_id');
                $payment = ModelPayment::newInstance()->getPayment($tx);
                if (!$payment) {
                    $data = ModelPayment::getCustom(Params::getParam('custom'));
                    $product_type = explode('x', Params::getParam('item_number'));
                    // SAVE TRANSACTION LOG
                    $payment_id = ModelPayment::newInstance()->saveLog(
                                                                Params::getParam('item_name'), //concept
                                                                $tx,
                                                                Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'), //amount
                                                                Params::getParam('mc_currency'), //currency
                                                                Params::getParam('payer_email')!=''?Params::getParam('payer_email'):'', // payer's email
                                                                $data['user'], //user
                                                                $data['itemid'], //item
                                                                $product_type[0], //product type
                                                                'PAYPAL'); //source
                    if ($product_type[0] == '101') {
                        ModelPayment::newInstance()->payPublishFee($product_type[2], $payment_id);
                    } else if ($product_type[0] == '201') {
                        ModelPayment::newInstance()->payPremiumFee($product_type[2], $payment_id);
                    } else {
                        ModelPayment::newInstance()->addWallet($data['user'], Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'));
                    }
                    return PAYMENT_COMPLETED;
                }
                return PAYMENT_ALREADY_PAID;
            }
            return PAYMENT_PENDING;
        }



        public static function processDGPayment($doresponse, $response) {

            $data = ModelPayment::getCustom(Params::getParam('extra'));

            if ($doresponse['ACK'] == 'Success' || $doresponse['ACK'] == 'SuccessWithWarning') {
                $product_type = explode('x', urldecode($response['L_PAYMENTREQUEST_0_NUMBER0']));
                // SAVE TRANSACTION LOG

                $payment_id = ModelPayment::newInstance()->saveLog(
                                                            urldecode($response['L_PAYMENTREQUEST_0_NAME0']), //concept
                                                            urldecode($doresponse['PAYMENTINFO_0_TRANSACTIONID']),    // transaction code
                                                            urldecode($doresponse['PAYMENTINFO_0_AMT']), //amount
                                                            urldecode($doresponse['PAYMENTINFO_0_CURRENCYCODE']), //currency
                                                            isset($response['EMAIL']) ? urldecode($response['EMAIL']) : '', // payer's email
                                                            $data['user'], //user
                                                            $data['itemid'], //item
                                                            $product_type[0], //product type
                                                            'PAYPAL'); //source

                if ($product_type[0] == '101') {
                    ModelPayment::newInstance()->payPublishFee($product_type[2], $payment_id);
                } else if ($product_type[0] == '201') {
                    ModelPayment::newInstance()->payPremiumFee($product_type[2], $payment_id);
                } else {
                    ModelPayment::newInstance()->addWallet($data['user'], Params::getParam('mc_gross')!=''?Params::getParam('mc_gross'):Params::getParam('payment_gross'));
                }
                return PAYMENT_COMPLETED;
            } else if($doresponse['ACK'] == "Failure" || $doresponse['ACK'] == "FailureWithWarning") {
                return PAYMENT_FAILED;
            }
            return PAYMENT_PENDING;
        }



        //Makes an API call using an NVP String and an Endpoint
        public static function httpPost($my_endpoint, $my_api_str) {
            // setting the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $my_endpoint);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // turning off the server and peer verification(TrustManager Concept).
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            // setting the NVP $my_api_str as POST FIELD to curl
            curl_setopt($ch, CURLOPT_POSTFIELDS, $my_api_str);
            // getting response from server
            $httpResponse = curl_exec($ch);
            if (!$httpResponse) {
                $response = "$API_method failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')';
                return $response;
            }
            $httpResponseAr = explode("&", $httpResponse);
            $httpParsedResponseAr = array();
            foreach ($httpResponseAr as $i => $value) {
                $tmpAr = explode("=", $value);
                if (sizeof($tmpAr) > 1) {
                    $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
                }
            }

            if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
                $response = "Invalid HTTP Response for POST request($my_api_str) to $API_Endpoint.";
                return $response;
            }

            return $httpParsedResponseAr;
        }

    }

?>