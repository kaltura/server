<?php
class KWidevineBatchHelper
{
	const WV_DATE_FORMAT = 'Y-m-d H:i:s';
	
	const ASSET_ID = 'assetid';
	const ASSET_ID_ADD = 'id';
	const ASSET_NAME = 'asset';
	const PROVIDER = 'provider';
	const OWNER = 'owner';
	const STATUS = 'status';
	const POLICY = 'policy';
	const REPLACE = 'replace';
	const PROVIDER_NAME = 'name';
	const LSTART = 'lstart';
	const LEND = 'lend';
	const LICSTART = 'licstart';
	const LICEND = 'licend';
	
	private static $encryptionErrorCodes = array(
						'OK', 'InvalidUsage', 'OwnerNotSpecified', 'ProviderNotSpecified', 'AssetNotSpecified', 'WVEncError',
  						'ConversionError', 'FinalIndexError', 'SyncFrameCountError', 'TrickPlaySyncFrameTooFarError', 'SyncFrameTooFarError',
  						'SyncFrameOffsetMatchError', 'SpecFileError', 'FileNotFoundError', 'ExceptionError', 'ChapteringError',
  						'CodecError', 'ClearEncodeNotAllowed', 'CACgiHostNotSpecified', 'InputFileNotSpecified', 'OutputFileNotSpecified',
  						'IndexError', 'MediaDurationTooLong', 'TooManyTrickPlayFiles', 'IFrameIndexError', 'ManifestCreationError',
  						'ContainerIncompatibleError', 'TrackIdentifierError', 'FileCopyError', 'MediaDurationTooShort'); 
    
	private static $wvPackagerErrorCodes = array(
						'Unknown', 'OK', 'AssetNotFound', 'AssetSaveFailed', 'AssetDeleteFailed', 'AssetAlreadyExist',
    					'InternalError','OperationNotAllowed', 'AssetBlocked', 'OutsideLicenseWindow', 'OutsideRegion',
    					'SignatureMissing', 'SignatureNotValid', 'ProviderUnknown', 'NetworkErr', 'EntitlementMessageErr',
    					'EntitlementDecodeFailed', 'ClientNetworkingErr', 'RequestAborted', 'ClientKeyMissing', 'RegServerNotResponding',
    					'RegServerDown', 'PortalMissing', 'PortalUnknown', 'AssetIdMissing', 'OwnerMissing', 'ProviderMissing',
    					'NameMissing', 'InvalidCCI', 'InvalidDCP', 'InvalidLicenseWindow', 'PolicyNotFound', 'PolicyRejected', 
    					'PolicyServerNotResponding', 'ErrorProcessingClientToken', 'InvalidRegion', 'InvalidNonce', 'InvalidHWID',
    					'InvalidToken', 'InvalidAssetId', 'InvalidName', 'InvalidDiversity', 'InvalidKeyId', 'ModelNotSupported',
    					'InvalidKeyboxSystemID', 'NoDeviceLicenseAvailable', 'UnknownCode', 'InvalidAccessCriteria', 'RegionMissing',
    					'KeyVerificationFailed', 'STBKeyHashFailed', 'UnableToGetSharedKey', 'WrongSystemID', 'RevokedClientVersion',
    					'ClientVersionTampered', 'ClientVersionMissing', 'AssetProviderAlreadyExist', 'DiversityMissing', 'TokenMissing', 
						'ClientModelTampered', 'AssetKeyTooLarge', 'DecryptFailed', 'TooManyAssets', 'MakeNotSupported', 'PolicyAlreadyExist',
    					'InvalidXML', 'ProviderViolation', 'PortalVerificationFailed', 'PortalOverrideNotAllowed', 'Last');
	
