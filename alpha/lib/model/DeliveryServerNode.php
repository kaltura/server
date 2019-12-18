<?php

abstract class DeliveryServerNode extends ServerNode {

	const CUSTOM_DATA_DELIVERY_IDS = "delivery_profile_ids";
	const CUSTOM_DATA_CONFIG = "config";

	abstract public function getPlaybackHost($protocol = 'http', $format = null, $baseUrl = null, $deliveryType = null);
	
	public function getPlaybackDomain()
	{
		$playbackHostName = $this->playback_host_name;
		
		if(!$playbackHostName)
			$playbackHostName = $this->host_name;
		
		return $playbackHostName;
	}

	public function getManifestUrl($protocol = 'http', $format = null)
	{
		$playbackHost = $this->getPlaybackHost($protocol, $format);
		return $protocol . '://' . rtrim($playbackHost, '/') . '/';
	}

	public function getAppNameAndPrefix()
	{
		return '';
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
