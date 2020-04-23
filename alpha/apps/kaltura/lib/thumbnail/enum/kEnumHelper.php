<?php
/**
 * @package core
 * @subpackage thumbnail.enum
 */

class kEnumHelper
{
	protected static $constantsCache = array();

	protected static function getConstantsByReflection($enumName)
	{
		$reflect = new ReflectionClass($enumName);
		return $reflect->getConstants();
	}

	protected static function getConstants($enumName)
	{
		if(!array_key_exists($enumName, self::$constantsCache))
		{
			self::$constantsCache[$enumName] = self::getConstantsByReflection($enumName);
		}

		return self::$constantsCache[$enumName];
	}

	public static function isValidName($enumName, $name)
	{
		$constants = self::getConstants($enumName);
		$keys = array_keys($constants);
		return in_array($name, $keys);
	}

	public static function isValidValue($enumName, $value)
	{
		$values = array_values(self::getConstants($enumName));
		return in_array($value, $values);
	}
}