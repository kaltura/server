<?php

class DeliveryProfileAkamaiRtmp extends DeliveryProfileRtmp {
	
	function __construct() {
		parent::__construct();
		$this->FLV_FILE_EXTENSION = null;
		$this->NON_FLV_FILE_EXTENSION = null;
	}
	
	// doGetFlavorAssetUrl - Inherited from parent with different fallback.
	// doGetFileSyncUrl - Inherited from parent
}

