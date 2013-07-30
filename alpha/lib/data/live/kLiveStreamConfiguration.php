<?php
/**
 * Live stream configuration object, containing information regarding the protocol and url of the live stream. 
 * 
 * @package Core
 * @subpackage model
 *
 */
class kLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	protected $protocol;
	
	/**
	 * @var string
	 */
	protected $url;
	
	
	/**
	 * @var string
	 */
	protected $publishUrl;
	
	/**
	 * @return string $protocol
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * @param string $protocol
	 */
	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}
	
	/**
	 * @return string $url
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * @return string $publishUrl
	 */
	public function getPublishUrl() {
		return $this->publishUrl;
	}

	/**
	 * @param string $publishUrl
	 */
	public function setPublishUrl($publishUrl) {
		$this->publishUrl = $publishUrl;
	}

	/**
	 * Function extracts the first item in the array where the property $propertyName has the value $propertyValue
	 * @param entry $liveStreamEntry
	 * @param string $propertyName
	 * @param string $propertyValue
	 * @return kLiveStreamConfiguration
	 */
	public static function getSingleItemByPropertyValue ($liveStreamEntry, $propertyName, $propertyValue)
	{
		foreach ($liveStreamEntry->getLiveStreamConfigurations() as $config)
		{
			/* @var $config kLiveStreamConfiguration */
			if (property_exists(get_class($config), $propertyName))
			{
				$getter = "get{$propertyName}";
				if ($config->$getter() == $propertyValue)
				{
					return $config;
				}
			}
		}

		return null;
	}


}