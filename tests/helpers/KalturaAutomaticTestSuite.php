<?php
class KalturaAutomaticTestSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct ($testSuiteName, $testsDirectory)
    {
        $this->setName($testSuiteName);
   	
        $this->addRecursiveTests($testsDirectory);
    }
    
    protected function addRecursiveTests($directory)
    {
    	foreach(scandir($directory) as $file)
		{
			if ($file[0] != ".") // ignore linux hidden files
			{
				$path = realpath($directory."/".$file);
				if (is_dir($path))
				{
					$this->addRecursiveTests($path);
				}
				else if (is_file($path)) 
				{
					$className = pathinfo($path, PATHINFO_FILENAME);
					if (class_exists($className))
					{
						$reflectionClass = new ReflectionClass($className);
						if ($reflectionClass->isSubclassOf("PHPUnit_Framework_TestCase"))
							$this->addTestSuite($reflectionClass);
					}
				}
			}
		}
    }
}