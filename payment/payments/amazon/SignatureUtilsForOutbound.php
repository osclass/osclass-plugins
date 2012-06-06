<?php
/*******************************************************************************
 *	Copyright 2008-2010 Amazon Technologies, Inc.
 *	Licensed under the Apache License, Version 2.0 (the 'License');
 *
 *	You may not use this file except in compliance with the License.
 *	You may obtain a copy of the License at: http://aws.amazon.com/apache2.0
 *	This file is distributed on an 'AS IS' BASIS, WITHOUT WARRANTIES OR
 *	CONDITIONS OF ANY KIND, either express or implied. See the License for the
 *	specific language governing permissions and limitations under the License.
 ******************************************************************************/


class Amazon_FPS_SignatureException extends Exception {}


class Amazon_FPS_SignatureUtilsForOutbound {
	 
    const SIGNATURE_KEYNAME = "signature";
    const SIGNATURE_METHOD_KEYNAME = "signatureMethod";
    const SIGNATURE_VERSION_KEYNAME = "signatureVersion";
    const SIGNATURE_VERSION_1 = "1";
    const SIGNATURE_VERSION_2 = "2";
    const CERTIFICATE_URL_KEYNAME = "certificateUrl";
    const FPS_PROD_ENDPOINT = 'https://fps.amazonaws.com/';
    const FPS_SANDBOX_ENDPOINT = 'https://fps.sandbox.amazonaws.com/';
    const USER_AGENT_IDENTIFIER = 'SigV2_MigrationSampleCode_PHP-2010-09-13';


	//Your AWS access key	
	private $aws_access_key;

	//Your AWS secret key. Required only for ipn or return url verification signed using signature version1.	
	private $aws_secret_key;

    public function __construct($aws_access_key = null, $aws_secret_key = null) {
        $this->aws_access_key = $aws_access_key;
        $this->aws_secret_key = $aws_secret_key;
    }
	
    /**
     * Validates the request by checking the integrity of its parameters.
     * @param parameters - all the http parameters sent in IPNs or return urls. 
     * @param urlEndPoint should be the url which recieved this request. 
     * @param httpMethod should be either POST (IPNs) or GET (returnUrl redirections)
     */
    public function validateRequest(array $parameters, $urlEndPoint, $httpMethod)  {
        $signatureVersion = $parameters[self::SIGNATURE_VERSION_KEYNAME];
        if (self::SIGNATURE_VERSION_2 == $signatureVersion) {
            return $this->validateSignatureV2($parameters, $urlEndPoint, $httpMethod);
        } else {
            return $this->validateSignatureV1($parameters);
        }
    }

    /**
     * Verifies the signature using HMAC and your secret key. 
     */
    private function validateSignatureV1(array $parameters) {
	if(isset($parameters[self::SIGNATURE_KEYNAME])){
	    $signatureKey = self::SIGNATURE_KEYNAME;
	}else{
	    throw new Amazon_FPS_SignatureException("Signature not present in parameter list"); 
	}
	$signature = $parameters[$signatureKey];
	unset($parameters[$signatureKey]);
	    //We should not include signature while calculating string to sign.
	$stringToSign = self::_calculateStringToSignV1($parameters);
	    //We should include signature back to array after calculating string to sign.
	$parameters[$signatureKey] = $signature;
	        
        return $signature == base64_encode(hash_hmac('sha1', $stringToSign, $this->aws_secret_key, true));
    }
	
