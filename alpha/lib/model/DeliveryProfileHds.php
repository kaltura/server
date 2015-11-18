<?php

class DeliveryProfileHds extends DeliveryProfileVod {
	
	function __construct() {
		parent::__construct();
		$this->DEFAULT_RENDERER_CLASS = 'kF4MManifestRenderer';
	}
	
	/**
	 * @return array $flavors
	 */
	public function buildServeFlavors()
	{
		$flavors = $this->buildHttpFlavorsArray();
		$flavors = $this->sortFlavors($flavors);
		
		return $flavors;
	}
}

