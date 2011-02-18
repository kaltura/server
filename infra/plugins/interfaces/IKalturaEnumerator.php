<?php
/**
 * Plugins may add enumeration values to those used by the Kaltura core's baseEnumName interface. 
 * You implement baseEnumName by defining a class for one or more additional enum values. 
 * The getEnums action returns a list of the class names that you define to implement baseEnumName. 
 * This enables the plugin API to receive enumeration values that other plugins define, in addition to the values that the core defines.
 * 
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEnumerator extends IKalturaBase
{
	const PLUGIN_VALUE_DELIMITER = '.';
	
	/**
	 * Returns a list of enumeration class names that implement the baseEnumName interface.
	 * 
	 * @return array<string> A string listing the enum class names that extend baseEnumName
	 */
	public static function getEnums($baseEnumName);
}