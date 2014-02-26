<?php

/**
 * TODO 
 * @_!! 
 * Consider removing this class since it is used only by akamai
 */
class DeliveryProfileHd extends DeliveryProfileVod {
	
	protected $DEFAULT_RENDERER_CLASS = 'kSmilManifestRenderer';
	
	/**
	 * @return kManifestRenderer
	 */
	public function serve()
	{
		$flavors = $this->buildHttpFlavorsArray();
		
		// When playing HDS with Akamai HD the bitrates in the manifest must be unique
		$this->ensureUniqueBitrates($flavors); 

		return $this->getRenderer($flavors);
	}
	
	protected function ensureUniqueBitrates(array &$flavors)
	{
		$seenBitrates = array();
		foreach ($flavors as &$flavor)
		{
			while (in_array($flavor['bitrate'], $seenBitrates))
			{
				$flavor['bitrate']++;
			}
			$seenBitrates[] = $flavor['bitrate'];
		}
	}
}

