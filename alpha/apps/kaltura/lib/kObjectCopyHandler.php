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
			$newFileAssets->incrementVersion();
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
		if ($fromObject instanceof FileAsset)
		{
			$syncKey = $fromObject->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
			$newSyncKey = $toObject->getSyncKey(asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);

			if(kFileSyncUtils::fileSync_exists($syncKey))
			{
				kFileSyncUtils::softCopy($syncKey, $newSyncKey);
			}
		}
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


			/**
			 * @var entry $toObject
			 */
			if ( ($toObject->getPartnerId() == $fromObject->getPartnerId()) && ($toObject->shouldCloneByProperty(BaseEntryCloneOptions::CATEGORIES) === true) ){  
				$categoryEntriesObjects = categoryEntryPeer::retrieveActiveAndPendingByEntryId($fromObject->getId());
				$categoryIds = array();
				$categoryEntryStatusById = array();
				foreach ($categoryEntriesObjects as $categoryEntryObject)
				{
					/* @var $categoryEntry categoryEntry */
					$categoryId = $categoryEntryObject->getCategoryId();
					$categoryIds[] = $categoryId;
					$categoryEntryStatusById[$categoryId] = $categoryEntryObject->getStatus();
				}
				if (count($categoryIds)){
			  		$categories = categoryPeer::retrieveByPKs($categoryIds); //which will return only the entiteled ones
			  		foreach ($categories as $category){
			  			/* @var $category category */
			  			$categoryEntry = new categoryEntry();
		                $categoryEntry->setEntryId($toObject->getId());
		                $categoryEntry->setCategoryId($category->getId());
		                $categoryEntry->setStatus($categoryEntryStatusById[$category->getId()]);
		                $categoryEntry->setPartnerId($toObject->getPartnerId());
		                $categoryEntry->save();	  			
			  		}
				}
			}
		}
		
		return true;
	}
}