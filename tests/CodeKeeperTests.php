<?php
require_once("tests/bootstrapTests.php");

class CodeKeeperTests extends PHPUnit_Framework_TestCase 
{
	public function __construct()
	{
		parent::__construct();
		require_once("dummy/DummyGenerator.php");
	}
	public function testThatObjectClassNameEqualToFileNames()
	{
    	$this->loadAndAssertDirectory(KALTURA_API_PATH."/lib");
    	$this->loadAndAssertDirectory(KALTURA_API_PATH."/services");
	}
	
	private function loadAndAssertDirectory($dir)
	{
		foreach(scandir($dir) as $file)
		{
			if ($file != "." && $file != "..")
			{
				if (is_dir($dir."/".$file))
				{
					$this->loadAndAssertDirectory($dir."/".$file);
				}
				else if (pathinfo($dir."/".$file, PATHINFO_EXTENSION) == "php") 
				{
					$fileData = file_get_contents($dir."/".$file);
					$result = null;
					$classNameInCode = null;
					if (preg_match_all("/^\\s?class\\s?(\\w*)/m", $fileData, $result))
					{
						$classNameInCode = $result[1][0];						
					}
					
					$classNameInFile = str_replace(".php", "", $file);
					if ($classNameInCode) // only if we have class in that file
					    $this->assertEquals($classNameInFile, $classNameInCode);
				}
			}
		}
	}
	
	public function testApiObjectsPropertyTypes()
	{
		return;
		// test that only string, int, float, bool, instanceof KalturaEnum, instanceof KalturaObject are allowed in API objects 
		$dummyGenerator = new DummyGenerator();
		$dummyGenerator->load();
		$services = $dummyGenerator->getServices();
		foreach($services as $service)
		{
			$actions = $service->getActions();
			$actions = array_keys($actions);
			foreach($actions as $actionId)
			{
				$actionParams = $service->getActionParams($actionId);
				foreach($actionParams as $actionParam)
				{
					if (!$actionParam->isSimpleType())
						$actionParam->getTypeReflector(); // will fail if the type doesn't exists					
				}
			}
		}
	}
	
	public function testEnumsValuesAreIntegers()
	{
		$ignoreClasses = array("KalturaResponseCacher");
		$classMapFileLocation = KAutoloader::buildPath(KALTURA_API_PATH, "cache", "KalturaClassMap.cache");
		
		$classMap = unserialize(file_get_contents($classMapFileLocation));
		
		foreach($classMap as $class => $path)
		{
			if (in_array($class, $ignoreClasses))
				continue;
				
			if (strpos(($path), KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor")) === 0) // ignore vendor libs
				continue;
				
			if (strpos(($path), KAutoloader::buildPath(KALTURA_ROOT_PATH, "infra")) === 0) // ignore infra
				continue;
				
			if (strpos(($path), SF_ROOT_DIR) === 0) // ignore alpha
				continue;
				
			if (!class_exists($class, false))
				require_once($path);
			
			$reflectionClass = new ReflectionClass($class);
			
			if ($reflectionClass->isSubclassOf("KalturaEnum"))
			{
				$constants = $reflectionClass->getConstants();
				foreach($constants as $key => $value)
				{
					$this->assertType("integer", $value, "in class \"".$class."\"");
				}
			}
		}
	}
	
	public function testApiSignaturesMatchPhpDoc()
	{
		$this->markTestIncomplete("TODO");
	}
}

?>