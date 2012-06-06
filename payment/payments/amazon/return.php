<?php
    /*
     * return.php
     *
     * This page will handle the GetECDetails, and DoECPayment API Calls
     */
    
    //set include
    define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
    require_once ABS_PATH . 'oc-load.php';
    
    $status = PAYMENT_FAILED;
    if(osc_get_preference('amazon_standard', 'payment')==1) {
        $data = ModelPayment::getCustom(Params::getParam('extra'));
        
        $product_type = explode('x', Params::getParam('item_number'));

        $status = Amazon::processStandardPayment();
        /*if($status==PAYMENT_COMPLETED || $status==PAYMENT_ALREADY_PAID) {
            osc_add_flash_ok_message(__('Payment processed correctly', 'payment'));
            if($product_type[0]==101) {
                $item = Item::newInstance()->findByPrimaryKey($product_type[2]);
                $category = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
                View::newInstance()->_exportVariableToView('category', $category);
                payment_js_redirect_to(osc_search_category_url());
            } else if($product_type[0]==201) {
                payment_js_redirect_to(payment_url() . 'user_menu.php');
            } else {
                payment_js_redirect_to(payment_url() . 'user_menu_pack.php');
            }
        } else {
            osc_add_flash_info_message(__('We are processing your payment, if we did not finish in a few seconds, please contact us', 'payment'));
            if($product_type[0]==301) {
                payment_js_redirect_to(payment_url() . 'user_menu_pack.php');
            } else {
                payment_js_redirect_to(payment_url() . 'user_menu.php');
            }
        }*/
    } else {
    
    };
    
    
?>