<?php
/**
 * Adjust asset-params with watermarks according to custom metadata
 *
 * @package plugins.watermark
 */
class WatermarkPlugin extends KalturaPlugin implements IKalturaPending, IKalturaAssetParamsAdjuster
{
	const PLUGIN_NAME = 'watermark';
	
	const METADATA_PLUGIN_NAME = 'metadata';
	const METADATA_PLUGIN_VERSION_MAJOR = 1;
	const METADATA_PLUGIN_VERSION_MINOR = 0;
	const METADATA_PLUGIN_VERSION_BUILD = 0;

	const TRANSCODING_METADATA_PROF_SYSNAME = 'TRANSCODINGPARAMS';
		
	const TRANSCODING_METADATA_WATERMMARK_SETTINGS = 'WatermarkSettings';
	const TRANSCODING_METADATA_WATERMMARK_IMAGE_ENTRY = 'WatermarkImageEntry';
	const TRANSCODING_METADATA_WATERMMARK_IMAGE_URL = 'WatermarkImageURL';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaPending::dependsOn()
	 */
	public static function dependsOn()
	{
		$metadataVersion = new KalturaVersion(self::METADATA_PLUGIN_VERSION_MAJOR, self::METADATA_PLUGIN_VERSION_MINOR, self::METADATA_PLUGIN_VERSION_BUILD);
		$metadataDependency = new KalturaDependency(self::METADATA_PLUGIN_NAME, $metadataVersion);
		
		return array($metadataDependency);
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaAssetParamsAdjuster::adjustAssetParams()
	 */
	public function adjustAssetParams($entryId, array &$flavors)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!isset($entry)){
			KalturaLog::warning("Bad entry id ($entryId).");
			return;
		}
		
		$partnerId = $entry->getPartnerId();
		$profile = MetadataProfilePeer::retrieveBySystemName(self::TRANSCODING_METADATA_PROF_SYSNAME,$partnerId);
		if(!isset($profile)){
			KalturaLog::log("No Transcoding Metadata Profile (sysName:".self::TRANSCODING_METADATA_PROF_SYSNAME.", partner:$partnerId). Nothing to adjust");
			return;
		}

		$metadata = MetadataPeer::retrieveByObject($profile->getId(), MetadataObjectType::ENTRY, $entryId);
		if(!isset($metadata)){
			KalturaLog::log("No Metadata for entry($entryId), metadata profile (id:".$profile->getId()."). Nothing to adjust");
			return;
		}

		KalturaLog::log("Entry ($entryId) has following metadata fields:".print_r($metadata,1));
		
		// Retrieve the associated XML file
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		if(!isset($key)){
			KalturaLog::log("Entry($entryId) metadata object misses file sync key! Nothing to adjust");
			return;
		}
		$xmlStr = kFileSyncUtils::file_get_contents($key, true, false);
		if(!isset($xmlStr)){
			KalturaLog::log("Entry($entryId) metadata object misses valid file sync! Nothing to adjust");
			return;
		}
		
		KalturaLog::log("Adjusting: entry($entryId),metadata profile(".self::TRANSCODING_METADATA_PROF_SYSNAME."),xml==>$xmlStr");

		$watermarkSettingsStr = null;
		$imageEntry = null;
		$imageUrl = null;
		
		// Retrieve the custom metadata fields from the asocieted XML
		$xml = new SimpleXMLElement($xmlStr);
		$fldName = self::TRANSCODING_METADATA_WATERMMARK_SETTINGS;
		if(isset($xml->$fldName)) {
			$watermarkSettingsStr =(string)$xml->$fldName;
			KalturaLog::log("Found metadata - $fldName($watermarkSettingsStr)");
		}
		
		$fldName = self::TRANSCODING_METADATA_WATERMMARK_IMAGE_ENTRY;
		if(isset($xml->$fldName)) {
			$imageEntry =(string)$xml->$fldName;
			KalturaLog::log("Found metadata - $fldName($imageEntry)");
		}
		$fldName = self::TRANSCODING_METADATA_WATERMMARK_IMAGE_URL;
		if(isset($xml->$fldName)) {
			$imageUrl =(string)$xml->$fldName;
			KalturaLog::log("Found metadata - $fldName($imageUrl)");
		}
		
		/*
		 * The imageEntry is preffered if both imageEntry and url are set,
		 * in such case - remove the url
		 */
		if(isset($imageEntry) && isset($imageUrl)) {
			KalturaLog::log("Found both ".self::TRANSCODING_METADATA_WATERMMARK_IMAGE_URL."($imageEntry) and $fldName($imageUrl). Removing $fldName");
			$imageUrl = null; // 
		}
		
		/*
		 * If custom-metadate contains 'full' WM settings ('watermarkSettingsStr' is set), 
		 * adjust it to custom meta imageEntry/imageUrl values,
		 * if those provided.
		 */
		if(isset($watermarkSettingsStr)) {
			$watermarkSettings = json_decode($watermarkSettingsStr);
			$this->adjustWatermarSettings($watermarkSettings, $imageEntry, $imageUrl);
		}
		
		/*
		 * Loop through the flavor params to update the WM settings,
		 * if it is required.
		 */
		foreach($flavors as $k=>$flavor) {
			KalturaLog::log("Processing flavor id:".$flavor->getId());
			$wmDataObj = null;
			
			/*
			 * The 'full' WM settings in the custom metadata overides any exitings WM settings 
			 */
			if(isset($watermarkSettings)) {
				$wmDataObj = clone $watermarkSettings;
			}
			else {
				/*
				 * No 'full' settings.
				 * Adjust the existing flavor WM data with custom metadata imageEntry/imageUrl
				 */
				$wmDataStr = $flavor->getWatermarkData();
				if(isset($wmDataStr)){
					$wmDataObj = json_decode($wmDataStr);
					if($this->adjustWatermarSettings($wmDataObj, $imageEntry, $imageUrl)==false){
						continue;
					}
				}
			}
			
			if(isset($wmDataObj)) {
				$toJson = json_encode($wmDataObj);
				$flavor->setWatermarkData($toJson);
				$flavors[$k]= $flavor;
				KalturaLog::log("Set flavor (".$flavor->getId().") WM to $toJson");
			}
		}
	}

	/**
	 * 
	 * @param unknown_type $watermarkSettings
	 * @param unknown_type $imageEntry
	 * @param unknown_type $imageUrl
	 */
	protected function adjustWatermarSettings($watermarkSettings, $imageEntry, $imageUrl)
	{
		if(isset($imageEntry)) {
			$watermarkSettings->imageEntry = $imageEntry;
			if(isset($watermarkSettings->url)){
				unset($watermarkSettings->url);
			}
		}
		else if(isset($imageUrl)) {
			$watermarkSettings->imageUrl = $imageUrl;
			if(isset($watermarkSettings->imageEntry)){
				unset($watermarkSettings->imageEntry);
			}
		}
		else
			return false;
		return true;
	}
}
