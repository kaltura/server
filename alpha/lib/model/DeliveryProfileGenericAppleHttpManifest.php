<?php

class DeliveryProfileGenericAppleHttpManifest extends DeliveryProfileGenericAppleHttp {

	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kRedirectManifestRenderer';
	}
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		if ($this->params->getManifestFileSync())
		{
			$this->initDeliveryDynamicAttributes($this->params->getManifestFileSync());
			$url = $this->getFileSyncUrl($this->params->getManifestFileSync(), false);
			$manifestInfo = $this->getFlavorAssetInfo($url);
			return $this->getRenderer(array($manifestInfo));
		} else {
			KalturaLog::debug("No manifest file was found");
			return null;
		}
	}
	
}