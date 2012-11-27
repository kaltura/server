<?php
/**
 * @package Core
 * @subpackage storage.FMS
 */
class kFmsUrlManager extends kUrlManager
{
	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$url = parent::doGetFileSyncUrl($fileSync);
		$url = ltrim($url, '/');

		switch ($this->protocol)
		{
		case PlaybackProtocol::APPLE_HTTP:
			return "/hls-vod/{$url}.m3u8";
		
		case PlaybackProtocol::HDS:
			return "/hds-vod/{$url}.f4m";
		
		default:
			return $url;
		}
	}
}
