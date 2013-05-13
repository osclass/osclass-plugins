<?php
$url = '';
$mp = ModelPayment::newInstance();
if(osc_is_web_user_logged_in()) {
    $extra = payment_get_custom(Params::getParam('extra'));;
    $product_type = explode('x', Params::getParam("product"));
    $item = Item::newInstance()->findByPrimaryKey($extra['itemid']);
    $wallet = $mp->getWallet(osc_logged_user_id());
    $category_fee = 0;
    if(osc_logged_user_id()==$item['fk_i_user_id']) {
        if ($product_type[0] == '101') {
            if(!$mp->publishFeeIsPaid($item['pk_i_id'])) {
                $category_fee = $mp->getPublishPrice($item['fk_i_category_id']);
            }
        } else if ($product_type[0] == '201') {
            if(!$mp->premiumFeeIsPaid($item['pk_i_id'])) {
                $category_fee = $mp->getPremiumPrice($item['fk_i_category_id']);
            }
        }
    }
    if($category_fee > 0 && $wallet['formatted_amount']>$category_fee) {
        $payment_id = $mp->saveLog(
            Params::getParam('desc'), //concept
            'wallet_'.date("YmdHis"), // transaction code
            $category_fee, //amount
            osc_get_preference("currency", "payment"), //currency
            $data['email'], // payer's email
            $data['user'], //user
            $data['itemid'], //item
            $product_type[0], //product type
            'WALLET'); //source
        $mp->addWallet(osc_logged_user_id(), -$category_fee);
        if ($product_type[0] == '101') {
            $mp->payPublishFee($data['itemid'], $payment_id);
            $url = osc_search_category_url();
        } else if ($product_type[0] == '201') {
            $mp->payPremiumFee($data['itemid'], $payment_id);
            $url = osc_render_file_url(osc_plugin_folder(__FILE__) . 'user_menu.php');
        }
    }
}
    /*
if($url!='') {
    osc_add_flash_ok_message(__('Payment processed correctly', 'payment'));
    payment_js_redirect_to($url);
} else {
    osc_add_flash_error_message(__('There were some errors, please try again later or contact the administrators', 'payment'));
    payment_js_redirect_to(osc_render_file_url(osc_plugin_folder(__FILE__) . 'user_menu.php'));
}*/
?>