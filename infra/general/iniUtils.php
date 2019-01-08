<?php
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