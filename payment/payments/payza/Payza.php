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

    class Payza
    {

        public function __construct()
        {
        }

        public static function button($amount = '0.00', $description = '', $itemnumber = '101', $extra_array = null) {
            $extra = payment_prepare_custom($extra_array);
            $r = rand(0,1000);
            $extra .= 'random,'.$r;
            $apcs = self::customToAPC($extra);

            $RETURNURL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'return.php?extra=' . $extra;
            $CANCELURL = osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__) . 'cancel.php?extra=' . $extra;
        ?>
            <form method="post" action="https://secure.payza.com/checkout" >
                <input type="hidden" name="ap_merchant" value="seller_1_desteban@osclass.org"/>
                <input type="hidden" name="ap_purchasetype" value="service"/>
                <input type="hidden" name="ap_itemname" value="<?php echo $description; ?>"/>
                <input type="hidden" name="ap_amount" value="<?php echo $amount; ?>"/>
                <input type="hidden" name="ap_currency" value="<?php echo osc_get_preference('currency', 'payment'); ?>"/>

               <input type="hidden" name="ap_quantity" value="1"/>
                <input type="hidden" name="ap_itemcode" value="<?php echo $itemnumber; ?>"/>
                <input type="hidden" name="ap_description" value="Audio equipment"/>
                <input type="hidden" name="ap_returnurl" value="<?php echo $RETURNURL; ?>"/>
                <input type="hidden" name="ap_cancelurl" value="<?php echo $CANCELURL; ?>"/>

                <?php foreach($apcs as $k => $v) {
                    echo '<input type="hidden" name="apc_'.$k.'" value="'.$v.'"/>';
                }; ?>

                <input type="image" src="<?php echo osc_base_url() . 'oc-content/plugins/' . osc_plugin_folder(__FILE__); ?>payza-buy-now.png"/>
            </form>
        <?php
        }

        // Payza set a maximum of 100 chars in each apc_X variable
        // $extra should not be larger than 100, but added this function just in case
        public static function customToAPC($extra) {
            $apc = array();
            $min = min(6, ceil(strlen($extra)));
            for($k=0;$k<$min;$k++) {
                $apc[$k+1] = substr($extra, 100*$k, 100);
            }
            return $apc;
        }

    }

?>