<?php
/**
 * @package api
 * @subpackage enum
 */
interface IKalturaDynamicEnum 
{
	/**
	 * @return array
	 */
	public static function getEnumClass();
	
	/**
	 * @return array
	 */
	public static function getAdditionalValues();
	
	/**
	 * @return KalturaDynamicEnum
	 */
	public static function get();
}
