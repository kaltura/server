<?php

abstract class DeliveryServerNode extends ServerNode {
	
	abstract public function getManifestUrl($protocol = 'http', $format = null);
	abstract public function getPlaybackHost($protocol = 'http', $format = null, $deliveryType = null);
	
	public function getPlaybackDomain()
	{
		$playbackHostName = $this->playback_host_name;
		
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
		
		return $playbackHostName;
	}

} // DeliveryServerNode
