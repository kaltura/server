<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 12/19/2018
 * Time: 5:40 PM
 */

class iniUtils
{
	public static function arrayToIniString(array $iniData)
	{
		$res = '';
		foreach ($iniData as $key => $value)
		{
			if (is_array($value))
			{
				$res .= "\n[$key]\n" . self::arrayToIniString($value);
			}
			else
			{
				$res .= $key . " = " . (is_numeric($value) ? $value : '"' . $value . '"') . "\n";
			}
		}
		return $res;
	}
}