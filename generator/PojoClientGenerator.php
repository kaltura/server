<?php
class PojoClientGenerator extends JavaClientGenerator
{
	private $javaSourcePath;
	
	function __construct($xmlPath, Zend_Config $config, $sourcePath = "sources/pojo")
	{
		$this->javaSourcePath = realpath("sources/java");
		parent::__construct($xmlPath, $config, $sourcePath);
	}

	/* (non-PHPdoc)
	 * @see JavaClientGenerator::generate()
	 */
	public function generate()
	{
		if (is_dir($this->javaSourcePath))
			$this->addSourceFiles($this->javaSourcePath, $this->javaSourcePath . DIRECTORY_SEPARATOR, "");
			
		parent::generate();
	}
}
