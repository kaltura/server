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
			die;
			
		$ks = $this->getRequestParameter( "ks" );
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';
			
		if ($type == "ism" || $type == "ismc")
		{
			// backward compatiblity - to be removed once ismc is created with pure objectId.ext instead of entryId_flavorId_version.ext
			if (strlen($objectId) != 10)
			{
				$version = substr($objectId, 13);
				$objectId = substr($objectId, 0, 10);
			}
			else
				$version = null;
			
			$entry = entryPeer::retrieveByPK($objectId);
			if (is_null($entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			$syncKey = $entry->getSyncKey($type == "ism" ? entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM : entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC, $version);
		}
		else if ($type == "ismv")
		{
			// backward compatiblity - to be removed once ismc is created with pure objectId.ext instead of entryId_flavorId_version.ext
			if (strlen($objectId) != 10)
			{
				$version = substr($objectId, 22);
				$objectId = substr($objectId, 11, 10);
			}
			else
				$version = null;
				
			$flavorAsset = assetPeer::retrieveById($objectId);
			if (is_null($flavorAsset))
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				
			$entry = entryPeer::retrieveByPK($flavorAsset->getEntryId());
			if (is_null($entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET, $version);
		}
		else
		{
			die;
		}
				
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
}
