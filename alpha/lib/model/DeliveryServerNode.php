<?php

abstract class DeliveryServerNode extends ServerNode {

	const CUSTOM_DATA_DELIVERY_IDS = "delivery_profile_ids";
	const CUSTOM_DATA_CONFIG = "config";
	
	public function getPlaybackDomain()
	{
		$playbackHostName = $this->playback_host_name;
		
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
		
		return $playbackHostName;
	}

	/* Delivery Settings */
	public function setDeliveryProfileIds($params)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DELIVERY_IDS, $params);
	}

	public function getDeliveryProfileIds()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DELIVERY_IDS, null, array());
	}

	/* Config Settings */
	public function setConfig($config)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CONFIG, $config);
	}

	public function getConfig()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONFIG, null, null);
	}

} // DeliveryServerNode