	/**
	 * Creates command line for package encryption execution with Widevine SDK
	 * 
	 */
	public static function getEncryptPackageCmdLine(
							$widevineExe, $wvLicenseServerUrl, $iv, $key, $assetName, $inputFiles, $destinationFile, $gop, $portal = null)
	{
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
		
		$cmd = $widevineExe.' -a '.$assetName.' -u '.$wvLicenseServerUrl.' -p '.$portal.' -o '.$portal.' -t '.$inputFiles.' -d '.$destinationFile.' -g '.$gop;
		
		KalturaLog::debug("Encrypt package command: ".$cmd);
		
		$cmd = $cmd.' -v '.$iv.' -k '.$key;
		return $cmd;
		
	}

	/**
	 * Map between error code and error title
	 * 
	 */
	public static function getEncryptPackageErrorMessage($status)
	{
		return self::$encryptionErrorCodes[$status];
	}
	
	/**
	* Send register asset request to Widevine license server
	* If asset name is not passed call getAsset first to get asset by id
	* 
	* https://register.uat.widevine.com/widevine/cypherpc/sign/cgi-bin/RegisterAsset.cgi?asset=test1155&owner=kaltura&provider=name:kaltura,policy:default&replace=1
	* https://register.uat.widevine.com/widevine/cypherpc/cgi-bin/GetAsset.cgi?asset=test537&owner=kaltura&provider=kaltura
	*/
	public static function sendRegisterAssetRequest(
							$wvLicenseServerUrl, $assetName = null, $assetId = null, $portal = null, 
							$policy = null, $licenseStartDate = null, $licenseEndDate = null, &$errorMessage)
	{
		$params = array();
		
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
			
		if($licenseStartDate)
			$licenseStartDate = self::convertLicenseStartDate($licenseStartDate);
		if($licenseEndDate)
			$licenseEndDate = self::convertLicenseEndDate($licenseEndDate);

		//get asset by Id and set assetName
		if($assetId && !$assetName)
		{			
			$params[self::ASSET_ID] = $assetId;
			$params[self::OWNER] = $portal;
			$params[self::PROVIDER] = $portal;
			
			$response = self::sendHttpRequest($wvLicenseServerUrl, WidevinePlugin::GET_ASSET_CGI, $params);
			if($response[self::STATUS] == 1)
			{
				$assetName = $response[self::ASSET_NAME];
				unset($params[self::PROVIDER]);
				$policy = $response[self::POLICY];
				if(!$licenseStartDate)
					$licenseStartDate = $response[self::LSTART];
				if(!$licenseEndDate)
					$licenseEndDate = $response[self::LEND];
			}
			else 
			{
				$errorMessage = "Error in GetAsset API - ".self::$wvPackagerErrorCodes[$response[self::STATUS]];
				return false;
			}
		}
			
		$params[self::ASSET_NAME] = $assetName;
		$params[self::OWNER] = $portal;		
		$params[self::REPLACE] = 1;
		
		$providerParams = array();
		$providerParams[self::PROVIDER_NAME] = $portal;
		if(!$policy)
			$policy = WidevinePlugin::DEFAULT_POLICY;
		$providerParams[self::POLICY] = $policy;
		if($licenseStartDate)
			$providerParams[self::LICSTART] = $licenseStartDate;
		if($licenseEndDate)
			$providerParams[self::LICEND] = $licenseEndDate;
				
		$response = self::sendHttpRequest($wvLicenseServerUrl, WidevinePlugin::REGISTER_ASSET_CGI, $params, $providerParams);
		
		if($response[self::STATUS] == 1)
		{
			return $response[self::ASSET_ID_ADD];
		}
		else
		{
			$errorMessage = "Error in RegisterAsset API -  ".self::$wvPackagerErrorCodes[$response[self::STATUS]];
			return false;
		}
	}
	
