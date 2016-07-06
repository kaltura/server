<?php

class DeliveryProfileAkamaiRtmp extends DeliveryProfileRtmp {
	
	function __construct() {
		parent::__construct();
		$this->FLV_FILE_EXTENSION = null;
		$this->NON_FLV_FILE_EXTENSION = null;
	}
	
	protected function doGetFlavorAssetUrl(asset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		if($this->params->getFileExtension())
			$url .= "/name/a." . $this->params->getFileExtension();
		return $url;
	}
	
	// doGetFileSyncUrl - Inherited from parent
}

