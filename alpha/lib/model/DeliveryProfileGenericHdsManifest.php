<?php

class DeliveryProfileGenericHdsManifest extends DeliveryProfileGenericHds {

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