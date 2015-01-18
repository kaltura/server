<?php
class AndroidClientGenerator extends JavaClientGenerator
{
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/android")
	{
		$this->_baseClientPath = "KalturaClient/" . $this->_baseClientPath;
		parent::__construct($xmlPath, $sourcePath, $config);
	}
	
	protected function normalizeSlashes($path)
	{
		return str_replace('/', DIRECTORY_SEPARATOR, $path);
	}
	
	protected function removeFiles($namePrefix)
	{
		$namePrefix = $this->normalizeSlashes($namePrefix);
		foreach ($this->_files as $name => $data)
		{
			if (kString::beginsWith($name, $namePrefix))
				unset($this->_files[$name]);
		}
	}

	protected function addFiles($sourcePath, $destPath)
	{
		$sourcePath = realpath($sourcePath);
		$destPath = $this->normalizeSlashes($destPath);
		$this->addSourceFiles($sourcePath, $sourcePath . DIRECTORY_SEPARATOR, $destPath);
	}
	
	public function generate() 
	{
		$this->addFiles("sources/java/src/com", "KalturaClient/src/com/");
		$this->removeFiles("KalturaClient/src/com/kaltura/client/KalturaLoggerLog4j.java");
		$this->removeFiles("KalturaClient/src/com/kaltura/client/test/");
		$this->addFiles("sources/java/src/com/kaltura/client/test", "KalturaClientTester/src/com/kaltura/client/test/");

		parent::generate();
	}
	
	protected function addFile($fileName, $fileContents, $addLicense = true)
	{
		$fileContents = str_replace(
				'String clientTag = "java:@DATE@"', 
				'String clientTag = "android:@DATE@"', 
				$fileContents);
		parent::addFile($fileName, $fileContents, $addLicense);
	}
}
