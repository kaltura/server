<?php
require_once __DIR__ . '/kBaseConfCache.php';
require_once __DIR__ . '/kMapCacheInterface.php';

class kFileSystemConf extends kBaseConfCache implements kMapCacheInterface
{
	const LOCAL_CONF_FILE = 'local';
	const HOSTS_DIR = '/hosts/';
	function __construct()
	{
		if(!class_exists('Zend_Config_Ini'))
		{
			require_once 'Zend/Config/Exception.php';
			require_once 'Zend/Config/Ini.php';
		}
		parent::__construct();
	}

	public function getFileNames ($mapName, $hostname)
	{
		$iniFiles = array();

		if(!$mapName)
		{
			return $iniFiles;
		}

		$configDir = kEnvironment::getConfigDir();
		if ($mapName == self::LOCAL_CONF_FILE)
		{
			$iniFiles[] = "$configDir/base.ini";
		}
		$iniFiles[] = "$configDir/$mapName.ini";
		if($hostname)
		{
			$configPath = $configDir.self::HOSTS_DIR;
			if ($mapName != self::LOCAL_CONF_FILE)
				$configPath .= $mapName;

			if(is_dir($configPath))
			{
				$localConfigFile = strtolower($hostname) . '.ini';
				$configDir = dir($configPath);
				while (false !== ($iniFile = $configDir->read()))
				{
					$iniFileMatch = str_replace('#', '*', $iniFile);
					if(!fnmatch($iniFileMatch, $localConfigFile))
						continue;
					$iniFiles[] = "$configPath/$iniFile";
				}
				$configDir->close();
			}
		}
		return $iniFiles;
	}

	protected function mergeMaps(array $mapNames, $isLocal)
	{
		$result = array();
		if ($isLocal)
			$result = kEnvironment::getEnvMap();
		foreach ($mapNames as $iniFile)
		{
			if(file_exists($iniFile))
			{
				$config = new Zend_Config_Ini($iniFile);
				if($result)
				{
					$result = kEnvironment::mergeConfigItem($result, $config->toArray());
				}
				else
				{
					$result = $config->toArray();
				}
			}
		}
		return $result;
	}

	public function load ($key, $mapName)
	{
		$hostname = $this->getHostName();
		return $this->loadByHostName($mapName,$hostname);
	}

	public function loadByHostName ($mapName , $hostname)
	{
		$iniFiles = $this->getFileNames ($mapName , $hostname);
		$this->orderMap($iniFiles);
		$mergedMaps = $this->mergeMaps($iniFiles,($mapName==self::LOCAL_CONF_FILE));
		return $mergedMaps;
	}

	public function store($key, $mapName, $map, $ttl = 0)
	{
		return true;
	}


	public function getIniFilesList($mapName, $hostNameRegex)
	{
		$iniFile = array();
		$configDir = kEnvironment::getConfigDir();
		$baseConfigFile = $configDir . DIRECTORY_SEPARATOR . $mapName . '.ini';
		if(kFile::fileSize($baseConfigFile))
		{
			$iniFile[] = $baseConfigFile;
		}
		$hostNameRegexPattern = '/' . strtolower($hostNameRegex) . '/';
		$iniFilesFiles = kFile::listDir($configDir . self::HOSTS_DIR . $mapName);
		foreach ($iniFilesFiles as $iniFileItem)
		{
			$mapHostNameRegex = $iniFileItem[0];
			if(preg_match($hostNameRegexPattern, $mapHostNameRegex))
			{
				$iniFile[] = $configDir . self::HOSTS_DIR . $mapName . DIRECTORY_SEPARATOR .$mapHostNameRegex;
			}
		}
		return $iniFile;
	}

	public function getMapInfo($iniFile)
	{
		if(strpos ($iniFile,self::HOSTS_DIR))
		{
			$hostname =  basename($iniFile,'.ini');
			$iniNameBlocks = explode ('/',$iniFile);
			$mapName = $iniNameBlocks[count($iniNameBlocks)-2];
		}
		else
		{
			$hostname = '';
			$mapName =  basename($iniFile,'.ini');
		}

		if(!kFile::fileSize($iniFile))
		{
			return array (null, null ,null);
		}

		$fsMap = new Zend_Config_Ini($iniFile);
		$content = json_encode($fsMap->toArray());
		return array ($mapName, $hostname ,$content);
	}
}
