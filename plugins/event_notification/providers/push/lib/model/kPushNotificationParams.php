<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushNotificationParams extends KalturaObject
{
	/**
	 * @var array<kEventNotificationParameter>
	 */
	public $userParams;

	/**
	 * @return array<kEventNotificationParameter>
	 */
	public function getUserParams()
	{
		return $this->userParams;
	}

	/**
	 * @param array <kEventNotificationParameter> $userParams
	 */
	public function setUserParams($userParams)
	{
		$this->userParams = $userParams;
	}
}