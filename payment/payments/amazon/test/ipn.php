<?php
# Copyright 2008 Amazon Technologies, Inc.  Licensed under the Apache License, Version 2.0 (the "License"); 
# you may not use this file except in compliance with the License. You may obtain a copy of the License at:
#
# http://aws.amazon.com/apache2.0
#
# This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and limitations under the License.

/*
 This simple IPN script demonstrates how to use the SimplePay_IPN validator class to validate IPN POSTS 
 from Amazon SimplePay.  You will need to edit the constants defined in config.php in order 
 for this script to function properly.
*/

require 'config.php';
require 'SimplePay_IPN.php';

/**
 * Simple logging function to show that this script is receiving IPN POSTS
 * The file specified in the LOG_FILE constant (config.php) will need write permissions
 */
function writeToLog($text) {
	$handle = fopen(LOG_FILE, 'a');
	if ($handle) {
		$logData = "\n\n----[" . date('Y-m-d') . ' ' . date('H:i:s') . "]-------------\n";
		$logData .= $text;
		fwrite($handle, $logData);
	}
}

// Make sure that a POST has been sent, and if so, ensure the POST was sent from Amazon SimplePay
if (array_key_exists('signature', $_POST)) {

	// Construct a new SimplePay IPN validator
	$SimplePayIPN = new SimplePay_IPN(AWS_SECRET_ACCESS_KEY);
		
	if ($SimplePayIPN->isValid($_POST)) {
		// The POST signature is valid
		writeToLog('Valid IPN POST: ' . "\n" . var_export($_POST, true));
	} else {
		// The POST signature is not valid
		writeToLog('IPN POST failed to validate: ' . "\n" . var_export($_POST, true));
	}
	
} else {
	/*
	 Something visited the IPN script, but the signature parameter was
	 not sent in the POST data.  Log the IP address of the request and
	 dump the contents of $_POST to the log
	*/
	writeToLog('Invalid visit to the IPN script from IP ' . $_SERVER['REMOTE_ADDR'] . "\n" . var_export($_POST, true));
}