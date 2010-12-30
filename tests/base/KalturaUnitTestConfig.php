<?php

class KalturaUnitTestConfig extends Zend_Config_Ini
{
	private $filePath;
	
	public function __construct($filePath)
	{
		if(! file_exists($filePath))
			file_put_contents($filePath, '');
		
		$this->filePath = $filePath;
		parent::__construct($filePath, null, true);
	}
	
	public function saveToIniFile()
	{
		$fileContent = $this->configArrayToIni($this);
		file_put_contents($this->filePath, $fileContent);
	}
	
	private function configArrayToIni($config, $parentPrefix = '', $level = 0)
	{
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
}