	private static function sendHttpRequest($wvLicenseServerUrl, $cgiUrl, $params, $providerParams = null)
	{
		$url = $wvLicenseServerUrl.$cgiUrl.'?';
		
		if($providerParams && count($providerParams))
			$params[self::PROVIDER] = self::providerRequestEncode($providerParams);
		$url .= http_build_query($params, '', '&');
		
		KalturaLog::debug("Request URL: ".$url);
				
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$response = curl_exec($ch);		
		curl_close($ch);
					
		return self::responseDecode($response);
	}
	
	/**
	 * Decode Get asset or register asset response
	 * 
	 * Get: status=1:asset=test1155:assetid=1611336952:owner=kaltura:provider=name:kaltura,lstart:1980-01-01 00:00:01,lend:2033-05-18 00:00:00,
	 * 		policy:default:region=EV:access=1:dcp=0:cci=0:sid=:sysid=447:lstart=1980-01-01 00:00:01:lend=2033-05-18 00:00:00:policy=:polexist=1:type=vod:start=0:max=0:total=0:end:1;
	 * Register: status=1:asset=test1155:id=1611336952
	 * 
	 * @param string $response
	 */
	private static function responseDecode($response)
	{
		$response = trim($response);
		$responseArr = array();
		
		if(!$response)
		{
			$responseArr[self::STATUS] = self::$wvPackagerErrorCodes[0];
			return $responseArr;
		}
		$providerPosStart = strpos($response, self::PROVIDER);
		if($providerPosStart !== false)
		{
			$providerStr = substr($response, $providerPosStart);
			$response = substr($response, 0, $providerPosStart);
			
			$responseArr = self::providerResponseDecode($providerStr);
		}
		$temp = explode(':', $response);
		
		foreach ($temp as $keyValue) 
		{
			$keyValueArr = explode('=', $keyValue);
			if(count($keyValueArr) == 2)
			{
				$responseArr[$keyValueArr[0]] = $keyValueArr[1];
			}
		}		
		return $responseArr;
	}
	
	/**
	 * Build key value array for provider response string
	 * 
	 * @param string $providerStr
	 */
	private static function providerResponseDecode($providerStr)
	{
		$responseArr = array();
		$providerStr = trim($providerStr, self::PROVIDER.'=');
			
		$temp = explode(',', $providerStr);
		foreach ($temp as $keyValue) 
		{
			$key = substr($keyValue, 0, strpos($keyValue, ':'));
			$value = substr($keyValue, strpos($keyValue, ':')+1);
			if($key == 'policy')
				$value = substr($value, 0, strpos($value, ':'));
			$responseArr[$key] = $value;
		}
		
		return $responseArr;
	}
	
	/**
	 * Build provider request params according to the required format
	 * format: provider=name:kaltura,licstart:1980-01-01 00:00:01,licend:2033-05-18 00:00:00,policy:default;
	 * 
	 * @param array $providerParams
	 */
	private static function providerRequestEncode($providerParams)
	{
		$providerParamsPairs = array();
		foreach ($providerParams as $key => $value) 
		{
			$pair = $key.':'.$value;
			$providerParamsPairs[] = $pair;
		}
		
		$encodedParams = implode(',', $providerParamsPairs).';';
		return $encodedParams;
	}
	
	/**
	 * @param field_type $licenseStartDate
	 */
	private static function convertLicenseStartDate($licenseStartDate) {	
		if(!$licenseStartDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_START);
			$licenseStartDate = (int) $dt->format('U');
		}	
		$licenseStartDate = date(self::WV_DATE_FORMAT, $licenseStartDate);
		
		return $licenseStartDate;
	}

	/**
	 * @param field_type $licenseEndDate
	 */
	private static function convertLicenseEndDate($licenseEndDate) {
		if(!$licenseEndDate)
		{
			$dt = new DateTime(WidevinePlugin::DEFAULT_LICENSE_END);
			$licenseEndDate = (int) $dt->format('U');
		}
		$licenseEndDate = date(self::WV_DATE_FORMAT, $licenseEndDate);
		
		return $licenseEndDate;
	}	
}