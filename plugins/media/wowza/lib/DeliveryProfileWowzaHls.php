<?php
/**
 * @package plugins.uplynk
 * @subpackage storage
 */
class DeliveryProfileWowzaHls extends DeliveryProfileAppleHttp
{
	public function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
		$postfix = "/playlist.m3u8";
		return $path.$postfix;
	}
	
}
