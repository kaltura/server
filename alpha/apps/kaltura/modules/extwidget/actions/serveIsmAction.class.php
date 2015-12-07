<?php
/**
 * @package Core
 * @subpackage externalWidgets
 */
class serveIsmAction extends sfAction
{
	/**
	 * @var entry
	 */
	private $entry = null;

	/**
	 * @var asset
	 */
	private $flavorAsset = null;
	
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		// where file is {entryId/flavorId}.{ism,ismc,ismv}
		
		$objectId = $type = null;
		$objectIdStr = $this->getRequestParameter( "objectId" );
		if($objectIdStr)
			list($objectId, $type) = @explode(".", $objectIdStr);
		
		if (!$type || !$objectId)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER);
			
		$ks = $this->getRequestParameter( "ks" );
		$referrer = base64_decode($this->getRequestParameter("referrer"));
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = '';
						
		$syncKey = $this->getFileSyncKey($objectId, $type);
				
		KalturaMonitorClient::initApiMonitor(false, 'extwidget.serveIsm', $this->entry->getPartnerId());
		
		myPartnerUtils::enforceDelivery($this->entry, $this->flavorAsset);
		
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
		
		if($type == 'ism')
		{
			$fileData = $this->fixIsmManifestForReplacedEntry($path);	
			$renderer = new kRendererString($fileData, 'image/ism');
			$renderer->output();
            KExternalErrors::dieGracefully();	
		}
		else 
		{
			kFileUtils::dumpFile($path);
		}
		
		
	}
	
	private function getFileSyncKey($objectId, $type)
	{
		$key = null;
		$hasVersion = strlen($objectId) != 10;
		$version = null;
		$object = null;
		$subType = null;
		$isAsset = false;
		$entryId = null;
		
		if($hasVersion)
		{
			list($objectId, $version, $subType, $isAsset, $entryId) = $this->parseObjectId($objectId);
		}

		switch ($type)
		{
			case 'ism':
				//To Remove - Until the migration process from asset sub type 3 to asset sub type 1 will be completed we need to support both formats
				if($subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET || $subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISM)
					$isAsset = true;
				else 
					$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM;
				break;
			case 'ismc':
				if($subType == flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC)
					$isAsset = true;
				if($isAsset)
					$subType = flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC;
				else
					$subType = entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC;
				break;
			case 'ismv':
			case 'isma':
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
	
	private function parseObjectId($objectIdStr)
	{
		$objectId = $version = $subType = $isAsset = $entryId = null;
		
		$parts = explode('_', $objectIdStr);
		if(count($parts) == 4)
		{
			$objectId = $parts[0].'_'.$parts[1];
			$subType = $parts[2];
			$version = $parts[3];
		}
		else if(count($parts) == 5)
		{
			$entryId = $parts[0].'_'.$parts[1];
			$objectId = $parts[2].'_'.$parts[3];
			$version = $parts[4];
			$isAsset = true;
		}	

		return array($objectId, $version, $subType, $isAsset, $entryId);
	}
	
	private function getObject($objectId, $isAsset)
	{
		if($isAsset)
		{
			$this->flavorAsset = assetPeer::retrieveById($objectId);
			if (is_null($this->flavorAsset))
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
				
			$this->entry = entryPeer::retrieveByPK($this->flavorAsset->getEntryId());
			if (is_null($this->entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			return $this->flavorAsset;
		}	
		else
		{
			$this->entry = entryPeer::retrieveByPK($objectId);
			if (is_null($this->entry))
				KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
				
			return $this->entry;
		}				
	}
	
	private function fixIsmManifestForReplacedEntry($path)
	{
		$fileData = file_get_contents($path);
		$xml = new SimpleXMLElement($fileData);
		$ismcFileName = $xml->head->meta['content'];
		list($ismcObjectId, $version, $subType, $isAsset, $entryId) = $this->parseObjectId($ismcFileName);
		
		if($entryId != $this->entry->getId())
		{
			//replacement flow
			$flavorAssets = assetPeer::retrieveByEntryIdAndStatus($this->entry->getId(), asset::ASSET_STATUS_READY);
			foreach ($flavorAssets as $asset) 
			{
				if($asset->hasTag(assetParams::TAG_ISM_MANIFEST))
				{
					list($replacingFileName, $fileName) = $this->getReplacedAndReplacingFileNames($asset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ISMC);
					if($replacingFileName && $fileName)
						$fileData = str_replace("content=\"$replacingFileName\"", "content=\"$fileName\"", $fileData);			
				}
				else
				{
					list($replacingFileName, $fileName) = $this->getReplacedAndReplacingFileNames($asset, flavorAsset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
					if($replacingFileName && $fileName)					
						$fileData = str_replace("src=\"$replacingFileName\"", "src=\"$fileName\"", $fileData);
				}
			}			
			return $fileData;
		}
		else
			return $fileData;	
	}
	
	private function getReplacedAndReplacingFileNames($asset, $fileSyncObjectSubType)
	{
		$replacingFileName = null;
		$fileName = null;
		$syncKey = $asset->getSyncKey($fileSyncObjectSubType);
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey , true);
		if($fileSync)			
		{
			$replacingFileName = basename($fileSync->getFilePath());
			$fileExt = pathinfo($fileSync->getFilePath(), PATHINFO_EXTENSION);
			$fileName = $asset->getEntryId().'_'.$asset->getId().'_'.$fileSync->getVersion().'.'.$fileExt;
		}
		return array($replacingFileName, $fileName);
	}
}
