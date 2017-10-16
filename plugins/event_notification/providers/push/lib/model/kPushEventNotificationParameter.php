<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushEventNotificationParameter extends kEventNotificationParameter
{
	/**
	 * @var string
	 */
	public $queueKeyToken;

	/**
	 * @return string
	 */
	public function getQueueKeyToken()
	{		
		return $this->queueKeyToken;
	}

	/**
	 * @param string $queueKeyToken
	 */
	public function setQueueKeyToken($queueKeyToken)
	{
		$this->queueKeyToken = $queueKeyToken;
	}
}