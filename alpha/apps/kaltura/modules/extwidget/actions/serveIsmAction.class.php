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
		if($type == 'ism' || $type == 'ismc')
		{
			if ($hasVersion)
			{
				$version = substr($objectId, 13);
				$subType = substr($objectId, 11, 12);
				$objectId = substr($objectId, 0, 10);
				KalturaLog::debug('objectId: '.$objectId.', subType: '.$subType.', version: '.$version);
			}
			if($subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM || $subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC)
			{
				$object = $this->getObject($objectId, true);
			}	
			else
			{
				$subType = $type == "ism" ? entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM : entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC;
				$object = $this->getObject($objectId, false);
			}		
		}
		else if ($type == 'ismv')
		{
			if ($hasVersion)
			{
				$version = substr($objectId, 22);
				$objectId = substr($objectId, 11, 10);
			}
			$subType = flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET;
			$object = $this->getObject($objectId, true);
		}
		else
		{
			KExternalErrors::dieError(KExternalErrors::INVALID_ISM_FILE_TYPE);
		}
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
