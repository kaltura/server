<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.data
 */
class kZoomPanelist implements iZoomObject
{
	const EMAIL = 'email';

	public $email;

	public function parseData($data)
	{
		if (isset($data[self::EMAIL]) && $data[self::EMAIL])
		{
			$this->email = $data[self::EMAIL];
		}
	}
}
