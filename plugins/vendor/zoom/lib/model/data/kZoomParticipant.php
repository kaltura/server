<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomParticipant implements iZoomObject
{
	const USER_EMAIL = 'user_email';
	const ROLE = 'role';
	const EMAIL = 'email';

	public $email;

	public function parseData($data, $role = null)
	{
		if (!$role && isset($data[self::USER_EMAIL]) && $data[self::USER_EMAIL])
		{
			$this->email = $data[self::USER_EMAIL];
		}
		elseif ($role && isset($data[self::ROLE]) && $data[self::ROLE] == $role && isset($data[self::EMAIL]))
		{
			$this->email = $data[self::EMAIL];
		}
	}
}
