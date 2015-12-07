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
	const PTIME = 'ptime';
	const SIGN = 'sign';
	
	const SYNC_FRAME_OFFSET_MATCH_ERROR = 11;
	const FIX_ASSET_ERROR = 'Stream duration mismatched';
	const FIX_ASSET_ERROR_RETURN_CODE = 100;
	
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
							$widevineExe, $wvRegServerHost, $iv, $key, $assetName, $inputFiles, $destinationFile, $gop, $portal = null)
	{
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
		
		$cmd = $widevineExe.' -a '.$assetName.' -u '.$wvRegServerHost.' -p '.$portal.' -o '.$portal.' -t '.$inputFiles.' -d '.$destinationFile.' -g '.$gop.' 2>&1';
		
		KalturaLog::info("Encrypt package command: ".$cmd);
		
		//$cmd = $cmd.' -v '.$iv.' -k '.$key;
		return $cmd;
		
	}

	/**
	 * Map between error code and error title
	 * 
	 */
	public static function getEncryptPackageErrorMessage($status)
	{
		if($status == self::FIX_ASSET_ERROR_RETURN_CODE)
		{
			return self::FIX_ASSET_ERROR;
		}
		return self::$encryptionErrorCodes[$status];
	}
	
	/**
	* Send register asset request to Widevine license server
	* If asset name is not passed call getAsset first to get asset by id
	* 
	* https://register.uat.widevine.com/registerasset/kaltura?asset=test1155&owner=kaltura&provider=name:kaltura,policy:default&replace=1
	* https://register.uat.widevine.com/getasset/kaltura?asset=test537&owner=kaltura&provider=kaltura
	*/
	public static function sendRegisterAssetRequest(
							$wvRegServerHost, $assetName = null, $assetId = null, $portal = null, 
							$policy = null, $licenseStartDate = null, $licenseEndDate = null, $iv, $key, &$errorMessage)
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
			
			$ptime = time();
			$signInput = $params[self::OWNER].
					 	 $params[self::PROVIDER].
					 	 $ptime;
			$sign = WidevineLicenseProxyUtils::createRequestSignature($signInput, $key, $iv);
			$params[self::PTIME] = $ptime;
			$params[self::SIGN] = $sign;
			$response = self::sendHttpRequest($wvRegServerHost.WidevinePlugin::GET_ASSET_URL_PART.$portal, $params);
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

		//sign register asset request
		$ptime = time();
		$signInput = $params[self::ASSET_NAME].
					 $params[self::OWNER].
					 $providerParams[self::PROVIDER_NAME].
					 $ptime;
		$sign = WidevineLicenseProxyUtils::createRequestSignature($signInput, $key, $iv);
		$params[self::PTIME] = $ptime;
		$params[self::SIGN] = $sign;
		$response = self::sendHttpRequest($wvRegServerHost.WidevinePlugin::REGISTER_ASSET_URL_PART.$portal, $params, $providerParams);
		
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
	
	public static function getFixAssetCmdLine($ffmpegCmd, $inputFile, $fixedInputFile)
	{
		$cmd =  "$ffmpegCmd -i $inputFile -i $inputFile -map 0:v -map 1:a -c copy -shortest -y -f mp4 -threads 1 $fixedInputFile 2>&1";
		
		KalturaLog::info("Executing command to fix asset : ".$cmd);
		
		return $cmd;
	}
	
	private static function sendHttpRequest($wvRegServerUrl, $params, $providerParams = null)
	{
		$url = $wvRegServerUrl.'?';
		
		if($providerParams && count($providerParams))
			$params[self::PROVIDER] = self::providerRequestEncode($providerParams);
		$url .= http_build_query($params, '', '&');
		
		KalturaLog::info("Request URL: ".$url);
				
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