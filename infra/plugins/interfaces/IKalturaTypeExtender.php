<?php
/**
 * Enable the plugin to return additional enum values that extend the base value
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaTypeExtender extends IKalturaBase
{
	/**
	 * Return all enum values that extend the base enum value
	 * @param string $baseClass
	 * @param string $enumValue
	 * @return array
	 */
	public static function getExtendedTypes($baseClass, $enumValue);
}