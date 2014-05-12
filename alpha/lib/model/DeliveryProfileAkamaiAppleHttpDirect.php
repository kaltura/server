<?php

class DeliveryProfileAkamaiAppleHttpDirect extends DeliveryProfileAkamaiAppleHttp {
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
	
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return $path;
	
		return $this->formatHdIos($path);
	}
}

