<?php

class kObjectCopyHandler implements kObjectCopiedEventConsumer
{
	protected static $idsMap = array();

	public static function mapIds($className, $fromId, $toId)
	{
		if(!isset(self::$idsMap[$className]))
			self::$idsMap[$className] = array();
			
		self::$idsMap[$className][$fromId] = $toId;
	}
	
	public static function getMappedId($className, $fromId)
	{
		if(!isset(self::$idsMap[$className]) || !isset(self::$idsMap[$className][$fromId]))
			return null;
			
		return self::$idsMap[$className][$fromId];
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		return true;
	}
	
	/**
	 * @param uiConf $fromObject
	 * @param uiConf $toObject
	 */
	protected function uiConfCopied(uiConf $fromObject, uiConf $toObject)
	{
		$fileAssets = FileAssetPeer::retrieveByObject(FileAssetObjectType::UI_CONF, $fromObject->getId());
		foreach($fileAssets as $fileAsset)
		{
			/* @var $fileAsset FileAsset */
				
			$newFileAssets = $fileAsset->copy();
			$newFileAssets->setObjectId($toObject->getId());
			$newFileAssets->setVersion(1);
			$newFileAssets->save();
			
			$syncKey = $fileAsset->getSyncKey(FileAsset::FILE_SYNC_ASSET);
			$newSyncKey = $newFileAssets->getSyncKey(FileAsset::FILE_SYNC_ASSET);
			
			if(kFileSyncUtils::fileSync_exists($syncKey))
				kFileSyncUtils::softCopy($syncKey, $newSyncKey);
		}
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof asset)
		{
			self::mapIds('asset', $fromObject->getId(), $toObject->getId());
		
			$flavorParamsId = self::getMappedId('assetParams', $fromObject->getFlavorParamsId());
			if($flavorParamsId)
			{
				$toObject->setFlavorParamsId($flavorParamsId);
				$toObject->save();
			}
		}
		elseif($fromObject instanceof assetParams)
		{
			self::mapIds('assetParams', $fromObject->getId(), $toObject->getId());
		}
		elseif($fromObject instanceof assetParamsOutput)
		{
			self::mapIds('assetParamsOutput', $fromObject->getId(), $toObject->getId());
		
			$flavorParamsId = self::getMappedId('assetParams', $fromObject->getFlavorParamsId());
			if($flavorParamsId)
			{
				$toObject->setFlavorParamsId($flavorParamsId);
				$toObject->save();
			}
		}
		else
		{
			self::mapIds(get_class($fromObject), $fromObject->getId(), $toObject->getId());
		}
		
		if($fromObject instanceof uiConf)
			$this->uiConfCopied($fromObject, $toObject);
		
		if($fromObject instanceof category && $fromObject->getParentId())
		{
			$parentId = self::getMappedId('category', $fromObject->getParentId());
			if($parentId)
			{
				$toObject->setParentId($parentId);
				$toObject->save();
			}
		}
		
		if($fromObject instanceof entry)
		{
			$conversionProfileId = self::getMappedId('conversionProfile2', $fromObject->getConversionProfileId());
			if($conversionProfileId)
			{
				$toObject->setConversionProfileId($conversionProfileId);
				$toObject->save();
			}
		
			$accessControlId = self::getMappedId('accessControl', $fromObject->getAccessControlId());
			if($accessControlId)
			{
				$toObject->setAccessControlId($accessControlId);
				$toObject->save();
			}
			
			if ($toObject->getPartnerId() == $fromObject->getPartnerId()){  
				$categoryEntriesObjects = categoryEntryPeer::retrieveActiveByEntryId($fromObject->getId());
				$categoryIds = array();
				foreach ($categoryEntriesObjects as $categoryEntryObject)
				{
					/* @var $categoryEntry categoryEntry */ 
					$categoryIds[] = $categoryEntryObject->getCategoryId();
				}
				if (count($categoryIds)){
			  		$categories = categoryPeer::retrieveByPKs($categoryIds); //which will return only the entiteled ones
			  		foreach ($categories as $category){
			  			/* @var $category category */
			  			$categoryEntry = new categoryEntry();
		                $categoryEntry->setEntryId($toObject->getId());
		                $categoryEntry->setCategoryId($category->getId());
		                $categoryEntry->setStatus(CategoryEntryStatus::ACTIVE);
		                $categoryEntry->setPartnerId($toObject->getPartnerId());
		                $categoryEntry->save();	  			
			  		}
				}
			}
		}
		
		return true;
	}
}