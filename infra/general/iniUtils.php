<?php
/**
 *  Ini files manipulation utlities
 *
 *  @package infra
 *  @subpackage utils
 */
class IniUtils
{
	/**
	 * Given an associative array, this function will generate INI file string that represent it.
	 * @param $ini
	 * @param bool $isBaseLevel
	 * @param null $baseKey
	 * @return string
	 */
	public static function arrayToIniString(array $ini, $isBaseLevel = true, $baseKey = null)
	{
		$res = '';
		foreach ($ini as $key => $value)
		{
			if (!is_array($value))
			{
				$levelKey = $baseKey ? $baseKey . ".$key" : $key;
				$res .= $levelKey . ' = ' . (is_numeric($value) ? $value : '"' . $value . '"') . "\n";
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

	/**
	 * @param $iniStringContent
	 * @return array
	 */
	public static function iniStringToIniArray($iniStringContent)
	{
		$tempIniFile = tempnam(sys_get_temp_dir(), 'TMP_INI_');
		$res = file_put_contents($tempIniFile, $iniStringContent);
		if (!$res)
		{
			KalturaLog::warning("Could not write ini content to file $tempIniFile");
		}
		$ini = new Zend_Config_Ini($tempIniFile);
		unlink($tempIniFile);
		return $ini->toArray();
	}
}