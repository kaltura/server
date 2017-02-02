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
	public $isQueueKeyParam;

	/**
	 * @return bool
	 */
	public function getIsQueueKeyParam()
	{
		if(!isset($this->isQueueKeyParam))
			return false;
		
		return $this->isQueueKeyParam;
	}

	/**
	 * @param bool $isQueueKeyParam
	 */
	public function setIsQueueKeyParam($isQueueKeyParam)
	{
		$this->isQueueKeyParam = $isQueueKeyParam;
	}
}