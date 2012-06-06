<?php
# Copyright 2008 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); 
# you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.
		
	require 'config.php';
	
	// Simple demonstration of how to construct and sign an Amazon SimplePay form
	function getPayNowButtonForm($amount, $description, $referenceId, $immediateReturn, $returnUrl, $abandonUrl) {		
		$formHiddenInputs['accessKey'] = AWS_ACCESS_KEY_ID;
		$formHiddenInputs['amount'] = $amount;
		$formHiddenInputs['description'] = $description;
		if ($referenceId) $formHiddenInputs['referenceId'] = $referenceId;
		if ($immediateReturn) $formHiddenInputs['immediateReturn'] = $immediateReturn;
		if ($returnUrl) $formHiddenInputs['returnUrl'] = $returnUrl;
		if ($abandonUrl) $formHiddenInputs['abandonUrl'] = $abandonUrl;
		$formHiddenInputs['ipnUrl'] = IPN_URL;
		ksort($formHiddenInputs);
		$stringToSign = '';		
		foreach ($formHiddenInputs as $formHiddenInputName => $formHiddenInputValue) {
			$stringToSign = $stringToSign . $formHiddenInputName . $formHiddenInputValue;
		}		
		$formHiddenInputs['signature'] = getSignature($stringToSign, AWS_SECRET_ACCESS_KEY);		
		$form = "<form action=\"https://authorize.payments-sandbox.amazon.com/pba/paypipeline\" method=\"post\">\n";
		foreach ($formHiddenInputs as $formHiddenInputName => $formHiddenInputValue) { 
			$form = $form . "<input type=\"hidden\" name=\"$formHiddenInputName\" value=\"$formHiddenInputValue\" />\n";
		}
		$form = $form . "<input type=\"image\" src=\"https://authorize.payments-sandbox.amazon.com/pba/images/amazonPaymentsButton.jpg\" border=\"0\" alt=\"Pay Using Amazon\" />\n";
		$form = $form . "</form>\n";
		return $form;
	}
	
	// Sign the form parameters
	function getSignature($stringToSign, $secretKey) {
		$signature = hash_hmac('sha1', $stringToSign, $secretKey);
		$signature = base64_encode(pack('H40', $signature));
		return $signature;
	}

	echo getPayNowButtonForm(
		"USD 1.00", 							// Amount
		"test payment",							// Payment reason
		"i123n", 								// Reference Id
		"1", 									// Immediate return
		"http://yourwebsite.com/return.html", 	// Return URL
		"http://yourwebsite.com/abandon.htm"	// Abandon URL
	);