<?php

class downloadAction extends sfAction
{
	/**
	 * Will forward to the regular swf player according to the widget_id 
	 */
	public function execute()
	{
		$entryId = $this->getRequestParameter("entry_id");
		$flavorId = $this->getRequestParameter("flavor");
		$fileName = $this->getRequestParameter("file_name");
		$ksStr = $this->getRequestParameter("ks");
		$referrer = $this->getRequestParameter("referrer");
		$referrer = base64_decode($referrer);
		if (!is_string($referrer)) // base64_decode can return binary data
			$referrer = "";
		// get entry
		$entry = entryPeer::retrieveByPK($entryId);
		if (is_null($entry))
			KExternalErrors::dieError(KExternalErrors::ENTRY_NOT_FOUND);
			
		myPartnerUtils::blockInactivePartner($entry->getPartnerId());
			
		$securyEntryHelper = new KSecureEntryHelper($entry, $ksStr, $referrer);
		$securyEntryHelper->validateForDownload($entry, $ksStr);
		
		$flavorAsset = null;

		if ($flavorId) 
		{
			// get flavor asset
			$flavorAsset = flavorAssetPeer::retrieveById($flavorId);
			if (is_null($flavorAsset) || $flavorAsset->getStatus() != flavorAsset::FLAVOR_ASSET_STATUS_READY)
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
			
			// the request flavor should belong to the requested entry
			if ($flavorAsset->getEntryId() != $entryId)
				KExternalErrors::dieError(KExternalErrors::FLAVOR_NOT_FOUND);
		}
		else // try to find some flavor
		{
			$flavorAsset = flavorAssetPeer::retrieveBestPlayByEntryId($entry->getId());
		}

		// Gonen 26-04-2010: in case entry has no flavor with 'mbr' tag - we return the source
		if(!$flavorAsset && ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_VIDEO || $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUDIO))
		{
			$flavorAsset = flavorAssetPeer::retrieveOriginalByEntryId($entryId);
		}
		
		if ($flavorAsset)
		{
			$syncKey = $this->getSyncKeyAndForFlavorAsset($entry, $flavorAsset);
		}
		else
		{
			$syncKey = $this->getBestSyncKeyForEntry($entry);
		}
		
		list($fileBaseName, $fileExt) = $this->getFileName($entry, $flavorAsset);

		if (!$fileName)
			$fileName = $fileBaseName;
			
		if ($fileExt)
			$fileName = $fileName . '.' . $fileExt;
			
		if (is_null($syncKey))
			KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			
		$this->handleFileSyncRedirection($syncKey);

		$filePath = kFileSyncUtils::getReadyLocalFilePathForKey($syncKey);
		$this->dumpFile($filePath, $fileName);
		
		die(); // no view
	}
	
	private function getFileName(entry $entry, flavorAsset $flavorAsset = null)
	{
		$fileExt = "";
		$fileBaseName = $entry->getName();
		if ($flavorAsset)
		{
			$flavorParams = $flavorAsset->getFlavorParams();
			if ($flavorParams)
				$fileBaseName = ($fileBaseName . " (" . $flavorParams->getName() . ")");
					
			$fileExt = $flavorAsset->getFileExt();
		}
		else
		{
			$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
			list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
			if ($fileSync)
				$fileExt = $fileSync->getFileExt();
		}
		
		return array($fileBaseName, $fileExt);
	}
	
	private function getSyncKeyAndForFlavorAsset(entry $entry, flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		return $syncKey;
	}
	
	private function getBestSyncKeyForEntry(entry $entry)
	{
		$entryType = $entry->getType();
		$entryMediaType = $entry->getMediaType();
		$syncKey = null;
		switch($entryType)
		{
			case entry::ENTRY_TYPE_MEDIACLIP: 
				switch ($entryMediaType)
				{
					case entry::ENTRY_MEDIA_TYPE_IMAGE:
						$syncKey = $entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
						break;
				}
				break;
		}
		
		return $syncKey;
	}
	
	private static function encodeUrl($url)
	{
		return str_replace(array(' ', '[', ']'), array('%20', '%5B', '%5D'), $url);
	}
	
	private function dumpFile($file_path, $file_name)
	{
		$relocate = $this->getRequestParameter("relocate");
		$directServe = $this->getRequestParameter("direct_serve");

		if (!$relocate)
		{
			$url = $_SERVER["REQUEST_URI"];
			if (strpos($url, "?") !== false) // when query string exists, just remove it (otherwise it might cause redirect loops)
			{
				$url = substr($url, 0, strpos($url, "?"));
			}
				
			$url .= "/relocate/" . $this->encodeUrl($file_name);
			header("Location: {$url}");
			die;
		}
		else
		{
			if(!$directServe)
				header("Content-Disposition: attachment; filename=\"$file_name\"");
				
			$mime_type = kFile::mimeType($file_path);
			kFile::dumpFile($file_path, $mime_type);
		}
	}
	
	private function handleFileSyncRedirection(FileSyncKey $syncKey)
	{
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		
		if (is_null($fileSync))
			KExternalErrors::dieError(KExternalErrors::FILE_NOT_FOUND);
			
		if (!$local)
		{
			$remote_url = kDataCenterMgr::getRedirectExternalUrl($fileSync);
			$this->redirect($remote_url);
		}
	}
}
?>
