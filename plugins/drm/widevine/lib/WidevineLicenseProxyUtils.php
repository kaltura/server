<?php
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
		
	public static function sendLicenseRequest($requestParams, $overrideParamsStr)
	{
		if(	!array_key_exists(self::CLIENTID, $requestParams) ||
			!array_key_exists(self::MK, $requestParams) ||
			!array_key_exists(self::MD, $requestParams) ||
			!array_key_exists(self::ASSETID, $requestParams) 
			)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::MISSING_MANDATORY_PARAMETER);
		
		$ptime = time();
		$signInput = $requestParams[self::ASSETID].
					 $requestParams[self::CLIENTID].
					 $requestParams[self::MK].
					 $requestParams[self::MD].
					 $ptime;
		$sign = self::createRequestSignature($signInput);		
		$requestParams[self::PTIME] = $ptime;
		$requestParams[self::SIGN] = $sign;
				
		$overrideParams = self::getLicenseOverrideParams($overrideParamsStr);
		
		$requestParams = array_merge($requestParams, $overrideParams);
		$url = self::buildLicenseServerUrl($requestParams);
		
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		//https options
//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//		curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/CAcerts/BuiltinObjectToken-EquifaxSecureCA.crt");
		
		$response = curl_exec($ch);		
		$error = curl_error($ch);
		curl_close($ch);
		
		return $response;
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

	protected static function createRequestSignature($data)
	{
		KalturaLog::debug("sign input: ".$data);
		
		$digest = openssl_digest($data, "sha1", true);
		$key_bytes = WidevinePlugin::getWidevineConfigParam('key_bytes');
		if(!$key_bytes)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::LICENSE_KEY_NOT_SET);
		$iv = pack("H*", substr($key_bytes, 0, 32));
    	$key = pack("H*", substr($key_bytes, 32));
	   	return openssl_encrypt($digest,'aes-256-cbc',$key, false, $iv);
	}
	
	protected static function getLicenseOverrideParams($overrideParamsStr)
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
		return $overrideParams;
	}
	
	protected static function buildLicenseServerUrl($urlParams)
	{
		$baseUrl = WidevinePlugin::getWidevineConfigParam('license_server_url');
		if(!$baseUrl)
			throw new KalturaWidevineLicenseProxyException(KalturaWidevineErrorCodes::LICENSE_SERVER_URL_NOT_SET);
			
		$portal = WidevinePlugin::getWidevineConfigParam('portal');
		if(!$portal)
			$portal = 'kaltura';
			
		$urlParams[self::PORTAL] = $portal;
		$requestUrl = $baseUrl.'?';
		$requestUrl .= http_build_query($urlParams, '', '&');
		
		KalturaLog::debug("License request URL: ".$requestUrl);
		
		return $requestUrl;
	}
}