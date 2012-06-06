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
    if(osc_get_preference('paypal_standard', 'payment')==1) {
        $data = ModelPayment::getCustom(Params::getParam('custom'));
        
        $product_type = explode('x', Params::getParam('item_number'));

        $status = Paypal::processStandardPayment();
        if($status==PAYMENT_COMPLETED || $status==PAYMENT_ALREADY_PAID) {
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
        }
    } else {
    
        $data = ModelPayment::getCustom(Params::getParam('extra'));
        
        //set GET var's to local vars:
        $token   = $_GET['token'];
        $payerid = $_GET['PayerID'];
        //set API Creds, Version, and endpoint:
        //**************************************************//
        // This is where you would set your API Credentials //
        // Please note this is not considered "SECURE" this // 
        // is an example only. It is NOT Recommended to use //
        // this method in production........................//
        //**************************************************//
        $APIUSERNAME  = payment_decrypt(osc_get_preference('paypal_api_username', 'payment'));
        $APIPASSWORD  = payment_decrypt(osc_get_preference('paypal_api_password', 'payment'));
        $APISIGNATURE = payment_decrypt(osc_get_preference('paypal_api_signature', 'payment'));
        $ENDPOINT     = 'https://api-3t.paypal.com/nvp';
        if(osc_get_preference('paypal_sandbox', 'payment')==1) {
            $ENDPOINT = 'https://api-3t.sandbox.paypal.com/nvp';
        }

        $VERSION  = '65.1'; //must be >= 65.1
        //Build the Credential String:
        $cred_str = 'USER=' . $APIUSERNAME . '&PWD=' . $APIPASSWORD . '&SIGNATURE=' . $APISIGNATURE . '&VERSION=' . $VERSION;
        //Build NVP String for GetExpressCheckoutDetails
        $nvp_str  = '&METHOD=GetExpressCheckoutDetails&TOKEN='. urldecode($token);

        //combine the two strings and make the API Call
        $req_str  = $cred_str . $nvp_str;
        $response = Paypal::httpPost($ENDPOINT, $req_str);
        //based on the API Response from GetExpressCheckoutDetails
        $doec_str = $cred_str . '&METHOD=DoExpressCheckoutPayment'
                . '&TOKEN=' . $token
                . '&PAYERID=' . $payerid
                . '&PAYMENTREQUEST_0_CURRENCYCODE=' . urldecode($response['PAYMENTREQUEST_0_CURRENCYCODE'])
                . '&PAYMENTREQUEST_0_AMT=' . urldecode($response['PAYMENTREQUEST_0_AMT'])
                . '&PAYMENTREQUEST_0_ITEMAMT=' . urldecode($response['PAYMENTREQUEST_0_ITEMAMT'])
                . '&PAYMENTREQUEST_0_TAXAMT=' . urldecode($response['PAYMENTREQUEST_0_TAXAMT'])
                . '&PAYMENTREQUEST_0_DESC=' . urldecode($response['PAYMENTREQUEST_0_DESC'])
                . '&PAYMENTREQUEST_0_PAYMENTACTION=Sale'
                . '&L_PAYMENTREQUEST_0_ITEMCATEGORY0=' . urldecode($response['L_PAYMENTREQUEST_0_ITEMCATEGORY0'])
                . '&L_PAYMENTREQUEST_0_NAME0=' . urldecode($response['L_PAYMENTREQUEST_0_NAME0'])
                . '&L_PAYMENTREQUEST_0_NUMBER0=' . urldecode($response['L_PAYMENTREQUEST_0_NUMBER0'])
                . '&L_PAYMENTREQUEST_0_QTY0=' . urldecode($response['L_PAYMENTREQUEST_0_QTY0'])
                . '&L_PAYMENTREQUEST_0_TAXAMT0=' . urldecode($response['L_PAYMENTREQUEST_0_TAXAMT0'])
                . '&L_PAYMENTREQUEST_0_AMT0=' . urldecode($response['L_PAYMENTREQUEST_0_AMT0'])
                . '&L_PAYMENTREQUEST_0_DESC0=' . urldecode($response['L_PAYMENTREQUEST_0_DESC0'])
                . '&NOTIFYURL=';

        //make the DoEC Call:
        $doresponse = Paypal::httpPost($ENDPOINT, $doec_str);

        $status = Paypal::processDGPayment($doresponse, $response);

        $product_type = explode('x', urldecode($response['L_PAYMENTREQUEST_0_NUMBER0']));
        if($status==PAYMENT_COMPLETED || $status==PAYMENT_ALREADY_PAID) {
            osc_add_flash_ok_message(__('Payment processed correctly', 'payment'));
            if ($product_type[0] == '101') {
                $item = Item::newInstance()->findByPrimaryKey($product_type[2]);
                $category = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
                View::newInstance()->_exportVariableToView('category', $category);
                $html = '<p>' . __('Payment processed correctly', 'payment') . ' <a href=\\"' . osc_search_category_url() . '\\">' . __('Click here to continue', 'payment') . '</a></p>';
                $url = osc_search_category_url();
            } else if ($product_type[0] == '201') {

                $html = '<p>' . __('Payment processed correctly', 'payment') . ' <a href=\\"' . payment_js_redirect_to(payment_url() . 'user_menu.php') . '\\">' . __("Click here to continue", 'payment') . '</a></p>';
                
                $url = payment_js_redirect_to(payment_url() . 'user_menu.php');
            } else {
                $html = '<p>' . __('Payment processed correctly', 'payment') . ' <a href=\\"' . payment_js_redirect_to(payment_url() . 'user_menu_pack.php') . '\\">' . __("Click here to continue", 'payment') . '</a></p>';
                $url = payment_js_redirect_to(payment_url()."user_menu_pack.php");
            }
        } else if($status==PAYMENT_PENDING) {
            osc_add_flash_info_message(__('We are processing your payment, if we did not finish in a few seconds, please contact us', 'payment'));
            if($product_type[0]==301) {
                payment_js_redirect_to(payment_url() . 'user_menu_pack.php');
            } else {
                payment_js_redirect_to(payment_url() . 'user_menu.php');
            }
        } else {
            $item = Item::newInstance()->findByPrimaryKey($product_type[2]);
            $category = Category::newInstance()->findByPrimaryKey($item['fk_i_category_id']);
            View::newInstance()->_exportVariableToView('category', $category);
            $html = '<p>'.__("There was a problem processing your payment. Please contact the administrators and",'payment').' <a href=\\"'.osc_search_category_url().'\\">'.__("Click here to continue", 'payment').'</a></p>';
            $url = osc_search_category_url();

            osc_add_flash_error_message(__("There was a problem processing your payment. Please contact the administrators",'payment'));
        }
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <script type="text/javascript" src="https://www.paypalobjects.com/js/external/dg.js"></script>
            <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.js"></script>

            <title><?php echo osc_page_title(); ?></title>
        </head>
        <body>
            <script type="text/javascript">
                top.rd.innerHTML = "<?php echo $html ; ?>" ;
                top.location.href = "<?php echo $url ; ?>" ;
                top.dg_<?php echo $data['random'] ; ?>.closeFlow() ;
            </script>
        </body>
    </html>
<?php }; ?>