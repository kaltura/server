<?php

/**
 * 
 * Represents a test configuration
 * @author Roni
 *
 */
class KalturaTestConfig extends Zend_Config_Ini
{
	/**
	 * 
	 * The test configuration file path
	 * @var string
	 */
	private $filePath;
	
	/**
	 * 
	 * Creates a new test configuration
	 * @param unknown_type $filePath
	 */
	public function __construct($filePath = null)
	{
		if(empty($filePath))
		{
			$this->_allowModifications = true;
			return;
		}
		
		if(! file_exists($filePath))
		{
			KalturaLog::debug("Test configuration doesn't exists [$filePath] creating new ");
			file_put_contents($filePath, '');
		}
		
		$this->filePath = $filePath;
		parent::__construct($filePath, null, true);
	}

	/**
	 * 
	 * Creates a new test configuration from Zend_Config
	 */
	public static function fromZendConfig(Zend_Config $config)
	{
		$newConfig = new KalturaTestConfig();
		$dataArray = $config->toArray();
		
		foreach ($dataArray as $key => $value)
		{
			$newConfig->$key = $value;
		}
		
		return $newConfig;
	}
	
	/**
	 * 
	 * Saves the current configuration into the file path
	 */
	public function saveToIniFile()
	{
		$fileContent = $this->configArrayToIni($this);
		file_put_contents($this->filePath, $fileContent);
	}
	
	/**
	 * 
	 * Takes a config array, with given parent prefix and returns the Ini array
	 * @param array $config
	 * @param string $parentPrefix
	 * @param int $level
	 */
	private function configArrayToIni($config, $parentPrefix = '', $level = 0)
	{
		//config must be an instance of array and Zend_Config
		if(!is_array($config) && !($config instanceof Zend_Config))
			return '';
			
		$str = '';
		foreach($config as $k => $v)
		{
			$prefix = $k;
			if($parentPrefix)
				$prefix = "{$parentPrefix}.{$prefix}";
			
			if(is_array($v) || ($v instanceof Zend_Config))
			{
				if(!$level)
				{
					$str .= "\n[$k]\n";
					$prefix = false;
				}
					
				$str .= $this->configArrayToIni($v, $prefix, $level + 1);
			}
			else
			{
				$str .= $prefix . (strlen($prefix) < 50 ? str_repeat(" ", 50 - strlen($prefix)) : '') . "= " . $v . "\n";
			}
		}
		
		return $str;
	}
	 
	/**
	 * (non-PHPdoc)
	 * @see Zend_Config::get()
	 */
	public function get($name, $default = null)
	{
		$value = parent::get($name, $default);

		$isGlobal = KalturaGlobalData::isGlobalData($value);
		
		if($isGlobal)
			$value = KalturaGlobalData::getData($value);
		else
		{
			if($value instanceof Zend_Config)
			{
				//We transform it to be a test configuration object so we can take global data from it
				$value = KalturaTestConfig::fromZendConfig($value);
			}
		}
				
		return $value;
	}
}