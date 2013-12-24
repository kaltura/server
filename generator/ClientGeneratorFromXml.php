<?php
abstract class ClientGeneratorFromXml 
{
	protected $_txt = "";
	protected $_files = array();
	protected $_xmlFile = "";
	protected $_sourcePath = "";
	protected $_params = array();
	
	protected $generateDocs = false;
	protected $package = 'External';
	protected $subpackage = 'Kaltura';
	
	public function setGenerateDocs($generateDocs)
	{
		$this->generateDocs = $generateDocs;
	}
	
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

		$this->_licenseBuffer = file_get_contents(dirname(__FILE__).'/sources/license.txt');
		$this->_licenseBuffer = str_replace('//', $this->getSingleLineCommentMarker(), $this->_licenseBuffer);
		$this->_licenseBuffer = str_replace("\r\n", "\n", $this->_licenseBuffer);
		
		$this->addFile('agpl.txt', file_get_contents(dirname(__FILE__).'/sources/agpl.txt'), false);
	}
	
	public function generate()
	{
		if (is_dir($this->_sourcePath))
			$this->addSourceFiles($this->_sourcePath, $this->_sourcePath . DIRECTORY_SEPARATOR, "");
	}
	
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
	
	protected function addFile($fileName, $fileContents, $addLicense = true)
	{
		if ($addLicense)
		{
			if ($this->beginsWith($fileContents, '<?php'))
			{
				$fileContents = "<?php\n" . $this->_licenseBuffer . substr($fileContents, 5);
			}
			else
			{
				$fileContents = $this->_licenseBuffer . $fileContents;
			}
		}
		
		$this->_files[$fileName] = str_replace('@DATE@', date('y-m-d'), $fileContents);
	}
	
	protected function addSourceFiles($directory, $rootSourceFolder, $rootDestFolder)
	{
		// add if file
		if (is_file($directory)) 
		{
			$file = str_replace($rootSourceFolder, $rootDestFolder, $directory);
			$this->addFile($file, file_get_contents($directory), false);
			return;
		}
		
		// loop through the folder
		$dir = dir($directory);
		$sourceFilesPaths = array();
		while (false !== $entry = $dir->read()) 
		{
			// skip source control files
			if ($this->beginsWith($entry, ".svn") || 
				$this->beginsWith($entry, ".cvs") || 
				$this->beginsWith($entry, ".git") || 
				$entry == '.'  || 
				$entry == '..' 
			)
			{
				continue;
			} 
			$sourceFilesPaths[] = realpath("$directory/$entry");
		}
		// clean up
		$dir->close();
		
		foreach($sourceFilesPaths as $sourceFilesPath)
			$this->addSourceFiles($sourceFilesPath, $rootSourceFolder, $rootDestFolder);
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
		return in_array($type, array("int","string","bool","float","bigint"));
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

	/* 
	 * returns the symbol used for single line comments, e.g. //
	 * 
	 * @return string 
	 */
	protected abstract function getSingleLineCommentMarker();
}