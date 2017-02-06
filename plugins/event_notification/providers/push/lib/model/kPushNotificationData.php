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
	public $queueName;
	
	/**
	 * @var string
	 */
	public $queueKey;
	
	/**
	 * @var string
	 */
	public $url;

	/**
	 * @return string
	 */
	public function getQueueName()
	{
		return $this->queueName;
	}
	
	/**
	 * @param string $queueName
	 */
	public function setQueueName($queueName)
	{
		$this->queueName = $queueName;
	}

	/**
	 * @return string
	 */
	public function getQueueKey()
	{
		return $this->queueKey;
	}

	/**
	 * @param string $key
	 */
	public function setQueueKey($queueKey)
	{
		$this->queueKey = $queueKey;
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
}