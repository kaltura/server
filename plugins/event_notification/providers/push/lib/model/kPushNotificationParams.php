<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushNotificationParams extends KalturaObject
{
	/**
	 * @var array<kPushEventNotificationParameter>
	 */
	public $userParams;

	/**
	 * @return array<kPushEventNotificationParameter>
	 */
	public function getUserParams()
	{
		return $this->userParams;
	}

	/**
	 * @param array <kPushEventNotificationParameter> $userParams
	 */
	public function setUserParams($userParams)
	{
		$this->userParams = $userParams;
	}
}