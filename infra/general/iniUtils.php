<?php

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/**
 *  Ini files manipulation utilities
 *
 *  @package infra
 *  @subpackage utils
 */
class IniUtils
{
	const GLOBAL_INI_SECTION_REGEX = '/^\s*\[.*\S.*\]/m';
	const EMPTY_LINE_REGEX = '/^\h*\v+/m';

	private static $fileSystemLoader = null;

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
		$ini = new kZendConfigIni($tempIniFile);
		unlink($tempIniFile);
		return $ini->toArray();
	}

	/**
	 * @param $content
	 * @param $globalContent
	 * @param array $sectionsContent
	 */
	public static function splitContent($content, &$globalContent, array &$sectionsContent)
	{
		if (is_array($content))
		{
			self::safeLog("Retrieved mapContent in array format");
			$content = IniUtils::arrayToIniString($content);
		}
		$content = trim(preg_replace(self::EMPTY_LINE_REGEX, '', $content));
		$tempSectionsContent = null;
		//get global section data - PREG_OFFSET_CAPTURE return offset starting point in index[1] of match
		preg_match(self::GLOBAL_INI_SECTION_REGEX, $content, $matches, PREG_OFFSET_CAPTURE);
		if (isset($matches[0][1]))// find the split point between the global part and the other sections
		{
			$globalContent .= PHP_EOL . substr($content, 0, $matches[0][1]);
			$tempSectionsContent = substr($content, $matches[0][1]);
		}
		else
		{
			$globalContent .= PHP_EOL . $content;
		}

		// merge new sections content to existing content
		if (preg_match_all(self::GLOBAL_INI_SECTION_REGEX, $tempSectionsContent, $tempSectionsMatches, PREG_SET_ORDER, 0))
		{
			$res = preg_split(self::GLOBAL_INI_SECTION_REGEX, $tempSectionsContent);
			array_shift($res);
			foreach ($tempSectionsMatches as $index => $part)
			{
				if (isset($sectionsContent[$part[0]]))
				{
					$sectionsContent[$part[0]] .= $res[$index];
				}
				else
				{
					$sectionsContent[$part[0]] = $res[$index];
				}
			}
		}
	}

	/**
	 * @param array $contentArray
	 * @return null|string
	 */
	public static function iniSectionsToString(array $contentArray)
	{
		$out = null;
		foreach ($contentArray as $key => $value)
		{
			$out .= "$key \n $value\n";
		}
		return $out;
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

	/**
	 * @param $path
	 * @return string
	 */
	public static function getBatchConfigFromFS()
	{
		$batchConfigFolder = realpath(dirname(__FILE__) . '/../../configurations/batch');
		$content = '';
		$configFilePaths = self::getConfigFilePaths($batchConfigFolder);
		foreach($configFilePaths as $configFilePath)
		{
			$content .= file_get_contents($configFilePath) . "\n";
		}
		return $content;
	}

	/**
	 * @param $path
	 * @return array
	 */
	protected static function getConfigFilePaths($path)
	{
		$configFilePaths = array();
		$d = dir($path);
		while (false !== ($file = $d->read()))
		{
			if(preg_match('/\.ini$/', $file))
			{
				$configFilePaths[] = $path . DIRECTORY_SEPARATOR . $file;
			}
		}
		$d->close();

		return $configFilePaths;
	}

	public static function parseIniFile($filename, $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL)
	{
		if(!self::$fileSystemLoader)
		{
			self::$fileSystemLoader = new FilesystemLoader();
		}

		$tmpFileName = kConf::get("cache_root_path") . '/twig/' . $filename;
		if (!kFile::checkFileExists($tmpFileName))
		{
			$dirName = dirname($filename);
			$baseName = basename($filename);
			self::$fileSystemLoader->setPaths($dirName);
	
			$twig = new Environment(self::$fileSystemLoader);
			$twig->addExtension(new twigExtensions());
	
			$renderedOutput = $twig->render($baseName);

			kFile::fullMkDir($tmpFileName);
			kFile::filePutContents($tmpFileName, $renderedOutput);
		}

		return parse_ini_file($tmpFileName, $process_sections, $scanner_mode);
	}
}
