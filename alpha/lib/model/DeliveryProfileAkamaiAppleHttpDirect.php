<?php

class DeliveryProfileAkamaiAppleHttpDirect extends DeliveryProfileAkamaiAppleHttp {
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$path = parent::doGetFileSyncUrl($fileSync);
	
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return $path;
	
		if(!is_null($this->getHostName()))
			return $this->formatHdIos($path);
	
		return $path;
	}
}

