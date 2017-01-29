<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushNotificationData extends KalturaObject
{
	/**
	 * @var string
	 */
	public $key;
	
	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $clientId;

	/**
	 * @var exception
	 */
	public $errorMessage;


	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getClientId()
	{
		return $this->clientId;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId)
	{
		$this->clientId = $clientId;
	}


}