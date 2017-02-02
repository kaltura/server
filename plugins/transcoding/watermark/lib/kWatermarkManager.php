<?php

/**
 * @package plugins.watermark
 * @subpackage lib
 */
class kWatermarkManager
{
	/**
	 *
	 * @param unknown_type $watermarkData
	 * @param unknown_type $watermarkToMerge
	 */
	public static function adjustWatermarkSettings($watermarkData, $watermarkToMerge)
	{
		KalturaLog::log("Merge WM (".serialize($watermarkToMerge).") into (".serialize($watermarkData).")");
		if(is_array($watermarkData))
			$watermarkDataArr = $watermarkData;
		else
			$watermarkDataArr = array($watermarkData);
		
		if(is_array($watermarkToMerge))
			$watermarkToMergeArr = $watermarkToMerge;
		else
			$watermarkToMergeArr = array($watermarkToMerge);
		
		foreach($watermarkToMergeArr as $wmI=>$watermarkToMerge)
		{
			KalturaLog::log("Merging WM:$wmI");
			if(!array_key_exists($wmI, $watermarkDataArr))
			{
				$watermarkDataArr[$wmI] = $watermarkToMerge;
				KalturaLog::log("Added object ($wmI)-".serialize($watermarkToMerge));
				continue;
			}
			
			foreach($watermarkToMerge as $fieldName=>$fieldValue)
			{
				$watermarkDataArr[$wmI]->$fieldName = $fieldValue;
				KalturaLog::log("set($fieldName):".$fieldValue);
				switch($fieldName){
					case "imageEntry":
						KalturaLog::log("unset(url):".$watermarkDataArr[$wmI]->url);
						unset($watermarkDataArr[$wmI]->url);
						break;
					case  "url":
						KalturaLog::log("unset(imageEntry):".$watermarkDataArr[$wmI]->imageEntry);
						unset($watermarkDataArr[$wmI]->imageEntry);
						break;
				}
			}
		}
		
		KalturaLog::log("Merged WM (".serialize($watermarkDataArr).")");
		return $watermarkDataArr;
	}
	
	public static function getWatermarkMetadata($entry)
	{
		$entryId = $entry->getId();
		$partnerId = $entry->getPartnerId();
		$profile = MetadataProfilePeer::retrieveBySystemName(WatermarkPlugin::TRANSCODING_METADATA_PROF_SYSNAME,$partnerId);
		if(!isset($profile))
		{
			KalturaLog::log("Missing Transcoding Metadata Profile (sysName:TRANSCODINGPARAMS, partner:$partnerId)s");
			return null;
		}
		
		$profileId = $profile->getId();
		$metadata = MetadataPeer::retrieveByObject($profileId, MetadataObjectType::ENTRY, $entryId);
		if(!isset($metadata))
			KalturaLog::log("Missing Metadata for entry($entryId), metadata profile (id:$profileId)!");
		
		return $metadata;
	}
	
	/**
	 * $entry
	 */
	public static function getWatermarkMetadataXml($entry)
	{
		$entryId = $entry->getId();
		$metadata = self::getWatermarkMetadata($entry);
		if(!$metadata)
			return null;
		
		KalturaLog::log("Entry ($entryId) has following metadata fields:".print_r($metadata,1));
		
		// Retrieve the associated XML file
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		if(!isset($key))
		{
			KalturaLog::log("Missing file sync key for entry($entryId) metadata object!");
			return null;
		}
		$xmlData = kFileSyncUtils::file_get_contents($key, true, false);
		if(!isset($xmlData)){
			KalturaLog::log("Missing valid file sync for entry($entryId) metadata object!");
			return null;
		}
		return $xmlData;
	}
	
	public static function copyWatermarkData(Metadata $watermarkMetadata, entry $fromEntry, entry $toEntry)
	{
		KalturaLog::debug("copyWatermarkData from [{$fromEntry->getId()}] to [{$toEntry->getId()}]");
		$copyWatermarkMetadata = $watermarkMetadata->copy();
		$copyWatermarkMetadata->setObjectId($toEntry->getId());
		$copyWatermarkMetadata->save();
		
		$srcSyncKey = $watermarkMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$destinationSyncKey = $copyWatermarkMetadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		
		kFileSyncUtils::softCopy($srcSyncKey, $destinationSyncKey);
	}
}