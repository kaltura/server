<?php
/**
	Ini files manipulation utlities
	@Package infra
 */
class iniUtils
{
	/**
	 * Given an associative array, this function will generate INI file string that represent it.
	 * @param $ini
	 * @param bool $isBaseLevel
	 * @param null $baseKey
	 * @return string
	 */
	public static function arrayToIniString($ini, $isBaseLevel = true, $baseKey = null)
	{
		$res = '';
		foreach ($ini as $key => $value)
		{
			if (!is_array($value))
			{
				$levelKey = $baseKey ? $baseKey . ".$key" : $key;
				$res .= $levelKey . " = " . (is_numeric($value) ? $value : '"' . $value . '"') . "\n";
			}
		}
		foreach ($ini as $key => $value)
		{
			if (is_array($value))
			{
				if ($isBaseLevel)
				{
					$res .= "\n[$key]\n" . self::arrayToIniString($value, false);
				}
				else
				{
					$innerKey = $baseKey ? $baseKey . ".$key" : $key;
					$res .= self::arrayToIniString($value, false, $innerKey);
				}
			}
		}
		return $res;
	}
}