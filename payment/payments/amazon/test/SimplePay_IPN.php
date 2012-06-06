<?php
/**
 * SimplePay_IPN.php
 * @category    Amazon
 * @package     SimplePay
 * @copyright   Copyright 2008 Amazon Technologies, Inc.
 * @link        http://aws.amazon.com
 * @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0 
 * @author      Michael@AWS
 */

/**
 * SimplePay_IPN
 * Check if an IPN POST is valid
 * @package SimplePay
 */
class SimplePay_IPN {
	
	/** @var string AWS Access Secret Access Key */
	private $_AWS_SecretAccessKey = '';

	/**
	 * Construct a new SimplePay_IPN	 
	 * @param string $AWS_SecretAccessKey Your AWS secret access key
	 */
	public function __construct($AWS_SecretAccessKey) {
		$this->_AWS_SecretAccessKey = $AWS_SecretAccessKey;
        echo "#~ ".$AWS_SecretAccessKey." ~#";
	}

	/**
	 * Sign a string using an AWS Secret Access Key
	 * @param string $stringToSign string to sign
	 * @return string signed string
	 */
	private function _signString($stringToSign) {
		$signature = hash_hmac('sha1', $stringToSign, $this->_AWS_SecretAccessKey);
		$signature = base64_encode(pack('H40', $signature));
		return $signature;
	}
	
	/**
	 * Create a string to sign based on an associative array of request parameters
	 * @param array $request associative array of request parameters
	 * @return string a string that needs to be signed
	 */
	private function _createSignatureString(array $request) {
		$sigString = '';
		foreach ($request as $k => $v) {
			if (isset($v) && strcmp($k, 'signature')) $sigString .= $k . $v;
		}
		return $sigString;
	}
	
	/**
	 * Determine if an IPN POST is valid
	 * @param array $response an associative array of parameters returned from an IPN.  Typically you will just need to pass $_POST to this method
	 * @return bool true if the POST is valid, false if it is not
	 * @see http://docs.amazonwebservices.com/AmazonFPS/2007-01-08/AmazonSimplePayImplementationGuide/ipn.html
	 */
	public function isValid(array $response) {
		uksort($response, 'strcasecmp');
        print_r("\n\n\n");
        print_r($response);
        print_r("\n\n\n");
		$generatedSignature = $this->_signString($this->_createSignatureString($response));
        print_r("\n\n\n");
        print_r($generatedSignature);
        print_r("\n\n\n");
		return (strcmp($generatedSignature, $response['signature'])) ? false : true;
	}
}