<?php
/**
 * Integration utilities with Widevine license server
 * 
 * @package plugins.widevine
 * @subpackage lib
 * 
 */
class WidevineLicenseProxyUtils
{
	const SETDURATION = 'setduration';
	const SETPURDURATION = 'setpurduration';
	const DENYHD = 'denyhd';
	const SETPOLICY = 'setpolicy';
	const PORTAL = 'portal';
	const SIGN = 'sign';
	const PTIME = 'ptime';
	const ASSETID = 'assetid';
	const CLIENTID = 'clientid';
	const MK = 'mk';
	const MD = 'md';
	const VER = 'ver';
	
	protected static $allowedOverrideParams = array(self::SETDURATION => self::SETDURATION, self::SETPOLICY =>self::SETPOLICY, 
													self::SETPURDURATION => self::SETPURDURATION, self::DENYHD => self::DENYHD);

	/**
	* Signs the input and forwards license request to Widevine license server
	* @param $requestParams - original parameters
	* @param $overrideParamsStr - additional parameters passed on KS
	* @param $isAdmin - true/false, identifies if called with admin KS
	* @return byte sequence
	*/
	public static function sendLicenseRequest($requestParams, $overrideParamsStr = null, $isAdmin = false)
	{
		self::validateRequest($requestParams);
		
		$ptime = time();
		$signInput = $requestParams[self::ASSETID].
					 $requestParams[self::CLIENTID].
					 $requestParams[self::MK].
					 $requestParams[self::MD].
					 $ptime;
					 
		$dbDrmProfile = DrmProfilePeer::retrieveByProvider(WidevinePlugin::getWidevineProviderCoreValue());
		if($dbDrmProfile)
		{
			$key = $dbDrmProfile->getKey();
			$iv = $dbDrmProfile->getIv();	
			$baseUrl = $dbDrmProfile->getLicenseServerUrl();
			$portal = $dbDrmProfile->getPortal();	
		}
		else 
		{
			$key = WidevinePlugin::getWidevineConfigParam('key');
			$iv = WidevinePlugin::getWidevineConfigParam('iv');
			$baseUrl = WidevinePlugin::getWidevineConfigParam('license_server_url');
			$portal = WidevinePlugin::getWidevineConfigParam('portal');
		}
		KalturaLog::debug("sign input: ".$signInput);
		
		$sign = self::createRequestSignature($signInput, $key, $iv);		
		$requestParams[self::PTIME] = $ptime;
		$requestParams[self::SIGN] = $sign;
				
		$overrideParams = self::getLicenseOverrideParams($overrideParamsStr, $isAdmin);
		
		$requestParams = array_merge($requestParams, $overrideParams);
		
		if(!$baseUrl)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::LICENSE_SERVER_URL_NOT_SET);
					
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
			
		$requestParams[self::PORTAL] = $portal;
		$baseUrl .= '/'.$portal;
		
		return self::doCurl($baseUrl, $requestParams);
	}
	
	public static function createErrorResponse($errorCode, $assetid)
	{
        $badResponse = pack("NN", $errorCode, $assetid);
        $response = base64_encode($badResponse);
        return $response;
	}
	
	public static function printLicenseResponseStatus($response)
	{
		KalturaLog::debug("Encoded license response: ". $response);
		$decoded_response = base64_decode($response);

		// bytes 0 through 3 contain response status code
		// parse response status code
		$response_status = '';
		for ( $i = 0; $i < 4; $i++ )
		{
			$response_byte = sprintf("%02X", ord(substr($decoded_response, $i, 1)));
			$response_status .= $response_byte;
		}
		$response_status_dec = hexdec($response_status);
		if($response_status == 1)
			KalturaLog::debug("License response status OK");
		else
			KalturaLog::debug("License response status Error with code: ".$response_status_dec);
	}

	//this utility function used by both batch and API
	public static function createRequestSignature($data, $key, $iv)
	{
		$digest = openssl_digest($data, "sha1", true);
		$key_bytes = self::getKeyBytes($key, $iv);
		if(!$key_bytes)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::LICENSE_KEY_NOT_SET);
		$iv = pack("H*", substr($key_bytes, 0, 32));
    	$key = pack("H*", substr($key_bytes, 32));
	   	return openssl_encrypt($digest,'aes-256-cbc',$key, false, $iv);
	}
	
	private static function getKeyBytes($key, $iv)
	{	
		$key = str_replace("0x", "", $key);
		$key = str_replace(", ", "", $key);

		$iv = str_replace("0x", "", $iv);
		$iv = str_replace(", ", "", $iv);
		
		return $key.$iv;
	}
	
	protected static function getLicenseOverrideParams($overrideParamsStr, $isAdmin)
	{
		$overrideParams = array();
		$allParams = explode(',', $overrideParamsStr);
		foreach($allParams as $param)
		{
			$exParam = explode(':', $param);
			if (count($exParam) == 2 && array_key_exists($exParam[0], self::$allowedOverrideParams))
			{
				$overrideParams[$exParam[0]] = $exParam[1];
			}
		}	
		if($isAdmin)
		{
			$kmcPolicy = WidevinePlugin::getWidevineConfigParam('kmc_policy');
			if($kmcPolicy)
				$overrideParams[self::SETPOLICY] = $kmcPolicy;
		}	
		return $overrideParams;
	}
	
	protected static function validateRequest($requestParams)
	{
		if(	!array_key_exists(self::CLIENTID, $requestParams) ||
			!array_key_exists(self::MK, $requestParams) ||
			!array_key_exists(self::MD, $requestParams) ||
			!array_key_exists(self::ASSETID, $requestParams) 
			)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::MISSING_MANDATORY_SIGN_PARAMETER);		
	}
	
	protected static function doCurl($baseUrl, $requestParams)
	{
		$requestParamsStr = http_build_query($requestParams, '', '&');
		
		KalturaLog::debug("License request URL: ".$baseUrl);
		KalturaLog::debug("License request params: ".$requestParamsStr);
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $baseUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch,CURLOPT_POST, count($requestParams));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $requestParamsStr);
		
		$response = curl_exec($ch);		
		$error = curl_error($ch);
		curl_close($ch);
		
		return $response;
	}
}