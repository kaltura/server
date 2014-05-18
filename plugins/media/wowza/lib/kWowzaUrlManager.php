<?php
/**
 * @package plugins.wowza
 * @subpackage lib
 */
class kWowzaUrlManager extends kUrlManager
{
	public function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);

		$postfix = '';
		switch ($this->protocol)
		{
			case PlaybackProtocol::HDS:
				$postfix = "/playmanifest.f4m";
				break;
			case PlaybackProtocol::HLS:
			case PlaybackProtocol::APPLE_HTTP:
				$postfix = "/playlist.m3u8";
				break;
		}
		
		return $path.$postfix;
	}
	
	public function getRendererClass()
	{
		switch ($this->protocol)
		{
			case PlaybackProtocol::HLS:
			case PlaybackProtocol::APPLE_HTTP:
				return 'kRedirectManifestRenderer';
		}
	}
}