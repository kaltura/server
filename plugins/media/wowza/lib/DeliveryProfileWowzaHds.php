<?php
/**
 * @package plugins.uplynk
 * @subpackage storage
 */
class DeliveryProfileWowzaHds extends DeliveryProfileHds
{
	public function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
		$postfix = "/playmanifest.f4m";
		return $path.$postfix;
	}
	
}
