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
		$url = trim($url, '/');
		
		switch ($this->protocol)
		{
		case PlaybackProtocol::APPLE_HTTP:
			$pattern = isset($this->params["hls_pattern"]) ? $this->params["hls_pattern"] : '/hls-vod/{url}.m3u8';
			break;
		
		case PlaybackProtocol::HDS:
			$pattern = isset($this->params["hds_pattern"]) ? $this->params["hds_pattern"] : '/hds-vod/{url}.f4m';
			break;
			
		default:
			$pattern = isset($this->params["default_pattern"]) ? $this->params["default_pattern"] : '{url}'; 
			break;
		}
		
		return str_replace('{url}', $url, $pattern);
	}
}
