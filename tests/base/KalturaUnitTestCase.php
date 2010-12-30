<?php

class KalturaUnitTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	protected $outputFolder;
	
	/**
	 * @var string
	 */
	protected $testFolder;
	
	/**
	 * @var KalturaUnitTestConfig
	 */
	protected $config;
	
	/**
	 * @var KalturaUnitTestsSource
	 */
	protected $dataSource;

	
	public function __construct($name = NULL, array $data = array(), $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
		
		KalturaLog::debug(print_r($this->data, true));
		
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
			
		$this->config = new KalturaUnitTestConfig("$testFilePath.ini");
		$testConfig = $this->config->get('config');
		if(!$testConfig)
		{
			$testConfig = new Zend_Config(array(
				'source' => KalturaUnitTestSource::INI,
			), true);
			
			$this->config->config = $testConfig;
			$this->config->saveToIniFile();
		}
		
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
	 * @throws KalturaUnitTestException
	 * @return Ambigous
	 */
	protected function getArgConfig(Zend_Config $testConfig, ReflectionParameter $arg)
	{
		$argName = $arg->getName();
		$argConfig = $testConfig->get($argName);
		if(!$argConfig)
		{
			if(!$arg->allowsNull())
				throw new Exception("Argument [$argName] can't be null for test [" . $testConfig->getSectionName() . "]");
				
			return null;
		}
		
		switch($argConfig->type)
		{
			case 'dependency':
				throw new KalturaUnitTestException("Argument [$argName] taken from dependency");
				
			case 'array':
				return array();
				
			case 'native':
				return $argConfig->value;
				
			default:
				return $this->populateObject($argConfig);
		}
	}
	
	/**
	 * @param Zend_Config $config
	 * @param Object $object
	 * @return Object
	 */
	protected function populateObject(Zend_Config $config)
	{
		if(!$config->type)
			return null;
			
		$type = $config->type;
		KalturaLog::debug("Creating object [$type]");
		$reflectionClass = new ReflectionClass($type); 
		$object = new $type();
		
		foreach($config as $field => $value)
		{
			if($field == 'type')
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
		if(!$methodConfig)
		{
			$default = new Zend_Config(array(), true);
			
			foreach($args as $index => $arg)
			{
				$argConfig = new Zend_Config(array(), true);
				$argName = $arg->getName();
				KalturaLog::info("Create default config for attribute [$argName] in method [$methodName]");
				$type = $arg->getClass();
				if($type)
				{
					$type = $type->getName();
					KalturaLog::info("Default config for attribute [$argName] of type [$type]");
					$argConfig->type = $type;
				}
				elseif($arg->isArray())
				{
					KalturaLog::info("Default config for attribute [$argName] of type [array]");
					$argConfig->type = 'array';
				}
				else
				{
					KalturaLog::info("Default config for attribute [$argName] of native type");
					$argConfig->type = 'native';
				}
				$default->$argName = $argConfig;
			}
			$methodConfig = new Zend_Config(array('test1' => $default), true);
			
			$this->config->$methodName = $methodConfig;
			$this->config->saveToIniFile();
		}
		
		$tests = array();
		foreach($methodConfig as $testName => $testConfig)
		{
			$test = array();
			foreach($args as $index => $arg)
			{
				try 
				{
					$value = $this->getArgConfig($testConfig, $arg);
					$test[] = $value;
				}
				catch (KalturaUnitTestException $e)
				{
					KalturaLog::log($e->getMessage());
				}
			}
				
			$tests[] = $test;
		}
		
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
			return $this->provideTestData($className, $methodName);
			
		throw new Exception('Calling method not found');
	}
}

