<?php
abstract class ClientGeneratorFromXml 
{
	protected $_txt = "";
	protected $_files = array();
	protected $_xmlFile = "";
	protected $_sourcePath = "";
	protected $_params = array();
	
	protected $package = 'Kaltura';
	protected $subpackage = 'Client';
	
	public function setPackage($package)
	{
		$this->package = $package;
	}

	public function setSubpackage($subpackage)
	{
		$this->subpackage = $subpackage;
	}

	public function ClientGeneratorFromXml($xmlFile, $sourcePath = null)
	{
		$this->_xmlFile = realpath($xmlFile);
		$this->_sourcePath = realpath($sourcePath);
		
		if (!file_exists($this->_xmlFile))
			throw new Exception("The file [" . $this->_xmlFile . "] was not found");
			
		if (($sourcePath !== null) && !(file_exists($sourcePath)))
			throw new Exception("Source path was not found [$sourcePath]");
			
		if (is_dir($this->_sourcePath))
			$this->addSourceFiles($this->_sourcePath);
	}
	
	public abstract function generate();
	
	public function getOutputFiles()
	{
		return $this->_files;
	}
	
	public function setParam($key, $value)
	{
		$this->_params[$key] = $value;		
	}
	
	public function getParam($key)
	{
		if (!array_key_exists($key, $this->_params))
			return null;
		return $this->_params[$key];
	}
	
	protected function addFile($fileName, $fileContents)
	{
		 $this->_files[$fileName] = $fileContents;
	}
	
	protected function addSourceFiles($directory)
	{
		// add if file
		if (is_file($directory)) 
		{
			$file = str_replace($this->_sourcePath.DIRECTORY_SEPARATOR, "", $directory);
			$this->addFile($file, file_get_contents($directory));
			return;
		}
		
		// loop through the folder
		$dir = dir($directory);
		while (false !== $entry = $dir->read()) 
		{
			// skip pointers & hidden files
			if ($this->beginsWith($entry, ".")) 
			{
				continue;
			}
			 
			$this->addSourceFiles(realpath("$directory/$entry"));
		}
		 
		// clean up
		$dir->close();
	}
	
	protected function endsWith($str, $end) 
	{
		return (substr($str, strlen($str) - strlen($end)) === $end);
	}
	
	protected function beginsWith($str, $start) 
	{
		return (substr($str, 0, strlen($start)) === $start);
	}
	
	protected function upperCaseFirstLetter($str)
	{
		return ucwords($str); 
	}
	
	protected function camelCaseToUnderscoreAndLower($value)
	{
		$separator = '_';
		$matchPattern = array('#(?<=(?:[A-Z]))([A-Z]+)([A-Z][A-z])#', '#(?<=(?:[a-z]))([A-Z])#');
		$replacement = array('\1' . $separator . '\2', $separator . '\1');
		$newValue = preg_replace($matchPattern, $replacement, $value);
		return strtolower($newValue);
		//$filter = new Zend_Filter_Word_CamelCaseToUnderscore();
		//return strtolower($filter->filter($value));
	}
	
	protected function isSimpleType($type)
	{
		return in_array($type, array("int","string","bool","float"));
	}
	
	protected function startNewTextBlock()
	{
		$this->_txt = "";
	}
	
	protected function appendLine($txt = "")
	{
		$this->_txt .= $txt ."\n";
	}
	
	protected function getTextBlock()
	{
		return $this->_txt;
	}
}