<?php
/**
 * @package api
 * @subpackage enum
 */
interface IKalturaDynamicEnum extends IKalturaEnum
{
	/**
	 * @return array
	 */
	public static function getEnumClass();
}
