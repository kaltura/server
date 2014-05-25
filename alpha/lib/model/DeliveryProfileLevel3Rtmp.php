<?php

class DeliveryProfileLevel3Rtmp extends DeliveryProfileRtmp {
	
	function __construct() {
		parent::__construct();
		$this->FLV_FILE_EXTENSION = null;
		$this->NON_FLV_FILE_EXTENSION = null;
		$this->REDUNDANT_EXTENSIONS = array(".flv");
	}
	
	// doGetFlavorAssetUrl - Inherit from parent
	
}

