<?php

abstract class DeliveryServerNode extends ServerNode {
	
	abstract public function getManifestUr($protocol = 'http');
	abstract public function getPlaybackHost();
	
	public function getPlaybackHostName()
	{
		$playbackHostName = $this->playback_host_name;
		
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
		
		return $playbackHostName;
	}

} // DeliveryServerNode
