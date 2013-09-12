<?php
class KWidevineBatchHelper
{
	const WV_DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
	
	private static $encryptionErrorCodes = array(
						'OK', 'InvalidUsage', 'OwnerNotSpecified', 'ProviderNotSpecified', 'AssetNotSpecified', 'WVEncError',
  						'ConversionError', 'FinalIndexError', 'SyncFrameCountError', 'TrickPlaySyncFrameTooFarError', 'SyncFrameTooFarError',
  						'SyncFrameOffsetMatchError', 'SpecFileError', 'FileNotFoundError', 'ExceptionError', 'ChapteringError',
  						'CodecError', 'ClearEncodeNotAllowed', 'CACgiHostNotSpecified', 'InputFileNotSpecified', 'OutputFileNotSpecified',
  						'IndexError', 'MediaDurationTooLong', 'TooManyTrickPlayFiles', 'IFrameIndexError', 'ManifestCreationError',
  						'ContainerIncompatibleError', 'TrackIdentifierError', 'FileCopyError', 'MediaDurationTooShort'); 
    
	/**
	 * Creates command line for register asset execution with Widevine SDK
	 * 
	 */
	public static function getRegisterAssetCmdLine(
							$widevineExe, $wvLicenseServerUrl, $iv, $key, $assetName = null, $assetId = null, 
							$portal = null, $policy = null, $licenseStartDate = null, $licenseEndDate = null)
	{
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
			
		$cmd = $widevineExe.' -A -u '.$wvLicenseServerUrl.' -p '.$portal.' -o '.$portal;
		
		if($assetName)
			$cmd = $cmd.' -a '.$assetName;
		else if($assetId)
			$cmd = $cmd.' -i '.$assetId;
		else 
			//TODO error
			
		if($policy)
			$cmd = $cmd.' -n '.$policy;
		if($licenseStartDate)
			$cmd = $cmd.' -b '.self::convertLicenseStartDate($licenseStartDate);
		if($licenseEndDate)
			$cmd = $cmd.' -e '.self::convertLicenseEndDate($licenseEndDate);
			
		KalturaLog::debug("Register asset command: ".$cmd);
		
		$cmd = $cmd.' -v '.$iv.' -k '.$key;
		return cmd;
	}
	
	/**
	 * Creates command line for package encryption execution with Widevine SDK
	 * 
	 */
	public static function getEncryptPackageCmdLine(
							$widevineExe, $wvLicenseServerUrl, $iv, $key, $assetName, $inputFiles, $destinationFile, $portal = null)
	{
		if(!$portal)
			$portal = WidevinePlugin::KALTURA_PROVIDER;
		
		$cmd = $widevineExe.' -E -a '.$assetName.' -u '.$wvLicenseServerUrl.' -p '.$portal.' -o '.$portal.' -t '.$inputFiles.' -d '.$destinationFile;
		
		KalturaLog::debug("Encrypt package command: ".$cmd);
		
		$cmd = $cmd.' -v '.$iv.' -k '.$key;
		return cmd;
		
	}
	
	/**
	 * Parse output line from the register asset execution
	 * output format - assetId:12322323,message:OK
	 * 
	 */
	public static function parseRegisterAssetCmdOutput($output, &$assetId, &$message)
	{
		$output = trim($output);
		$outputParams = explode(',', $output);
		foreach ($outputParams as $outputParam) 
		{
			$keyValue = explode(':', $outputParam);
			if(count($keyValue) == 2)
			{
				if($keyValue[0] == 'assetId')
					$assetId = $keyValue[1];
				if($keyValue[0] == 'message')
					$message = $keyValue[1];
			}
		}		
	}

	/**
	 * Parse output line from the register asset execution
	 * output format - message:messagetext
	 * 
	 */
	public static function parseEncryptPackageCmdOutput($output, $returnValue, &$errorMessage)
	{
		//Temporary passing the return value and mapping it to the message title since the 
		//error message is not returned by Widevine SDK. Should be changed when they fix it
		$errorMessage = self::$encryptionErrorCodes($returnValue);
//		$output = trim($output);
//		$keyValue = explode(':', $output);
//		if(count($keyValue) == 2)
//		{
//			if($keyValue[0] == 'message')
//				$errorMessage = $keyValue[1];
//		}
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