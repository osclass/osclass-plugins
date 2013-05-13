<?php
/**
 *
 * Sample IPN V2 Handler for Item Payments
 *
 * The purpose of this code is to help you to understand how to process the Instant Payment Notification
 * variables for a payment received through Payza's buttons and integrate it in your PHP site. The following
 * code will ONLY handle ITEM payments. For handling IPNs for SUBSCRIPTIONS, please refer to the appropriate
 * sample code file.
 *
 * Put this code into the page which you have specified as Alert URL.
 * The conditional blocks provide you the logical placeholders to process the IPN variables. It is your responsibility
 * to write appropriate code as per your requirements.
 *
 * If you have any questions about this script or any suggestions, please visit us at: dev.payza.com
 *
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY
 * OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE IMPLIED WARRANTIES OF FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @author Payza
 * @copyright 2011
 */

//set include
define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/');
require_once ABS_PATH . 'oc-load.php';

//The value is the url address of IPN V2 handler and the identifier of the token string
define("IPN_V2_HANDLER", "https://secure.payza.com/ipn2.ashx");
define("TOKEN_IDENTIFIER", "token=");

// get the token from Payza
$token = urlencode($_POST['token']);

//preappend the identifier string "token="
$token = TOKEN_IDENTIFIER.$token;

/**
 *
 * Sends the URL encoded TOKEN string to the Payza's IPN handler
 * using cURL and retrieves the response.
 *
 * variable $response holds the response string from the Payza's IPN V2.
 */

$response = '';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, IPN_V2_HANDLER);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

curl_close($ch);

if(strlen($response) > 0) {
    if(urldecode($response) == "INVALID TOKEN") {
    } else {
        $response = urldecode($response);
        $aps = explode("&", $response);
        $info = array();
        foreach ($aps as $ap) {
            $ele = explode("=", $ap);
            $info[$ele[0]] = $ele[1];
        }

        fclose($fh);

        $receivedMerchantEmailAddress = $info['ap_merchant'];
        $transactionStatus = $info['ap_status'];
        $testModeStatus = $info['ap_test'];
        $purchaseType = $info['ap_purchasetype'];
        $totalAmountReceived = $info['ap_totalamount'];
        $feeAmount = $info['ap_feeamount'];
        $netAmount = $info['ap_netamount'];
        $transactionReferenceNumber = $info['ap_referencenumber'];
        $currency = $info['ap_currency'];
        $transactionDate = $info['ap_transactiondate'];
        $transactionType = $info['ap_transactiontype'];

        $customerFirstName = $info['ap_custfirstname'];
        $customerLastName = $info['ap_custlastname'];
        $customerAddress = $info['ap_custaddress'];
        $customerCity = $info['ap_custcity'];
        $customerState = $info['ap_custstate'];
        $customerCountry = $info['ap_custcountry'];
        $customerZipCode = $info['ap_custzip'];
        $customerEmailAddress = $info['ap_custemailaddress'];

        $myItemName = $info['ap_itemname'];
        $myItemCode = $info['ap_itemcode'];
        $myItemDescription = $info['ap_description'];
        $myItemQuantity = $info['ap_quantity'];
        $myItemAmount = $info['ap_amount'];

        $additionalCharges = $info['ap_additionalcharges'];
        $shippingCharges = $info['ap_shippingcharges'];
        $taxAmount = $info['ap_taxamount'];
        $discountAmount = $info['ap_discountamount'];

        $data = payment_get_custom($info['apc_1'].$info['apc_2'].$info['apc_3'].$info['apc_4'].$info['apc_5'].$info['apc_6']);

    }
} else {
    //something is wrong, no response is received from Payza
}

?>