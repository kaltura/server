<?php
/**
 * @package Core
 * @subpackage storage
 */
class kLocalPathUrlManager extends kUrlManager
{
	/**
	 * Returns the local path with no extension
	 * 
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$url = $fileSync->getFilePath();
		$url = str_replace('\\', '/', $url);
		
		if($this->protocol == PlaybackProtocol::RTMP)
			$url = preg_replace('/\.[\w]+$/', '', $url);
		
		if ($this->protocol == PlaybackProtocol::APPLE_HTTP)
			return $fileSync->getFilePath()."/playlist.m3u8";
		
		return $url;
	}
	
	/**
	 * Returns the url for the given flavor. in case of rtmp, return the file path in order ot use a local streaming server
	 * 
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		if($this->protocol != PlaybackProtocol::RTMP)
			return parent::doGetFlavorAssetUrl($flavorAsset);
                	
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
		$url = $this->doGetFileSyncUrl($fileSync);
		if ($this->extention && strtolower($this->extention) != 'flv' ||
			$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
		{
			$url = "mp4:$url";
		}

		$url = str_replace('\\', '/', $url);
		return $url;
	}
}