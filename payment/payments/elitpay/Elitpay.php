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

    class Elitpay
    {

        public function __construct()
        {
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

        }


        public static function processPayment() {
            return Paypal::processStandardPayment();
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
            if (Params::getParam('payment_status') == 'Completed') {
                // Have we processed the payment already?
                $payment = ModelPayment::newInstance()->getPayment(Params::getParam('txn_id'));
                if (!$payment) {
                    $data = ModelPayment::getCustom(Params::getParam('custom'));
                    $product_type = explode('x', Params::getParam('item_number'));
                    // SAVE TRANSACTION LOG
                    $payment_id = ModelPayment::newInstance()->saveLog(
                                                                Params::getParam('item_name'), //concept
                                                                Params::getParam('txn_id'),    // transaction code 
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
        
        
        /**
         * Generate random keyword
         * 
         * @return string
         */
        public static function genRandomString($length) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            $string = '';    

            for ($p = 0; $p < $length; $p++) {
                $string .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
            return $string;
        }


    }
  
?>