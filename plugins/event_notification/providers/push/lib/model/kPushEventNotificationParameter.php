<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushEventNotificationParameter extends kEventNotificationParameter
{
	/**
	 * @var bool
	 */
	public $includeInQueueKey;

	/**
	 * @return string
	 */
	public function getIncludeInQueueKey()
	{
		if(!isset($this->includeInQueueKey))
			return false;
		
		return $this->includeInQueueKey;
	}

	/**
	 * @param string $key
	 */
	public function setIncludeInQueueKey($includeInQueueKey)
	{
		$this->includeInQueueKey = $includeInQueueKey;
	}
}