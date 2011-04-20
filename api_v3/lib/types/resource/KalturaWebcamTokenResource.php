<?php
/**
 * Used to ingest media that streamed to the system and represented by token that returned from media server such as FMS or red5.
 * 
 * @package api
 * @subpackage objects
 */
class KalturaWebcamTokenResource extends KalturaContentResource 
{
	/**
	 * Token that returned from media server such as FMS or red5. 
	 * @var string
	 */
	public $token;

	public function validateEntry(entry $dbEntry)
	{
		parent::validateEntry($dbEntry);
    	$this->validatePropertyNotNull('token');
	}

	public function entryHandled(entry $dbEntry)
	{
		parent::entryHandled($dbEntry);
		
		$originalFlavorAsset = flavorAssetPeer::retreiveOriginalByEntryId($dbEntry->getId());
		$syncKey = $originalFlavorAsset->getSyncKey(asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
    	$sourceFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
    	
		// call mediaInfo for file
		$dbMediaInfo = new mediaInfo();
		try
		{
			$mediaInfoParser = new KMediaInfoMediaParser($sourceFilePath, kConf::get('bin_path_mediainfo'));
			$mediaInfo = $mediaInfoParser->getMediaInfo();
			$dbMediaInfo = $mediaInfo->toInsertableObject($dbMediaInfo);
			$dbMediaInfo->setFlavorAssetId($originalFlavorAsset->getId());
			$dbMediaInfo->save();
		}
		catch(Exception $e)
		{
			KalturaLog::err("Getting media info: " . $e->getMessage());
			$dbMediaInfo = null;
		}
		
		// fix flavor asset according to mediainfo
		if($dbMediaInfo)
		{
			KDLWrap::ConvertMediainfoCdl2FlavorAsset($dbMediaInfo, $originalFlavorAsset);
			$flavorTags = KDLWrap::CDLMediaInfo2Tags($dbMediaInfo, array(flavorParams::TAG_WEB, flavorParams::TAG_MBR));
			$originalFlavorAsset->setTags(implode(',', array_unique($flavorTags)));
		}
		
   		$dbEntry->setStatus(entryStatus::READY);
   		$dbEntry->save();
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kLocalFileResource();
			
	    $content = myContentStorage::getFSContentRootPath();
	    $entryFullPath = "{$content}/content/webcam/{$this->token}.flv";
	    
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$entryFixedFullPath = $entryFullPath . '.fixed.flv';
 		KalturaLog::debug("Fix webcam full path from [$entryFullPath] to [$entryFixedFullPath]");
		myFlvStaticHandler::fixRed5WebcamFlv($entryFullPath, $entryFixedFullPath);
				
		$entryNewFullPath = $entryFullPath . '.clipped.flv';
 		KalturaLog::debug("Clip webcam full path from [$entryFixedFullPath] to [$entryNewFullPath]");
		myFlvStaticHandler::clipToNewFile($entryFixedFullPath, $entryNewFullPath, 0, 0);
		$entryFullPath = $entryNewFullPath ;
				
		if(!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::RECORDED_WEBCAM_FILE_NOT_FOUND);
					
		$object_to_fill->setSourceType(KalturaSourceType::WEBCAM);
		$object_to_fill->setLocalFilePath($entryFullPath);
		return $object_to_fill;
	}
}