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
			&& 	!$object->getentry()->getReplacingEntryId()
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

			if( $flavorAsset->getStatus() == flavorAsset::ASSET_STATUS_READY )
			{
				$ismFilePath = $this->getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM);
				$ismcFilePath = $this->getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
				$ismvFilePath = $this->getFilePath($flavorAsset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
				
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
			$ismcMerged = $this->mergeIsmcManifests($ismcFiles);				
			$ismVersion = $entry->incrementIsmVersion();
			$ismcSyncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC, $ismVersion);
			kFileSyncUtils::file_put_contents($ismcSyncKey, $ismcMerged);
			$entry->save();
					
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
	
	private function mergeIsmcManifests(array $filePaths)
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
	   					$this->addQualityLevel($root->StreamIndex[$strIdx], $xml->StreamIndex[$strIdx]->QualityLevel);
	  				}
				} 				
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