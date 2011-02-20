<?php

require_once (dirname(__FILE__). '/../bootstrap/bootstrapServer.php');

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

	/**
	 * 
	 * Indicates wheter the test has failures
	 * @var bool
	 */
	public $hasFailures = false;
		
	/**
	 * 
	 * Holds the current failure in the test
	 * @var KalturaUnitTestCaseFailure
	 */
	public $currentFailure = null;
	
	/**
	 * 
	 * Creates a new Kaltura Unit Test Object
	 * @param unknown_type $name
	 * @param array $data
	 * @param unknown_type $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '')
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
			
		print ($testFilePath);
		
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
	 * 
	 * Returns the inputs for the test
	 */
	public function getInputs()
	{
		return $this->data;
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
		
		if(is_string($argConfig))
			return $argConfig;
			
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

	/**
	 * Overrides runTest method for the phpunit framework
	 * @see PHPUnit_Framework_TestCase::runTest()
	 */
	public function runTest()
	{
		print("In RunTest\n");
		
		//Do this section only once per test file and not for test... so we can initiate all the tests 
		//TODO: HOW to do nice :) and also how to know if this is a new test class or a new test or just another input
		$this->currentFailure = null;
		
		if(KalturaUnitTestListener::$failuresFile == null)
		{
			$class = get_class($this);

			$classPath = KAutoloader::getClassFilePath($class);
			KalturaUnitTestListener::$failuresFile = fopen(dirname($classPath) . "/testsData/{$this->name}.failures", "w+");
			$this->result->addListener(new KalturaUnitTestListener());
		}
		
		parent::runTest();
	}
	
	/**
	 * 
	 * The unit test data provider (gets the data for the different unit tests)
	 * @param string $dataFilePath - the data file path (with the objects)
	 * @return array<array>();
	 */
	public static function provider($dataFilePath)
	{
		$simpleXML = kXml::openXmlFile($dataFilePath);
				
		$inputsForUnitTests = array();
		
		foreach ($simpleXML->UnitTestsData->UnitTestData as $unitTestData)
		{
			$inputs = array();
			
			foreach ($unitTestData->Inputs->Input as $input)
			{
				$object = KalturaUnitTestDataObject::fromXml($input);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);
			}
			
			foreach ($unitTestData->OutputReferences->OutputReference as $output)
			{
				$object = KalturaUnitTestDataObject::fromXml($output);

				//Go to the last and current input and add the variable
				array_push($inputs, $object);
			}
			
			$inputsForUnitTests[] = $inputs;
		}
		
		return $inputsForUnitTests; 
	}

	/**
	 * 
	 * Compares two propel objects and notifies the PHPUnit / Kaltura's listeners
	 * @param BaseObject $outputReference
	 * @param BaseObject $newResult
	 * @return array<> $newErrors, if the objects are equal
	 */
	public function comparePropelObjectsByFields($outputReference, $newResult, $validErrorFields)
	{
		//Gets the data peer of the object (used to geting all the obejct feilds)
		$dataPeer = $outputReference->getPeer(); 
		
		$outputReferenceId = $outputReference->getId();
		$newResultId = $newResult->getId();
		
		//Gets all object feilds
		$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
		
		$newErrors = array();
		
		//Create the xml elements by all fields and their values
		foreach ($fields as $field)
		{
			PHPUnit_Util_Timer::start();
			
			//If the field is in the valid failure list then we skip him 
			if(in_array($field, $validErrorFields))
			{
				continue;
			}
			else 
			{
				$expectedValue = $outputReference->getByName($field);
				$actualValue = $newResult->getByName($field);
				
				//if this is an array we need to change it to a string
				$this->compareOnField($field, $actualValue, $expectedValue, "assertEquals");
			}
		}

		return $newErrors;
	}
	
	/**
	 * 
	 * Compares the $actualValue with the $expectedValue on the given field / property, no Exception is thrown
	 * @param string $fieldName
	 * @param unknown_type $actualValue
	 * @param unknown_type $expectedResult
	 * @throws no exception can be thrown (for mass compares)
	 */
	public function compareOnField($fieldName, $actualValue, $expectedValue, $assertToPerform, $message = null)
	{
		try 
		{
			$this->currentFailure = new KalturaFailure($fieldName, $actualValue, $expectedValue, $assertToPerform, $message);
			$this->$assertToPerform($expectedValue, $actualValue, $this->currentFailure);
		}
		catch (PHPUnit_Framework_AssertionFailedError $e) 
		{
			$this->hasFailures  = true;
			$this->result->addFailure($this, $e, PHPUnit_Util_Timer::stop());
		}
		catch (Exception $e) 
		{
			$this->result->addError($this, $e, PHPUnit_Util_Timer::stop());
		}
	}
}

