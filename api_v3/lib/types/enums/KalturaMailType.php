<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaMailType extends KalturaDynamicEnum implements MailType
{
	public static function getEnumClass()
	{
		return 'MailType';
	}
}