    /**
     * Verifies the signature. 
     * Only default algorithm OPENSSL_ALGO_SHA1 is supported.
     */
    private function validateSignatureV2(array $parameters, $urlEndPoint, $httpMethod) {
	//1. Input validation
	    $signature = $parameters[self::SIGNATURE_KEYNAME];
	    if (!isset($signature)) {
	    	throw new Amazon_FPS_SignatureException("'signature' is missing from the parameters.");
	    }
	    $signatureMethod = $parameters[self::SIGNATURE_METHOD_KEYNAME];
	    if (!isset($signatureMethod)) {
	    	throw new Amazon_FPS_SignatureException("'signatureMethod' is missing from the parameters.");
	    }
	    $signatureAlgorithm = self::getSignatureAlgorithm($signatureMethod);
	    if (!isset($signatureAlgorithm)) {
	    	throw new Amazon_FPS_SignatureException("'signatureMethod' present in parameters is invalid. Valid values are: RSA-SHA1");
	    }
	    $certificateUrl = $parameters[self::CERTIFICATE_URL_KEYNAME];
	    if (!isset($certificateUrl)) {
	    	throw new Amazon_FPS_SignatureException("'certificateUrl' is missing from the parameters.");
	    }
	    elseif((stripos($parameters[self::CERTIFICATE_URL_KEYNAME], self::FPS_PROD_ENDPOINT) !== 0) 
	        && (stripos($parameters[self::CERTIFICATE_URL_KEYNAME], self::FPS_SANDBOX_ENDPOINT) !== 0)){
			throw new Amazon_FPS_SignatureException('The `certificateUrl` value must begin with ' . self::FPS_PROD_ENDPOINT . ' or ' . self::FPS_SANDBOX_ENDPOINT . '.');
		}
	     $verified = $this->verifySignature($parameters, $urlEndPoint);
	    if (!$verified){
		throw new Amazon_FPS_SignatureException('Certificate could not be verified by the FPS service');
	    }

	     return $verified;
    
}
private function httpsRequest($url){
		// Compose the cURL request
   	   $curlHandle = curl_init();
   	   curl_setopt($curlHandle, CURLOPT_URL, $url);
   	   curl_setopt($curlHandle, CURLOPT_FILETIME, false);
   	   curl_setopt($curlHandle, CURLOPT_FRESH_CONNECT, true);
   	   curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
   	   curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
   	   curl_setopt($curlHandle, CURLOPT_CAINFO, getcwd().'/ca-bundle.crt');
   	   curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, false);
   	   curl_setopt($curlHandle, CURLOPT_MAXREDIRS, 0);
   	   curl_setopt($curlHandle, CURLOPT_HEADER, true);
   	   curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
   	   curl_setopt($curlHandle, CURLOPT_NOSIGNAL, true);
   	   curl_setopt($curlHandle, CURLOPT_USERAGENT, self::USER_AGENT_IDENTIFIER);
   		// Handle the encoding if we can.
   	   if (extension_loaded('zlib')){
   	   	curl_setopt($curlHandle, CURLOPT_ENCODING, '');
   	   }
   	
   	    // Execute the request
   	   $response = curl_exec($curlHandle);
   		
	    // Grab only the body
   	   $headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
   	   $responseBody = substr($response, $headerSize);
   	
   		// Close the cURL connection
   	   curl_close($curlHandle);
   	
   		// Return the public key
   	   return $responseBody;
	}

	/**
	 * Method: verify_signature
	 */
	private function verifySignature($parameters, $urlEndPoint){
		// Switch hostnames
		if (stripos($parameters[self::CERTIFICATE_URL_KEYNAME], self::FPS_SANDBOX_ENDPOINT) === 0){
			$fpsServiceEndPoint = self::FPS_SANDBOX_ENDPOINT;
		}
		elseif (stripos($parameters[self::CERTIFICATE_URL_KEYNAME], self::FPS_PROD_ENDPOINT) === 0){
			$fpsServiceEndPoint = self::FPS_PROD_ENDPOINT;
		}

		$url = $fpsServiceEndPoint . '?Action=VerifySignature&UrlEndPoint=' . rawurlencode($urlEndPoint);

		$queryString = rawurlencode(http_build_query($parameters, '', '&'));
		//$queryString = str_replace(array('%2F', '%2B'), array('%252F', '%252B'), $queryString);

		$url .= '&HttpParameters=' . $queryString . '&Version=2008-09-17';

		$response = $this->httpsRequest($url);
        print_r($response);
        
		$xml = new SimpleXMLElement($response);
		$result = (string) $xml->VerifySignatureResult->VerificationStatus;

		return ($result === 'Success');
	}

    /**
     * Calculate String to Sign for SignatureVersion 1
     * @param array $parameters request parameters
     * @return String to Sign
     */
    private static function _calculateStringToSignV1(array $parameters) {
        $data = '';
        uksort($parameters, 'strcasecmp');
        foreach ($parameters as $parameterName => $parameterValue) {
            $data .= $parameterName . $parameterValue;
        }
        return $data;
    }

    
	
    
    private static function getSignatureAlgorithm($signatureMethod) {
        if ("RSA-SHA1" == $signatureMethod) {
            return OPENSSL_ALGO_SHA1;
        }
        return null;
    }

}
?>