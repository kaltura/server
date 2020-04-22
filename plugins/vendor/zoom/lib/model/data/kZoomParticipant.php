<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomParticipant implements iZoomObject
{
	const USER_EMAIL = 'user_email';

	public $email;

	public function parseData($data)
	{
		if (isset($data[self::USER_EMAIL]) && $data[self::USER_EMAIL])
		{
			$this->email = $data[self::USER_EMAIL];
		}
	}
}
