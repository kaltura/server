<?php

/**
 * Represents a base test case for a general test
 * @author Roni
 *
 */
class KalturaTestCaseBase extends PHPUnit_Framework_TestCase
{	
	/**
	 * The test nothing method (so we won't get warnings for empty tests)
	 * @var string
	 */
	const TEST_NOTHING_TEST_NAME = "testNothing";
	
	/**
	 * 
	 * Retruns the test dependency inputs
	 */
	public function getDependencyInputs()
	{
		return $this->dependencyInput;
	}
	
	/**
	 * 
	 * Holds all the test inputs
	 * @var array()
	 */
	protected $inputs = array();
	
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
	 * 
	 * Returns the inputs for the test
	 */
	public function getInputs()
	{
		return $this->inputs;
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
			$array[$index] = $value;
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
				if($this->dataSource == KalturaTestSource::INI)
				{
					return $this->provideTestData($className, $methodName);
				}
				elseif($this->dataSource == KalturaTestSource::XML)
				{
					return $this->provider($className, $methodName);
				}
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
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::run()
	 */
	public function run(PHPUnit_Framework_TestResult $result = null)
	{
		$name = $this->getName(true);
		KalturaLog::debug("In KalturaTestCaseBase::run for test [$name]\n");
		//print("In KalturaTestCaseBase::run for test [$name]\n");
		
		if($name != KalturaTestCaseBase::TEST_NOTHING_TEST_NAME) //we init the framework for all tests but hte test nothing test
			$this->initFramework($result);
		
		try {
			$result = parent::run($result);	
		}
		catch(Exception $e)
		{
			$this->fail("Exception was raised during running of the test: " . $e->getMessage());
		}
		
						
		return $result; 
	}
	
	/**
	 * 
	 * Initializes the framework for all the tests
	 */
	protected function initFramework(&$result = null)
	{
		//if we got codeCoverage then we add the rest of the server folders
		$codeCoverageFilter = PHP_CodeCoverage_Filter::getInstance();
		
		if($codeCoverageFilter)
		{
			$testsBaseDir = dirname(__FILE__);
			
			$whiteList = $codeCoverageFilter->getWhitelist();
			if(!in_array("$testsBaseDir/../../api_v3", $whiteList))
			{
//				KalturaLog::debug("Adding" . $testsBaseDir . "/../../api_v3" . "to the CodeCoverage filter\n");
				//$codeCoverageFilter->addDirectoryToWhitelist($testsBaseDir . "/../../api_v3/services");
				//$codeCoverageFilter->addDirectoryToWhitelist($testsBaseDir . "/../../api_v3/lib");
			}
			
			if(!in_array("$testsBaseDir/../../plugins", $whiteList))
			{
//				KalturaLog::debug("Adding" . $testsBaseDir . "/../../plugins" . "to the CodeCoverage filter\n");
//				$codeCoverageFilter->addDirectoryToWhitelist($testsBaseDir . "/../../plugins");
			}
		}
		
		if($result)
		{
			if($result instanceof KalturaTestResult)
			{
				if(!$result->isListenerExists('KalturaTestListener'))
				{
					KalturaLog::debug("adding KalturaTestListener to result\n");
					$result->addListener(new KalturaTestListener());
				}
			}
		}

		if(KalturaTestCaseBase::$isFrameworkInit == false)
		{
			KalturaLog::debug("Initing framework\n");
			$class = get_class($this);
			$classPath = KAutoloader::getClassFilePath($class);
			KalturaTestListener::setFailureFilePath(dirname($classPath) . "/testsData/{$class}.failures");
			KalturaTestListener::setDataFilePath(dirname($classPath) . "/testsData/{$class}.data");
			KalturaTestListener::setTotalFailureFilePath(KALTURA_TESTS_PATH . "/common/totalFailures.failures");
			
			$result->addListener(new KalturaTestListener());
			
			KalturaTestCaseBase::$isFrameworkInit = true;

			//If the test case failures wasn't added
			if(KalturaTestListener::getTestCaseFailures() == null)
			{
				KalturaTestListener::setCurrentTestCase($class);
					
				//Then add the test case failure
				KalturaTestListener::setTestCaseFailures(new KalturaTestCaseFailures(KalturaTestListener::getCurrentTestCase()));
				$testCaseFailures = KalturaTestListener::getTestCaseFailures();
				$testProcedureName = $this->getName(false);
				
				$testProcedure = $testCaseFailures->getTestProcedureFailure($testProcedureName);

				if(!$testProcedure)
					$testCaseFailures->addTestProcedureFailure(new KalturaTestProcedureFailure());
				else
					KalturaLog::alert("Test procedure [$testProcedureName] already exists");
			}
		}
	}
	
	/**
	 * Overrides runTest method for the phpunit framework
	 * @see PHPUnit_Framework_TestCase::runTest()
	 */
	public function runTest()
	{
		$name = $this->getName(true);
		KalturaLog::debug("In runTest for test [$name]\n");
		//print("In KalturaTestCaseBase::runTest for test [$name]\n");
		
		foreach ($this->getDependencyInputs() as $index => $value)
		{
			KalturaLog::debug("Adding key [$index], value [$value] to the test data\n");
			
			//TODO: fix access to data
			$this->data[$index] = $value;
		}

		$this->currentFailure = null;

		try {
		$testResult = parent::runTest();
		}
		catch (Exception $e)
		{
			$this->fail("Exception was raised during running of the test: " . $e->getMessage());	
		}
		
		return $testResult;
	}

	/**
	 * 
	 * The add test result 
	 * @var mixed
	 */
	protected $testAddResult;
	
	/**
	 * 
	 * The test data provider (gets the data for the different tests)
	 * @param string $className - The class name
	 * @param string $procedureName - The current method (test) name
	 * @return array<array>();
	 */
	public function provider($className, $procedureName)
	{
		//print("In provider for $className, $procedureName \n");
		
		//Gets from the given class the class data file
		$class = get_class($this);
		$classFilePath = KAutoloader::getClassFilePath($class);
		$testClassDir = dirname($classFilePath);
		$dataFilePath = $testClassDir . DIRECTORY_SEPARATOR . "testsData/{$className}.data";
		
		KalturaLog::debug("The data file path [" . $dataFilePath . "]");
		
		if(file_exists($dataFilePath))
		{
			$simpleXML = kXml::openXmlFile($dataFilePath);
		}
		else
		{
			//TODO: Give notice or create the file don't throw an exception
			throw new Exception("Data file [$dataFilePath] not found");
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
	
		KalturaLog::info("Tests data provided Before transformation to objects: \n[" . print_r($inputsForTestProcedure, true) . "]");
		
		$inputsForTestProcedure = $this->transformToObjects($inputsForTestProcedure);
		
		KalturaLog::info("Tests data provided [" . print_r($inputsForTestProcedure, true) . "]");
	
		return $inputsForTestProcedure; 
	}
		
	/**
	 * 
	 * Transforms the the given input object into the right value (including getting from global data)
	 * @param KalturaTestDataObject $inputObject
	 */
	protected function transformToValue($inputObject)
	{
		$inputAsObject =  $inputObject->getValue();
					
		$isGlobalData = KalturaGlobalData::isGlobalData($inputAsObject);
		KalturaLog::debug("isGlobalData [$isGlobalData] \n");
		if($isGlobalData)
		{
			$name = $inputAsObject;
			$inputAsObject = KalturaGlobalData::getData($name);
			KalturaLog::debug("inputAsObject global name [$name] value [$inputAsObject]\n");

			//TODO: check if needed
			if(!$inputAsObject)
			{
				$inputAsObject = $name;
			}
		}
		
		return $inputAsObject;
	}
	
	/**
	 * 
	 * Gets an object and set it's params with the global data 
	 * @param unknown_type $inputAsObject
	 */
	protected function setGlobalData($inputAsObject)
	{
		KalturaLog::debug("Settign the object global data fields");
		
		if($inputAsObject instanceof BaseObject)
		{
			//Gets the data peer of the object (used to geting all the obejct feilds)
			$dataPeer = $inputAsObject->getPeer(); 
			
			//Gets all object fields
			$fields = call_user_func(array($dataPeer, "getFieldNames"), BasePeer::TYPE_PHPNAME);
			
			//Create the xml elements by all fields and their values
			foreach ($fields as $field)
			{
				$value = $inputAsObject->getByName($field);
				$isGlobal = KalturaGlobalData::isGlobalData($value);
				if($isGlobal)
				{
					$value = KalturaGlobalData::getData($value);
				}
			}
		}
		elseif($inputAsObject instanceof KalturaObjectBase || $inputAsObject instanceof KalturaObject)// Object is Kaltura object base
		{
			$reflector = new ReflectionClass($inputAsObject);
			$properties = $reflector->getProperties(ReflectionProperty::IS_PUBLIC);
						
			foreach ($properties as $property)
			{
				$value = $property->getValue($inputAsObject);
				$propertyName = $property->getName();
								
				$isGlobal = KalturaGlobalData::isGlobalData($value);
				if($isGlobal)
				{
					$value = KalturaGlobalData::getData($value);
					$property->setValue($inputAsObject, $value);
				}
			}
		}
		else 
		{
			KalturaLog::debug("Input as object is not an object returning null!!!");
			$inputAsObject = null;
		}
		
		return $inputAsObject;
	}
	
	/**
	 * 
	 * Transforms the test data from an array KalturaTestDataObject to the real objects 
	 * @param array<KalturaTestDataObject> $inputsForTestProcedure
	 */
	protected function transformToObjects(array $inputsForTestProcedure)
	{
		KalturaLog::debug("Transforming to inputs objects");
		
		$inputsAsObjects = array();
		
		$currentIndex = 0;
		foreach ($inputsForTestProcedure as $inputForTestProcedure)
		{
			$inputsAsObjects[] = array();
						
			foreach ($inputForTestProcedure as $inputObject)
			{
				$inputAsObject = $inputObject->getDataObject();
				
				if(is_object($inputAsObject))
					KalturaLog::debug("the input object is: " . get_class($inputAsObject));
				else 
					KalturaLog::debug("the input object is not an object!");
					
				if(is_null($inputAsObject) || empty($inputAsObject)) //No object is available
				{
					$inputAsObject = $this->transformToValue($inputObject);
				}
				else //Object is vaild needs to set it's properties from the global data
				{
					$inputAsObject = $this->setGlobalData($inputAsObject);
				}

				//If we have an empty object we set it to be null instead of empty string
				$objectType = $inputObject->getType();
				if(class_exists($objectType) && empty($inputAsObject))
					$inputAsObject = null;	
				
				$inputsAsObjects[$currentIndex][] = $inputAsObject;
			}
			
			$currentIndex++;
		}
		
		return $inputsAsObjects;
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
			
			if(!is_null($expectedValue))
			{
				$isGlobalData = KalturaGlobalData::isGlobalData($expectedValue);
							
				if($isGlobalData)
				{
					$expectedValue = KalturaGlobalData::getData($expectedValue);
				}
			}
			$this->$assertToPerform($expectedValue, $actualValue, $this->currentFailure);
		}
		catch (PHPUnit_Framework_AssertionFailedError $e) 
		{
			$this->hasFailures  = true;
			$resultObject = $this->getTestResultObject();

			if($resultObject)
			{
				$resultObject->addFailure($this, $e, PHP_Timer::stop());
			}
			else
			{
				$name = $this->getName(true);
				//print("In add failure Result is NULL for test [$name]\n");
			}
			
		}
		catch (Exception $e) 
		{
			$this->getTestResultObject()->addError($this, $e, PHP_Timer::stop());
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
		$this->result->addFailure($this, $kalturaFailure, PHP_Timer::stop());
	}
	
	/**
	 * 
	 * Adds a new error for this test case
	 * @param Exception $e
	 */
	public function addError(Exception $e)
	{
		$this->result->addError($this, $e, PHP_Timer::stop());
	}
	
	/**
	 * 
	 * Handles a given dependency and sets it's dependency input
	 * @param unknown_type $dependency
	 * @param array $passed
	 * @param array $passedKeys
	 */
	protected function handleDependency($dependency, array $passed, array $passedKeys)
	{
		$className  = get_class($this);
		
        if (strpos($dependency, '::') === FALSE) //Sets the test class name if not given
        	$dependency = $className . '::' . $dependency;

        $trimmedDependency = $this->trimTestInstanceName($dependency);
		$fullDependency = $dependency . " with data set #0"; //Checks for the first finish //TODO: fix to support multi data
	        	
        if (!isset($passedKeys[$dependency])&& 
        	!isset($passedKeys[$fullDependency]) && //No dependency found
        	!isset($passedKeys[$trimmedDependency])) 
        {
        	$this->getTestResultObject()->addError($this, 
				new PHPUnit_Framework_SkippedTestError(	sprintf('This test depends on "%s" to pass.', $dependency)),
				0);
           return FALSE;
        }
        else 
        {
			if (isset($passed[$dependency])) 
	        {
	        	$this->dependencyInput[] = $passed[$dependency]; //return the dependency input
	        	
	        	//TODO: add dependency between suites so we can have nested arrays of dependencies
//	        	if(is_array($passed[$dependency]))
//	        		$this->dependencyInput[] = $passed[$dependency][0]; // currently returns the first result form the data provider
	        }
	        else // we search to see if a test was ended in that test suite 
	        {
	        	if(isset($passed[$trimmedDependency]))
	        		$this->dependencyInput[] = $passed[$trimmedDependency];
	        	else if(isset($passed[$fullDependency ]))
	        			$this->dependencyInput[] = $passed[$fullDependency];
	        	else
	        		$this->dependencyInput[] = NULL;
	        }
        }
	}
	
	/**
	 * 
	 * Gets a test name and returns it trimmed name without the instance name 
	 * @param string $testName
	 * @return string - the new trimmed test nema
	 */
	public static function trimTestInstanceName($testName)
	{
		$pos = strpos($testName, ' with data set');
		
		if ($pos !== FALSE) {
			$testName = substr($testName, 0, $pos);
		}
		
		return $testName;
	}
	
	/**
     * @since Method available since Release 3.5.4
     */
    protected function handleDependencies()
    {
    	if (!empty($this->dependencies) && !$this->inIsolation) 
    	{
            $passed     = $this->getTestResultObject()->passed();
            $passedKeys = array_keys($passed);
            $numKeys    = count($passedKeys);
        	
            for ($i = 0; $i < $numKeys; $i++) 
            {
            	$passedKeys[$i] = KalturaTestCaseBase::trimTestInstanceName($passedKeys[$i]);
	        }
		
            $passedKeys = array_flip(array_unique($passedKeys));

            foreach ($this->dependencies as $dependency) 
            {
            	$this->handleDependency($dependency, $passed, $passedKeys);
            }
        }

        return TRUE;
    }
            
    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::createResult()
     */
    protected function createResult()
    {
    	return new KalturaTestResult();
    }

    /**
	 * Just a test function so this "test" will have tests as well 
     */
    public function testNothing()
    {return;}
}
