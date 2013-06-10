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
?>
<?php

    $mp = ModelPayment::newInstance();

    if(Params::getParam('plugin_action') == 'done') {
        $pub_prices = Params::getParam("pub_prices");
        $pr_prices  = Params::getParam("pr_prices");
        foreach($pr_prices as $k => $v) {
            $mp->insertPrice($k, $pub_prices[$k]==''?'NULL':$pub_prices[$k], $v==''?'NULL':$v);
        }
    }

    $categories = Category::newInstance()->toTreeAll();
    $prices     = ModelPayment::newInstance()->getCategoriesPrices();
    $cat_prices = array();
    foreach($prices as $p) {
        $cat_prices[$p['fk_i_category_id']]['f_publish_cost'] = $p['f_publish_cost'];
        $cat_prices[$p['fk_i_category_id']]['f_premium_cost'] = $p['f_premium_cost'];
    }


    function drawCategories($categories, $depth = 0) {
        foreach($categories as $c) { ?>
            <tr>
                <td>
                    <?php for($d=0;$d<$depth;$d++) { echo "&nbsp;&nbsp;"; }; echo $c['s_name']; ?>
                </td>
                <td>
                    <input style="width:150px;text-align:right;" type="text" name="pub_prices[<?php echo $c['pk_i_id']?>]" id="pub_prices[<?php echo $c['pk_i_id']?>]" value="<?php echo isset($cat_prices[$c['pk_i_id']]) ? $cat_prices[$c['pk_i_id']]['f_publish_cost'] : ''; ?>" />
                </td>
                <td>
                    <input style="width:150px;text-align:right;" type="text" name="pr_prices[<?php echo $c['pk_i_id']?>]" id="pr_prices[<?php echo $c['pk_i_id']?>]" value="<?php echo isset($cat_prices[$c['pk_i_id']]) ? $cat_prices[$c['pk_i_id']]['f_premium_cost'] : ''; ?>" />
                </td>
            </tr>
        <?php drawCategories($c['categories'], $depth+1);
        }
    };


?>
    <div style="padding: 20px;">
        <div style="float: left; width: 100%;">
            <fieldset>
                <legend><?php _e('Paypal Options', 'payment'); ?></legend>
                <div style="float: left; width: 100%;">
                    <form name="payment_form" id="payment_form" action="<?php echo osc_admin_base_url(true);?>" method="POST" enctype="multipart/form-data" >
                        <input type="hidden" name="page" value="plugins" />
                        <input type="hidden" name="action" value="renderplugin" />
                        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf_prices.php" />
                        <input type="hidden" name="plugin_action" value="done" />
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="width:300px;"><?php _e('Category Name', 'payment'); ?></td>
                                <td style="width:175px;"><?php echo sprintf(__('Publish fee (%s)', 'payment'), osc_get_preference('currency', 'payment')); ?></td>
                                <td style="width:175px;"><?php echo sprintf(__('Premium fee (%s)', 'payment'), osc_get_preference('currency', 'payment')); ?></td>
                            </tr>
                            <?php drawCategories($categories); ?>
                        </table>
                        <button type="submit" style="float: right;"><?php _e('Update', 'payment'); ?></button>
                    </form>
                </div>
            </fieldset>
        </div>
        <div style="clear:both;">
            <div style="float: left; width: 100%;">
                <fieldset>
                    <legend><?php _e('Help', 'payment'); ?></legend>
                    <h3><?php _e('Setting up your fees', 'payment'); ?></h3>
                    <p>
                        <?php _e('You could set up different prices for each category', 'payment'); ?>. <?php _e('If the price of a category is left empty, the default value will be applied', 'payment'); ?>.
                    </p>
                </fieldset>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
