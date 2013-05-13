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

    class Blockchain
    {

        public function __construct()
        {
        }

        public static function loadJS() {
            echo '<script type="text/javascript" src="https://blockchain.info/Resources/wallet/pay-now-button.js"></script>';
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
            $extra = payment_prepare_custom($extra_array);
            $extra .= 'concept,'.$description.'|';
            $extra .= 'product,'.$itemnumber.'|';
            $r = rand(0,1000);
            $extra .= 'random,'.$r;
            $CALLBACK_URL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'callback.php?extra=' . $extra;
        ?>
            <div style="font-size:16px;margin:10px;width:300px;cursor:pointer;margin-left:750px;margin-top:20px" class="blockchain-btn"
            data-address="<?php echo osc_get_preference('blockchain_btc_address'); ?>"
            data-anonymous="false"
            data-callback="<?php echo $CALLBACK_URL; ?>">
                <div class="blockchain stage-begin">
                    <img src="<?php echo osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__); ?>pay_now_64.png">
                </div>
                <div class="blockchain stage-loading" style="text-align:center">
                    <img src="<?php echo osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__); ?>loading-large.gif">
                </div>
                <div class="blockchain stage-ready">
                    <p align="center"><?php printf(__('Please send %f BTC to <br /> <b>[[address]]</b></p>', 'payment'), $amount); ?>
                    <p align="center" class="qr-code"></p>
                </div>
                <div class="blockchain stage-paid">
                    <?php _e('Payment Received <b>[[value]] BTC</b>. Thank You.', 'payment'); ?>
                </div>
                <div class="blockchain stage-error">
                    <span color="red">[[error]]</span>
                </div>
            </div>
        <?php
        }


        public static function processPayment() {

            if(Params::getParam('test')==true) {
                return PAYMENT_FAILED;
            }

            $data = payment_get_custom(Params::getParam('extra'));
            $transaction_hash = Params::getParam('transaction_hash');
            $value_in_btc = Params::getParam('value') / 100000000;
            $my_bitcoin_address = osc_get_preference('blockchain_btc_address');

            if (Params::getParam('address')!=$my_bitcoin_address) {
                return PAYMENT_FAILED;
            }

            $hosts = gethostbynamel('blockchain.info');
            foreach ($hosts as $ip) {
                // Check payment came from one of blockchain.info's IP
                if ($_SERVER['REMOTE_ADDR']==$ip) {
                    $exists = ModelPayment::newInstance()->getPaymentByCode($transaction_hash, 'BLOCKCHAIN');
                    if(isset($exists['pk_i_id'])) { return PAYMENT_ALREADY_PAID; }
                    if ((is_numeric(Params::getParam('confirmations')) && Params::getParam('confirmations')>=6) || Params::getParam('anonymous')== true) {
                        $product_type = explode('x', $data['product']);
                        // SAVE TRANSACTION LOG
                        $payment_id = ModelPayment::newInstance()->saveLog(
                            $data['concept'], //concept
                            $transaction_hash, // transaction code
                            $value_in_btc, //amount
                            'BTC', //currency
                            $data['email'], // payer's email
                            $data['user'], //user
                            $data['itemid'], //item
                            $product_type[0], //product type
                            'BLOCKCHAIN'); //source

                        if ($product_type[0] == '101') {
                            ModelPayment::newInstance()->payPublishFee($product_type[2], $payment_id);
                        } else if ($product_type[0] == '201') {
                            ModelPayment::newInstance()->payPremiumFee($product_type[2], $payment_id);
                        } else {
                            ModelPayment::newInstance()->addWallet($data['user'], $value_in_btc);
                        }

                        return PAYMENT_COMPLETED;
                    } else {
                        // Maybe we could do something here (the payment was correct, but it didn't get enought confirmations yet)
                        return PAYMENT_PENDING;
                    }
                    break;
                }
            }
            return $status = PAYMENT_FAILED;
        }

    }

?>