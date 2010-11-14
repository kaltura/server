<?php
interface IKalturaEnumerator extends IKalturaBase
{
	/**
	 * @return array<string> list of enum classes names that extend the base enum name
	 */
	public static function getEnums($baseEnumName);
}