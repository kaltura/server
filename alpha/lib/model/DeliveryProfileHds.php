<?php

class DeliveryProfileHds extends DeliveryProfileVod {
	protected $DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		return $this->getRenderer($flavors);
	}
}

