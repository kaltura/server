<?php
interface IKalturaEnumerator extends IKalturaBase
{
	const PLUGIN_VALUE_DELIMITER = '.';
	
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName);
}