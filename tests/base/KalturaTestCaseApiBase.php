<?php

/**
 * Represents a base test case for a general test
 * @author Roni
 *
 */
class KalturaTestCaseApiBase extends PHPUnit_Framework_TestCase
{	
	/**
	 * @var KalturaTestConfig
	 */
	protected $config;
	
	/**
	 * @var array
	 */
	protected $theDependencyInput;
	
	/**
	 * @var array
	 */
	protected $theDependencies;
	
    /* (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setDependencyInput()
     */
    public function setDependencyInput(array $dependencyInput)
    {
        $this->theDependencyInput = $dependencyInput;
        parent::setDependencyInput($dependencyInput);
    }

    /* (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setDependencies()
     */
    public function setDependencies(array $dependencies)
    {
        $this->theDependencies = $dependencies;
        parent::setDependencies($dependencies);
    }
    
	/**
	 * 
	 * Creates a new Kaltura test Object
	 * @param string $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = null, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
				
		$class = get_class($this);
		if($name)
		{
			KalturaLog::info("_____________________________________ [$class] [$name] ___________________________________");
		}
		else 
		{
			KalturaLog::info("__________________________________________ [$class] ______________________________________");
		}
		
		$testFilePath = KAutoloader::getClassFilePath($class);
		$this->testFolder = dirname($testFilePath);
			
		$this->inputs = $data;
		
		KalturaLog::info("Loads config file [$testFilePath.ini]");
		$this->config = new KalturaTestConfig("$testFilePath.ini");
		$testConfig = $this->config->get('config');
		
		if(!$testConfig)
		{
			$testConfig = new Zend_Config(array(
				'source' => KalturaTestSource::XML,
			), true);
			
			$this->config->config = $testConfig;
			$this->config->saveToIniFile();
		}
		
		$this->dataSource = $testConfig->source;
		
		$this->outputFolder = $testConfig->outputFolder;
		if($this->outputFolder && !is_dir($this->outputFolder))
		{
			KalturaLog::info("Creating folder output [$this->outputFolder]");
			mkdir($this->outputFolder, 777, true);
		}
	}
	
	/**
	 * @param Zend_Config $testConfig
	 * @param ReflectionParameter $arg
	 * @throws Exception
	 * @throws KalturaTestException
	 * @return Ambigous
	 */
	protected function getArgConfig(Zend_Config $testConfig, ReflectionParameter $arg)
	{
		$argName = $arg->getName();
		$argConfig = $testConfig->get($argName);
		
		KalturaLog::debug("Tests data [$argName] config [" . print_r($argConfig, true) . "]");
		
		if(!$argConfig)
		{
			if(!$arg->allowsNull())
				throw new Exception("Argument [$argName] can't be null for test [" . $this->getName() . "]");
				
			return null;
		}
		
		if(is_string($argConfig))
			return $argConfig;
			
		switch($argConfig->objectType)
		{
			case 'dependency':
				throw new KalturaTestException("Argument [$argName] taken from dependency");
				
			case 'array':
				return $this->populateArray($argConfig);
				
			case 'native':
				return $argConfig->value;
				
			case 'file':
				return $argConfig->path;
				
			default:
				return $this->populateObject($argConfig);
		}
	}
	
	/**
	 * @param Zend_Config $config
	 * @param Object $object
	 * @return Object
	 */
	protected function populateArray(Zend_Config $config)
	{
		KalturaLog::debug("Creating array");
		$array = array();
		
		foreach($config as $index => $value)
		{
			if($index == 'objectType')
				continue;
				
			if($value instanceof Zend_Config)
			{
				$value = $this->populateObject($value);
			}
			elseif(substr($value, 0, 1) == '@')
			{
				$fileName = substr($value, 1);
				KalturaLog::debug("Load key [$index] content from file [$fileName]");
				$value = file_get_contents($fileName);
			}

			KalturaLog::debug("Set array key [$index] to value [" . print_r($value, true) . "]");
			$array[] = $value;
		}
		return $array;
	}
	
