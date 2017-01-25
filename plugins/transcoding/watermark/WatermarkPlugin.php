<?php
/**
 * Adjust asset-params with watermarks according to custom metadata
 *
 * @package plugins.watermark
 */
class WatermarkPlugin extends KalturaPlugin implements IKalturaPending, IKalturaAssetParamsAdjuster, IKalturaEventConsumers
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
	
	const WATERMARK_FLOW_MANAGER_CLASS = 'kWatermarkFlowManager';
	
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
	 * @see IKalturaEventConsumers::getEventConsumers()
 	 */
	public static function getEventConsumers()
	{
		return array(
			self::WATERMARK_FLOW_MANAGER_CLASS,
		);
	}
		
	/* (non-PHPdoc)
	 * @see IKalturaAssetParamsAdjuster::adjustAssetParams()
	 */
	public function adjustAssetParams($entryId, array &$flavors)
	{
		$entry = entryPeer::retrieveByPK($entryId);
		if(!isset($entry))
		{
			KalturaLog::warning("Bad entry id ($entryId).");
			return;
		}
		
		$xmlStr = kWatermarkManager::getWatermarkMetadataXml($entry);
		if(!isset($xmlStr))
		{
			KalturaLog::log("Entry($entryId) metadata object misses valid file sync! Nothing to adjust");
			return;
		}
		
		KalturaLog::log("Adjusting: entry($entryId),metadata profile(".self::TRANSCODING_METADATA_PROF_SYSNAME."),xml==>$xmlStr");

		// Retrieve the custom metadata fields from the asocieted XML
		
		
		/*
		 * Acquire the optional 'full' WM settings (TRANSCODING_METADATA_WATERMMARK_SETTINGS) 
		 * adjust it to custom meta imageEntry/imageUrl values,
		 * if those provided.
		 */
		$watermarkSettings = array();
		$xml = new SimpleXMLElement($xmlStr);
		$fldName = self::TRANSCODING_METADATA_WATERMMARK_SETTINGS;

		if(isset($xml->$fldName)) 
		{
			$watermarkSettingsStr =(string)$xml->$fldName;
			KalturaLog::log("Found custom metadata - $fldName($watermarkSettingsStr)");
			if(isset($watermarkSettingsStr)) 
			{
				$watermarkSettings = json_decode($watermarkSettingsStr);
				if(!is_array($watermarkSettings)) 
				{
					$watermarkSettings = array($watermarkSettings);
				}
				KalturaLog::log("WM($fldName) object:".serialize($watermarkSettings));
			}
		}
		else
			KalturaLog::log("No custom metadata - $fldName");

		/*
		 * Acquire the optional partial WM settings ('imageEntry'/'url') 
		 * Prefer the 'imageEntry' in case when both 'imageEntr' and 'url' are previded ('url' ignored).
		 */
		$wmTmp = null;
		$fldName = self::TRANSCODING_METADATA_WATERMMARK_IMAGE_ENTRY;
		if(isset($xml->$fldName)) 
		{
			$wmTmp->imageEntry =(string)$xml->$fldName;
			KalturaLog::log("Found custom metadata - $fldName($wmTmp->imageEntry)");
		}
		else 
		{
			KalturaLog::log("No custom metadata - $fldName");
			$fldName = self::TRANSCODING_METADATA_WATERMMARK_IMAGE_URL;
			if(isset($xml->$fldName)) 
			{
				$fldVal = (string)$xml->$fldName;
				$wmTmp->url =(string)$xml->$fldName;
				KalturaLog::log("Found custom metadata - $fldName($wmTmp->url)");
			}
			else 
				KalturaLog::log("No custom metadata - $fldName");
		}
		
		/*
		 * Merge the imageEntry/imageUrl values into previously aquired 'full' WM settings (if provided).
		 */
		if(isset($wmTmp))
			$watermarkSettings = kWatermarkManager::adjustWatermarkSettings($watermarkSettings, $wmTmp);
		KalturaLog::log("Custom meta data WM settings:".serialize($watermarkSettings));

		/*
		 * Check for valuable WM custom data.
		 * If none - leave
		 */
		{
			$fldCnt = 0;
			foreach($watermarkSettings as $wmI=>$wmTmp)
			{
				if(isset($wmTmp))
				{
					$fldCnt+= count((array)$wmTmp);
				}
			}
			if($fldCnt==0)
			{
				KalturaLog::log("No WM custom data to merge");
				return;
			}
		}
		
		/*
		 * Loop through the flavor params to update the WM settings,
		 * if it is required.
		 */
		foreach($flavors as $k=>$flavor) 
		{
			KalturaLog::log("Processing flavor id:".$flavor->getId());
			$wmDataFixed = null;
			$wmPredefined = null;
			$wmPredefinedStr = $flavor->getWatermarkData();
			if(!(isset($wmPredefinedStr) && ($wmPredefined=json_decode($wmPredefinedStr))!=null))
			{
				KalturaLog::log("No WM data for flavor:".$flavor->getId());
				continue;
			}
			KalturaLog::log("wmPredefined : count(".count($wmPredefined).")-".serialize($wmPredefined));

			$wmDataFixed = kWatermarkManager::adjustWatermarkSettings($wmPredefined, $watermarkSettings);

			/*
			 * The 'full' WM settings in the custom metadata overides any exitings WM settings 
			 */
			$wmJsonStr = json_encode($wmDataFixed);
			$flavor->setWatermarkData($wmJsonStr);
			$flavors[$k]= $flavor;
			KalturaLog::log("Update flavor (".$flavor->getId().") WM to: $wmJsonStr");
		}
	}
}
