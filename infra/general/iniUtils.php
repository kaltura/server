<?php
/**
 *  Ini files manipulation utlities
 *
 *  @package infra
 *  @subpackage utils
 */
class IniUtils
{
	const GLOBAL_INI_SECTION_REGEX = '/^\s*\[.*\S.*\]/m';

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
			self::safeLog("Could not write ini content to file $tempIniFile");
		}
		$ini = new Zend_Config_Ini($tempIniFile);
		unlink($tempIniFile);
		return $ini->toArray();
	}

	/**
	 * @param $content
	 * @param $globalContent
	 * @param $sectionsContent
	 */
	public static function splitContent($content, &$globalContent, &$sectionsContent)
	{
		if (is_array($content))
		{
			self::safeLog("Retrieved mapContent in array format");
			$content = IniUtils::arrayToIniString($content);
		}
		//get global section data - PREG_OFFSET_CAPTURE return offset starting point in index[1] of match
		preg_match(self::GLOBAL_INI_SECTION_REGEX, $content, $matches, PREG_OFFSET_CAPTURE);
		if (isset($matches[0][1]))// find the split point between the global part and the other sections
		{
			$globalContent .= PHP_EOL . substr($content, 0, $matches[0][1]);
			$sectionsContent .= PHP_EOL . substr($content, $matches[0][1]);
		}
		else
		{
			$globalContent .= PHP_EOL . $content;
		}
	}

	/**
	 * This function is required since this code can run before the autoloader
	 *
	 * @param string $msg
	 */
	protected static function safeLog($msg)
	{
		if (class_exists('KalturaLog') && KalturaLog::isInitialized())
			KalturaLog::debug($msg);
	}
}