	/**
	 * @param Zend_Config $config
	 * @param Object $object
	 * @return Object
	 */
	protected function populateObject(Zend_Config $config)
	{
		if(!$config->objectType)
			return null;
			
		if($config->objectType == 'file')
			return $config->path;
		
		if($config->objectType == 'array')
			return $this->populateArray($config);
		
		$objectType = $config->objectType;
		KalturaLog::debug("Creating object [$objectType]");
		$reflectionClass = new ReflectionClass($objectType); 
		$object = new $objectType();
		
		foreach($config as $field => $value)
		{
			if($field == 'objectType')
				continue;
				
			if($value instanceof Zend_Config)
			{
				$value = $this->populateObject($value);
			}
			elseif(substr($value, 0, 1) == '@')
			{
				$fileName = substr($value, 1);
				KalturaLog::debug("Load attribute [$field] content from file [$fileName]");
				$value = file_get_contents($fileName);
			}

			KalturaLog::debug("Set attribute [$field] to value [" . print_r($value, true) . "]");
			if($reflectionClass->hasMethod("set{$field}"))
				call_user_method_array("set{$field}", $object, $value);
			else
				$object->$field = $value;
		}
		return $object;
	}
	
	/**
	 * @return array<array>
	 */
	protected function provideTestData($className, $methodName)
	{
		KalturaLog::debug("class [$className], method [$methodName]");
		
		$method = new ReflectionMethod($className, $methodName);
		$args = $method->getParameters();
		
		$methodConfig = $this->config->get($methodName);
		if($methodConfig)
		{
			KalturaLog::debug("Tests data found for test [$methodName]");
		}
		elseif(is_array($args) && count($args))
		{
			$default = new Zend_Config(array(), true);
			
			foreach($args as $index => $arg)
			{
				$argConfig = new Zend_Config(array(), true);
				$argName = $arg->getName();
				KalturaLog::info("Create default config for attribute [$argName] in method [$methodName]");
				$objectType = $arg->getClass();
				if($objectType)
				{
					$objectType = $objectType->getName();
					KalturaLog::info("Default config for attribute [$argName] of objectType [$objectType]");
					$argConfig->objectType = $objectType;
				}
				elseif($arg->isArray())
				{
					KalturaLog::info("Default config for attribute [$argName] of objectType [array]");
					$argConfig->objectType = 'array';
				}
				else
				{
					KalturaLog::info("Default config for attribute [$argName] of native objectType");
					$argConfig->objectType = 'native';
				}
				$default->$argName = $argConfig;
			}
			$methodConfig = new Zend_Config(array('test1' => $default), true);
			
			$this->config->$methodName = $methodConfig;
			$this->config->saveToIniFile();
		}
		
		$tests = array();
		if($methodConfig instanceof Zend_Config)
		{
			foreach($methodConfig as $testName => $testConfig)
			{
				$test = array();
				if(is_array($args) && count($args))
				{
					foreach($args as $index => $arg)
					{
						try 
						{
							$value = $this->getArgConfig($testConfig, $arg);
							$test[] = $value;
						}
						catch (KalturaTestException $e)
						{
							$this->fail($e->getMessage());
						}
					}
				}
					
				$tests[] = $test;
			}
		}
		
		KalturaLog::info("Tests data provided [" . print_r($tests, true) . "]");
		return $tests;
	}
	
	/**
	 * @return array<array>
	 */
	public function provideData()
	{
		$debugTrace = debug_backtrace();
		list($className, $methodName) = $debugTrace[2]['args'];
		if(class_exists($className) && method_exists($className, $methodName))
		{
			try
			{
				if($this->dataSource == KalturaTestSource::INI)
				{
					return $this->provideTestData($className, $methodName);
				}
				elseif($this->dataSource == KalturaTestSource::XML)
				{
					return null;
				}
			}
			catch (Exception $e)
			{
				KalturaLog::err($e->getMessage());
				$this->fail($e->getMessage());
			}
		}
		
		$this->fail('Calling method not found');
	}

	/* (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::runTest()
	 */
	public function runTest()
	{
		$name = $this->getName(true);
		KalturaLog::debug("Run test [$name]\n");
		
		if(is_array($this->theDependencyInput) && count($this->theDependencyInput))
		{
			foreach ($this->theDependencyInput as $index => $value)
			{
				KalturaLog::debug("Adding key [$index], value [$value] to the test data\n");
				$this->data[$index] = $value;
			}
		}

		return parent::runTest();
	}
	
    /* (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::run()
     */
    public function run(PHPUnit_Framework_TestResult $result = NULL)
    {
        if (is_array($this->theDependencies) && count($this->theDependencies)) {
            
            $className  = get_class($this);
           	$passed     = $result->passed();
            $passedKeys = array_keys($passed);

            foreach ($this->theDependencies as $dependency) 
            {
				$dependency = $className . '::' . $dependency;
                if(in_array($dependency, $passedKeys))
                	$this->theDependencyInput[] = $passed[$dependency];
            }
        }

        return parent::run($result);
    }
}
