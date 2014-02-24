<?php

class DeliveryHds extends DeliveryVod {
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

