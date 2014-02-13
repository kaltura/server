<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveIsmAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		// where file is {entryId/flavorId}.{ism,ismc,ismv}
		
		$objectId = $type = null;
		$objectIdStr = $this->getRequestParameter( "objectId" );
		list($objectId, $type) = @explode(".", $objectIdStr);
		
		if (!$type || !$objectId)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER);
			
		$ks = $this->getRequestParameter( "ks" );
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';
						
		$syncKey = $this->getFileSyncKey($objectId, $type);
		if (!kFileSyncUtils::file_exists($syncKey, false))
		{
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			
			if (is_null($fileSync))
			{
				KalturaLog::log("Error - no FileSync for type [$type] objectId [$objectId]");
				KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			}
			
			$remoteUrl = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			kFileUtils::dumpUrl($remoteUrl);
		}
		
		$path = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
		
		kFileUtils::dumpFile($path);
	}
	
	private function getFileSyncKey($objectId, $type)
	{
		$key = null;
		$hasVersion = strlen($objectId) != 10;
		$version = null;
		$object = null;
		$subType = null;
		$isAsset = false;
		
		if($hasVersion)
		{
			$parts = explode('_', $objectId);
			if(count($parts) == 4)
			{
				$objectId = $parts[0].'_'.$parts[1];
				$subType = $parts[2];
				$version = $parts[3];
				
				KalturaLog::debug('objectId: '.$objectId.', subType: '.$subType.', version: '.$version);
			}
			else if(count($parts) == 5)
			{
				$objectId = $parts[2].'_'.$parts[3];
				$version = $parts[4];
				$isAsset = true;
				
				KalturaLog::debug('objectId: '.$objectId.', version: '.$version);
			}				
		}

		switch ($type)
		{
			case 'ism':
				if($subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM)
					$isAsset = true;
				else 
					$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM;
				break;
			case 'ismc':
				if($subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC)
					$isAsset = true;
				if($isAsset)
					$subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC;
				else
					$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC;
				break;
			case 'ismv':
				$subType = flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET;
				$isAsset = true;
				break;
			default:
				KExternalErrors::dieError(KExternalErrors::INVALID_ISM_FILE_TYPE);
		}
		
		$object = $this->getObject($objectId, $isAsset);
		if(!$object)
			KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
			
		
		$key = $object->getSyncKey($subType, $version);
		return $key;
	}
	
	private function getObject($objectId, $isAsset)
	{
		if($isAsset)
		{
			$flavorAsset = assetPeer::retrieveById($objectId);
			if (is_null($flavorAsset))
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				
			$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
			if (is_null($entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			return $flavorAsset;
		}	
		else
		{
			$entry = entryPeer::retrieveByPK($objectId);
			if (is_null($entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
				return $entry;
		}				
	}
}
