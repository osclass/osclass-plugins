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

    class Amazon
    {

        public function __construct()
        {
        }


        /**
        * Create and print a "Pay with amazon" button
        * 
        * @param float $amount
        * @param string $description
        * @param string $itemnumber (publish fee, premium, pack and which category)
        * @param string $extra custom variables
        */
        public static function button($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {

            //if(osc_get_preference('amazon_standard', 'payment')==1) {
                Amazon::standardButton($amount, $description, $itemnumber, $extra_array);
            /*} else {
                Amazon::FPSButton($amount, $description, $itemnumber, $extra_array);
            }*/
        }

        public static function standardButton($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {

            require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'ButtonGenerator.php';
            require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'SignatureUtils.php';

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

            if(osc_get_preference('amazon_sandbox', 'payment')==1) {
                $ENDPOINT     = 'https://authorize.payments-sandbox.amazon.com/pba/paypipeline';
            } else {
                $ENDPOINT     = 'https://authorize.payments.amazon.com/pba/paypipeline';
            }

            $RETURNURL = str_replace("localhost", "95.62.72.123", osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'return.php');//?extra=' . $extra);
            $CANCELURL = str_replace("localhost", "95.62.72.123", osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'cancel.php?extra=' . $extra);
            $NOTIFYURL = str_replace("localhost", "95.62.72.123", osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'notify_url.php?extra=' . $extra);

            ButtonGenerator::GenerateForm(payment_decrypt(osc_get_preference('amazon_access_key', 'payment')), payment_decrypt(osc_get_preference('amazon_secret', 'payment')), $amount, $description, $itemnumber, 0, $RETURNURL, $CANCELURL, 1, $NOTIFYURL, null, "HmacSHA256", osc_get_preference('amazon_sandbox', 'payment')==1?"sandbox":"prod");
        }


        public static function getCustom($custom) {
          $tmp = array();
          if(preg_match_all('@\|?([^,]+),([^\|]*)@', $custom, $m)){
            $l = count($m[1]);
            for($k=0;$k<$l;$k++) {
                $tmp[$m[1][$k]] = $m[2][$k];
            }
          }
          return $tmp;
        }

        public static function processStandardPayment() {
            require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'SignatureUtilsForOutbound.php';

            
            $utils = new Amazon_FPS_SignatureUtilsForOutbound(payment_decrypt(trim(osc_get_preference("amazon_access_key", "payment"))), payment_decrypt(trim(osc_get_preference("amazon_secret", "payment"))));

            
           /* $params["transactionId"] = "14GPH3CZ83RPQ1ZH6J2G85NL1IO3KO8641R"; 
	$params["transactionDate"] = "1254987247"; 
	$params["status"] = "PS"; 
	$params["signatureMethod"] = "RSA-SHA1"; 
	$params["signatureVersion"] = "2"; 
	$params["buyerEmail"] = "test-sender@amazon.com"; 
	$params["recipientEmail"] = "test-recipient@amazon.com"; 
	$params["operation"] = "pay"; 
	$params["transactionAmount"] = "USD 1.100000"; 
	$params["referenceId"] = "test-reference123"; 
	$params["buyerName"] = "test sender"; 
	$params["recipientName"] = "Test Business"; 
	$params["paymentMethod"] = "CC"; 
	$params["paymentReason"] = "Test Widget"; 
	$params["certificateUrl"] = "https://fps.sandbox.amazonaws.com/certs/090909/PKICert.pem"; 
	$params["signature"] ="g2tEn6VVu8VKsxnkWeCPn8M9HABkzkVGbYTozSSKg9Y7B5Xsvq5GSoXkDlaz+izQM56wzvgFCou79un06KI6CeE4lf0SSsonoPInqvTrKoS/XPZqBChtdfciCqSyWBpPZ2YaEbSYEZdk1YZW0W7oeezgQqgzBL/CLN9U128GyFllt3/Yxr6p+XBltBUjh0kGmdAFVuFgwYq7h7cyMwAyseIRU7vDW5qsTreAPBmae9h3v4oZly5CyNDP+4HhExyzakf2r+UBEqj9EwZtek3k9qj956dlG8Dd3QeEF9AqjLp0D+7MyZr0rupNcWNbO1wGX8aEda/FvoWMRxXB3sU9dw=="; 

        $urlEndPoint = "http://yourwebsite.com/ipn.jsp"; //Your url end point receiving the ipn.
            */
            //Parameters present in return url.
            //$params = Params::getParamsAsArray();
            $params["transactionId"] = Params::getParam("transactionId");
            $params["transactionDate"] = Params::getParam("transactionDate");
            $params["status"] = Params::getParam("status");
            $params["signatureMethod"] = Params::getParam("signatureMethod");
            $params["signatureVersion"] = Params::getParam("signatureVersion");
            $params["buyerEmail"] = Params::getParam("buyerEmail");
            $params["recipientEmail"] = Params::getParam("recipientEmail");
            $params["operation"] = Params::getParam("operation");
            $params["transactionAmount"] = Params::getParam("transactionAmount");
            $params["referenceId"] = Params::getParam("referenceId");
            $params["buyerName"] = Params::getParam("buyerName");
            $params["recipientName"] = Params::getParam("recipientName");
            $params["paymentMethod"] = Params::getParam("paymentMethod");
            $params["paymentReason"] = Params::getParam("paymentReason");
            $params["certificateUrl"] = Params::getParam("certificateUrl");
            $params["signature"] = Params::getParam("signature");
            
            //print_r($params);

            $urlEndPoint = str_replace("localhost", "95.62.72.123", osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'return.php');//?extra=' . Params::getParam("extra")); 
/*            
            
            
            
            
            print "Verifying return url signed using signature v2 ....\n";
            //return url is sent as a http GET request and hence we specify GET as the http method.
            //Signature verification does not require your secret key
            print "Is signature correct: " . $utils->validateRequest($params, $urlEndPoint, "GET") . "\n";*/
            
            /*
 $params["expiry"] = "10/2013";
        $params["tokenID"] = "Q5IG5ETFCEBU8KBLTI4JHINQVL6VAJVHICBRR49AKLPIEZH1KB1S8C7VHAJJMLJ3";
        $params["status"] = "SC";
        $params["callerReference"] = "1253247023946cMcrTRrjtLjNrZGNKchWfDtUEIGuJfiOBAAJYPjbytBV";
        $params["signature"] = "IBUljqQYfKe4bdZU8YlCtcHmRBA=";
        

        // New parameters sent in return url signed using signature v2
        $params["signatureMethod"] = "RSA-SHA1";
        $params["signatureVersion"] = "2";
        $params["certificateUrl"] = "https://fps.amazonaws.com/certs/090909/PKICert.pem";
        $params["signature"] = "H4NTAsp3YwAEiyQ86j5B53lksv2hwwEaEFxtdWFpy9xX764AZy/Dm0RLEykUUyPVLgqCOlMopay5"
                . "Qxr/VDwhdYAzgQzA8VCV8x9Mn0caKsJT2HCU6tSLNa6bLwzg/ildCm2lHDho1Xt2yaBHMt+/Cn4q"
                . "I5B+6PDrb8csuAWxW/mbUhk7AzazZMfQciJNjS5k+INlcvOOtQqoA/gVeBLsXK5jNsTh09cNa7pb"
                . "gAvey+0DEjYnIRX+beJV6EMCPZxnXDGo0fA1PENLWXIHtAoIJAfLYEkVbT2lva2tZ0KBBWENnSjf"
                . "26lMZVokypIo4huoGaZMp1IVkImFi3qC6ipCrw==";
        
        
        $urlEndPoint = "http://www.mysite.com/call_pay.jsp"; //Your return url end point. 
        */
        print "Verifying return url signed using signature v2 ....\n";
        //return url is sent as a http GET request and hence we specify GET as the http method.
        //Signature verification does not require your secret key
        print "Is signature correct: " . $utils->validateRequest($params, $urlEndPoint, "GET") . "\n";            
            
            
            
            
        }
        
        
        

    }
  
?>