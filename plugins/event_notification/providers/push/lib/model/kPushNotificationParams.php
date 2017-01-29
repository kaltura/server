<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
 */
class kPushNotificationParams extends KalturaObject
{
	/**
	 * PushNotificationSystemName
	 * @var string
	 */
	public $systemName;

	/**
	 * @var array<kEventNotificationParameter>
	 */
	public $userParams;

	/**
	 * @return string
	 */
	public function getSystemName()
	{
		return $this->systemName;
	}

	/**
	 * @param string systemName
	 */
	public function setSystemName($systemName)
	{
		$this->systemName = $systemName;
	}

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