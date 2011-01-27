<?php
class kLocalPathUrlManager extends kUrlManager
{
	/**
	 * Returns the local path with no extension
	 * 
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$url = $fileSync->getFilePath();
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
			$url = preg_replace('/\.[\w]+$/', '', $url);
		
		return $url;
	}
	
	/**
	 * Returns the url for the given flavor. in case of rtmp, return the file path in order ot use a local streaming server
	 * 
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		if($this->protocol != StorageProfile::PLAY_FORMAT_RTMP)
			return parent::getFlavorAssetUrl($flavorAsset);
                	
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->getFileSyncUrl($fileSync);
		if ($this->extention && strtolower($this->extention) != 'flv' ||
			$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
		{
			$url = "mp4:$url";
		}

		$url = str_replace('\\', '/', $url);
		return $url;
	}
}