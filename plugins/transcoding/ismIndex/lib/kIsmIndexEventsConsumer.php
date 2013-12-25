<?php
class kIsmIndexEventsConsumer implements kObjectChangedEventConsumer
{	
		/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if(
			$object instanceof flavorAsset
			&&	in_array(assetPeer::STATUS, $modifiedColumns)
			&&  $object->getStatus() == flavorAsset::ASSET_STATUS_READY
			&&  $object->hasTag(assetParams::TAG_ISM)
		)
			return true;
			
		return false;
	}

		/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{	
		$flavorParams = assetParamsPeer::retrieveByPKNoFilter($object->getFlavorParamsId());
		if($flavorParams && $flavorParams->getConversionEngines() != conversionEngineType::EXPRESSION_ENCODER3)	
			$this->mergeManifestFiles($object->getEntryId());
							
		return true;
	}
	
	private function mergeManifestFiles($entryId)
	{
		$entry = entryPeer::retrieveByPK($entryId); 
		if(!$entry)
			throw new APIException(APIErrors::INVALID_ENTRY, $entryId);
		
		$flavorAssets = assetPeer::retrieveFlavorsByEntryId($entryId);
		$flavorAssets = assetPeer::filterByTag($flavorAssets, assetParams::TAG_ISM);
		
		$ismFiles = array();
		$ismcFiles = array();
		
		foreach ($flavorAssets as $flavorAsset) 
		{
			if(	$flavorAsset->getStatus() != flavorAsset::ASSET_STATUS_READY && 
				$flavorAsset->getStatus() != flavorAsset::ASSET_STATUS_NOT_APPLICABLE)
				return true;	
					
			$ismFiles[] = $this->getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
			$ismcFiles[] = $this->getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
			
		}
		
		try 
		{
			$ismcMerged = $this->mergeIsmcManifests($ismcFiles);				
			$ismVersion = $entry->getIsmVersion();
			$ismcSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC, $ismVersion);
			kFileSyncUtils::file_put_contents($ismcSyncKey, $ismcMerged);
					
			$ismMerged = $this->mergeIsmManifests($ismFiles, kFileSyncUtils::getLocalFilePathForKey($ismcSyncKey));
			$ismSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM, $ismVersion);
			kFileSyncUtils::file_put_contents($ismSyncKey, $ismMerged);
		}
		catch (kFileSyncException $e)
		{
			KalturaLog::debug("File Sync key already exists, skipping");
		}
	}
	
	private function getFilePath($flavorAsset, $subType)
	{
		$syncKey = $flavorAsset->getSyncKey($subType);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		if($fileSync)
			return $fileSync->getFullPath(); 
		else 
			return null;
	}
	
	private function mergeIsmManifests(array $filePaths, $targetIsmcPath)
	{
		$root = new SimpleXMLElement(file_get_contents(current($filePaths)));
		while(true) 
		{
  			$nextFilePath = next($filePaths);
  			if($nextFilePath == false)
  			{
   				break;
  			}
  			$xml = new SimpleXMLElement(file_get_contents($nextFilePath));
  			
  			$xml->body->switch->video['src'] = basename($nextFilePath);
  			$xml->body->switch->audio['src'] = basename($nextFilePath);
  			
  			KDLUtils::AddXMLElement($root->body->switch, $xml->body->switch->video);
  			KDLUtils::AddXMLElement($root->body->switch, $xml->body->switch->audio);
  			$root->head->meta['content'] = basename($targetIsmcPath);
 		}
 		
 		return $root->asXML();
	}
	
	private function mergeIsmcManifests(array $filePaths)
	{
		$root = new SimpleXMLElement(file_get_contents(current($filePaths)));
		while(true) 
		{
  			$nextFilePath = next($filePaths);
  			if($nextFilePath == false)
  			{
   				break;
  			}
  			$xml = new SimpleXMLElement(file_get_contents($nextFilePath));
  			
		  	for($strIdx=0; $strIdx<count($xml->StreamIndex); $strIdx++) 
  			{
   				$this->addQualityLevel($rootIsmc->StreamIndex[$strIdx], $xml->StreamIndex[$strIdx]->QualityLevel);
  			}
 		}
		return $root->asXML();
	}

	private function addQualityLevel(SimpleXMLElement $dest, SimpleXMLElement $source)
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