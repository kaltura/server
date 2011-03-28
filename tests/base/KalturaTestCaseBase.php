<?php

/**
 * Represents a base test case for a general test
 * @author Roni
 *
 */
class KalturaTestCaseBase extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * Indicates wheter the test framework was initialized 
	 * @var bool
	 */
	private static $isFrameworkInit = false;
	
	/**
	 * @var string
	 */
	protected $outputFolder;
	
	/**
	 * @var string
	 */
	protected $testFolder;
	
	/**
	 * @var KalturaTestConfig
	 */
	protected $config;
	
	/**
	 * @var KalturaTestsSource
	 */
	protected $dataSource;

	/**
	 * 
	 * Indicates wheter the test has failures
	 * @var bool
	 */
	private $hasFailures = false;
		
	/**
	 * 
	 * Holds the current failure in the test
	 * @var KalturaTestCaseFailure
	 */
	private $currentFailure = null;
	
	/**
	 * @return the $isFrameworkInit
	 */
	public static function getIsFrameworkInit() {
		return KalturaTestCaseBase::$isFrameworkInit;
	}

	/**
	 * @param bool $isFrameworkInit
	 */
	public static function setIsFrameworkInit($isFrameworkInit) {
		KalturaTestCaseBase::$isFrameworkInit = $isFrameworkInit;
	}

	/**
	 * @return the $hasFailures
	 */
	public function getHasFailures() {
		return $this->hasFailures;
	}

	/**
	 * @return the $currentFailure
	 */
	public function getCurrentFailure() {
		return $this->currentFailure;
	}

	/**
	 * @param bool $hasFailures
	 */
	public function setHasFailures($hasFailures) {
		$this->hasFailures = $hasFailures;
	}

	/**
	 * @param KalturaTestCaseFailure $currentFailure
	 */
	public function setCurrentFailure($currentFailure) {
		$this->currentFailure = $currentFailure;
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
			
		KalturaLog::info("Loads config file [$testFilePath.ini]");
		$this->config = new KalturaTestConfig("$testFilePath.ini");
		$testConfig = $this->config->get('config');
		if(!$testConfig)
		{
			$testConfig = new Zend_Config(array(
				'source' => KalturaTestSource::INI,
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
	 * @throws KalturaTestException
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
			
		switch($argConfig->objectType)
		{
			case 'dependency':
				throw new KalturaTestException("Argument [$argName] taken from dependency");
				
			case 'array':
				return array();
				
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
	protected function populateObject(Zend_Config $config)
	{
		if(!$config->objectType)
			return null;
			
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
		if(!$methodConfig)
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
				catch (KalturaTestException $e)
				{
					KalturaLog::log($e->getMessage());
				}
			}
				
			$tests[] = $test;
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
				return $this->provideTestData($className, $methodName);
			}
			catch (Exception $e)
			{
				KalturaLog::err($e->getMessage());
				throw $e;
			}
		}
			
		throw new Exception('Calling method not found');
	}

	/**
	 * Overrides runTest method for the phpunit framework
	 * @see PHPUnit_Framework_TestCase::runTest()
	 */
	public function runTest()
	{
		print("In RunTest\n");

		foreach ($this->dependencyInput as $index => $value)
			$this->data[$index] = $value;
		$this->dependencyInput = array();
		
		$this->currentFailure = null;
		
		//if the fraemwork wasn't initiated we need to init here (caused becasue only here we add our listener )
		if(KalturaTestCaseBase::$isFrameworkInit == false)
		{
			$class = get_class($this);
			$classPath = KAutoloader::getClassFilePath($class);
			KalturaTestListener::setFailureFilePath(dirname($classPath) . "/testsData/{$class}.failures");

			//add Listener from config with all params such as: when to report
			$this->result->addListener(new KalturaTestListener());
			
			KalturaTestCaseBase::$isFrameworkInit = true;

			//If the test case failures wasn't added
			if(KalturaTestListener::getTestCaseFailures() == null)
			{
				KalturaTestListener::setCurrentTestCase($class);
					
				//Then add the test case failure
				KalturaTestListener::setTestCaseFailures(new KalturaTestCaseFailures(KalturaTestListener::getCurrentTestCase()));
				$testCaseFailures = KalturaTestListener::getTestCaseFailures();
				$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure($this->getName(false)));
			}
		}
		
		$testResult = parent::runTest();
		return $testResult;
	}

	/**
	 * 
	 * The test data provider (gets the data for the different tests)
	 * @param string $dataFilePath - the data file path (with the objects)
	 * @return array<array>();
	 */
	public function provider($procedureName)
	{
		//Gets from the given class the class data file
		$class = get_class($this);
		$classFilePath = KAutoloader::getClassFilePath($class);
		$testClassDir = dirname($classFilePath);
		$dataFilePath = $testClassDir . DIRECTORY_SEPARATOR . "testsData/{$this->name}.Data";
		
		if(file_exists($dataFilePath))
		{
			$simpleXML = kXml::openXmlFile($dataFilePath);
		}
		else
		{
			//TODO: give notice or create the file don't throw an exception
			throw new Exception("Data file not found");
		}		
		
		$inputsForTestProcedure = array();
		
		foreach ($simpleXML->TestProcedureData as $xmlTestProcedureData)
		{
			if($xmlTestProcedureData["testProcedureName"] != $procedureName)
			{
				continue;
			}
								
			foreach($xmlTestProcedureData->TestCaseData as $xmlTestCaseData)
			{
				$testCaseInstanceInputs = array();
				
				foreach ($xmlTestCaseData->Input as $input)
				{
					$object = KalturaTestDataObject::generatefromXml($input);

					//Add the new input to the test case instance data
					$testCaseInstanceInputs[]  = $object;
				}
				
				foreach ($xmlTestCaseData->OutputReference as $output)
				{
					$object = KalturaTestDataObject::generatefromXml($output);
	
					//Add the new output reference to the test case instance data
					$testCaseInstanceInputs[]  = $object;
				}
			
				//Add the test case into the test procedure data
				$inputsForTestProcedure[] = $testCaseInstanceInputs;	
			}
		}
		
		return $inputsForTestProcedure; 
	}
	
	/**
	 * 
	 * Compares the $actualValue with the $expectedValue on the given field / property, no Exception is thrown
	 * @param string $fieldName
	 * @param object $actualValue
	 * @param object $expectedResult
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

	/**
	 * 
	 * Adds a new failure for this test case
	 * @param KalturaFailure $kalturaFailure
	 */
	public function addFailure(KalturaFailure $kalturaFailure)
	{
		$this->currentFailure = $kalturaFailure;
		$this->hasFailures  = true;
		$this->result->addFailure($this, $kalturaFailure, PHPUnit_Util_Timer::stop());
	}
	
	/**
	 * 
	 * Adds a new error for this test case
	 * @param Exception $e
	 */
	public function addError(Exception $e)
	{
		$this->result->addError($this, $e, PHPUnit_Util_Timer::stop());
	}
}
