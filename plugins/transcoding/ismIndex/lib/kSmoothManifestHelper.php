<?php

class kSmoothManifestHelper
{
	public static function mergeManifestFiles($entryId, $assetTag, $ismEntryFileSyncSubType, $ismcEntryFileSyncSubType)
	{
		$entry = entryPeer::retrieveByPK($entryId); 
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $entryId);
		
		$flavorAssets = assetPeer::retrieveFlavorsByEntryId($entryId);
		$flavorAssets = assetPeer::filterByTag($flavorAssets, $assetTag);
		
		$ismFiles = array();
		$ismcFiles = array();
		
		foreach ($flavorAssets as $flavorAsset) 
		{
			if(	$flavorAsset->getStatus() != flavorAsset::ASSET_STATUS_READY && 
				$flavorAsset->getStatus() != flavorAsset::ASSET_STATUS_NOT_APPLICABLE)
				return true;	

			if( $flavorAsset->getStatus() == flavorAsset::ASSET_STATUS_READY )
			{
				$ismFilePath = self::getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
				$ismcFilePath = self::getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
				$ismvFilePath = self::getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				
				if($ismFilePath)
					$ismFiles[] = array($ismFilePath, $ismvFilePath);
				if($ismcFilePath) 
					$ismcFiles[] = $ismcFilePath; 
			}			
		}
		
		if(!count($ismcFiles))
			return true;
			
		try 
		{
			$ismcMerged = self::mergeIsmcManifests($ismcFiles);				
			$ismVersion = $entry->incrementIsmVersion($ismEntryFileSyncSubType);
			$ismcSyncKey = $entry->getSyncKey($ismcEntryFileSyncSubType, $ismVersion);
			kFileSyncUtils::file_put_contents($ismcSyncKey, $ismcMerged);
			$entry->save();
					
			$ismMerged = self::mergeIsmManifests($ismFiles, kFileSyncUtils::getLocalFilePathForKey($ismcSyncKey));
			$ismSyncKey = $entry->getSyncKey($ismEntryFileSyncSubType, $ismVersion);
			kFileSyncUtils::file_put_contents($ismSyncKey, $ismMerged);			
		}
		catch (kFileSyncException $e)
		{
			KalturaLog::debug("File Sync key already exists, skipping");
		}
	}
	
	private static function getFilePath($flavorAsset, $subType)
	{
		$syncKey = $flavorAsset->getSyncKey($subType);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		if($fileSync)
			return $fileSync->getFullPath(); 
		else 
			return null;
	}
	
	private static function mergeIsmManifests(array $filePaths, $targetIsmcPath)
	{
		$root = null;
		foreach ($filePaths as $filePathPair) 
		{
			list($ismFilePath, $ismvFilePath) = $filePathPair;
			if($ismFilePath)
			{
				$xml = new SimpleXMLElement(file_get_contents($ismFilePath));
				if(isset($xml->body->switch->video)) $xml->body->switch->video['src'] = basename($ismvFilePath);
  				if(isset($xml->body->switch->audio)) $xml->body->switch->audio['src'] = basename($ismvFilePath);
  				
  				if(!$root)
  				{
  					$root = $xml;
  				}
  				else 
  				{
   					if(isset($xml->body->switch->video)) KDLUtils::AddXMLElement($root->body->switch, $xml->body->switch->video);
  					if(isset($xml->body->switch->audio)) KDLUtils::AddXMLElement($root->body->switch, $xml->body->switch->audio); 					
  				}				
			}

		} 		
 		$root->head->meta['content'] = basename($targetIsmcPath);
 		return $root->asXML();
	}
	
	private static function mergeIsmcManifests(array $filePaths)
	{
		$root = null;
		foreach ($filePaths as $filePath) 
		{
			if($filePath)
			{
				$xml = new SimpleXMLElement(file_get_contents($filePath));
				if(!$root)
  				{
  					$root = $xml;
  				}
				else
				{
			  		for($strIdx=0; $strIdx<count($xml->StreamIndex); $strIdx++) 
	  				{
	   					self::addQualityLevel($root->StreamIndex[$strIdx], $xml->StreamIndex[$strIdx]->QualityLevel);
	  				}
				} 				
			}
		} 		
 		return $root->asXML(); 		
	}

	private static function addQualityLevel(SimpleXMLElement $dest, SimpleXMLElement $source)
	{
 		$tmp = new SimpleXMLElement($dest->saveXML());
 		unset($dest->c);
 		KDLUtils::AddXMLElement($dest, $source);
 		$index  = count($tmp->QualityLevel);
 		$dest->QualityLevel[$index]['Index'] = $index;
 		foreach ($tmp->c as $obj)
 		{
  			KDLUtils::AddXMLElement($dest, $obj);
 		}
 		$dest['QualityLevels'] = $index+1;
	}
}