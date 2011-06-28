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
	 * Retruns the test dependency inputs
	 */
	public function getDependencyInputs()
	{
		return $this->dependencyInput;
	}
	
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
		print("In KalturaTestCaseBase::run for test [$this->name]\n");
		if(is_null($result) || !($result instanceof KalturaTestResult))
		{
			$resultClass = get_class($result);

			$result = new KalturaTestResult();	
		}
		else
		{
			KalturaLog::debug("result [" . print_r($result->passed(), true). "]");
		}

		parent::run($result);
	}
	
	/**
	 * Overrides runTest method for the phpunit framework
	 * @see PHPUnit_Framework_TestCase::runTest()
	 */
	public function runTest()
	{
		KalturaLog::debug("In runTest for test [$this->name]\n");
		print("In KalturaTestCaseBase::runTest for test [$this->name]\n");
		
		foreach ($this->dependencyInput as $index => $value)
		{
			KalturaLog::debug("Adding key [$index], value [$value] to the test data\n");
			$this->data[$index] = $value;
		}
	
		$this->dependencyInput = array();
		$this->currentFailure = null;
		
		//if the fraemwork wasn't initiated we need to init here (caused becasue only here we add our listener )
		if(KalturaTestCaseBase::$isFrameworkInit == false)
		{
			$class = get_class($this);
			$classPath = KAutoloader::getClassFilePath($class);
			KalturaTestListener::setFailureFilePath(dirname($classPath) . "/testsData/{$class}.failures");
			KalturaTestListener::setDataFilePath(dirname($classPath) . "/testsData/{$class}.data");

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
	 * @param string $className - The class name
	 * @param string $procedureName - The current method (test) name
	 * @return array<array>();
	 */
	public function provider($className, $procedureName)
	{
		//Gets from the given class the class data file
		$class = get_class($this);
		$classFilePath = KAutoloader::getClassFilePath($class);
		$testClassDir = dirname($classFilePath);
		$dataFilePath = $testClassDir . DIRECTORY_SEPARATOR . "testsData/{$className}.Data";
		
		KalturaLog::debug("The data file path [" . $dataFilePath . "]");
		
		if(file_exists($dataFilePath))
		{
			$simpleXML = kXml::openXmlFile($dataFilePath);
		}
		else
		{
			//TODO: Give notice or create the file don't throw an exception
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
	//	print("In transformToValue [" . print_r($inputObject) . "] \n");
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
				//print("inputAsObject is empty setting to [$name]\n");
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
		elseif($inputAsObject instanceof KalturaObjectBase)// object is Kaltura object base
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
					
//					if($value != null)
//					{
//						print("In setGlobalData value [" . print_r($value, true) . "]\n");
//					}
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
		$inputsAsObjects = array();
		
		$currentIndex = 0;
		foreach ($inputsForTestProcedure as $inputForTestProcedure)
		{
			$inputsAsObjects[] = array();
			$numOfObjects = count($inputForTestProcedure);
			
			foreach ($inputForTestProcedure as $inputObject)
			{
				$objectIndex = 0;
				
				//TOOD: How to skip the Output Reference more nicely
				if($objectIndex == $numOfObjects - 1) //The last object is the output reference
				{
					$objectIndex++; // Not a must
					continue;
				}
				
				$inputAsObject = $inputObject->getDataObject();
				
				//print("Input As object" . print_r($inputAsObject, true) . "\n");
				if(is_null($inputAsObject) || empty($inputAsObject)) //No object is available
				{
					$inputAsObject = $this->transformToValue($inputObject);
				//	print("Input As object is NULL new value is " . print_r($inputAsObject, true) . "\n");
				}
				else //Object is vaild needs to set it's properties from the global data
				{
					$inputAsObject = $this->setGlobalData($inputAsObject);
				}
				
				$inputsAsObjects[$currentIndex][] = $inputAsObject;
				
				$objectIndex++; 
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
	//			print("In compareOnField expectedValue[$expectedValue], isGlobalData[$isGlobalData]\n");
							
				if($isGlobalData)
				{
					//print("In compareOnField name[$expectedValue]\n");
					$expectedValue = KalturaGlobalData::getData($expectedValue);
	//				print("In compareOnField value[$expectedValue]\n");
				}
			}
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

        if (!isset($passedKeys[$dependency])) 
        {
        	$this->result->addError($this, 
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
	        	$trimmedDependency = $this->trimTestInstanceName($dependency);
	        	if(isset($passed[$trimmedDependency]))
	        		$this->dependencyInput[] = $passed[$dependency];
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
    	//print("current dependencies [" . print_r($this->dependencies, true) ."]\n");
    	    	    
    	if (!empty($this->dependencies) && !$this->inIsolation) 
    	{
            $passed     = $this->result->passed();
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
    	print("Creating new KalturaTestResult");
    	return new KalturaTestResult();
    }
}
