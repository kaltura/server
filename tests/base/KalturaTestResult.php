<?php

class KalturaTestResult extends PHPUnit_Framework_TestResult
{
	/**
	 * 
	 * Creates a new KalturaTestResult object
	 */
	public function __construct(PHP_CodeCoverage $codeCoverage = NULL)
	{
		if(method_exists($this, '__construct'))
			parent::__construct($codeCoverage);
		else
			KalturaLog::alert("Parent don't have __construct method");
	}
	
	/**
	 * 
	 * Called when the KalturaTestResult is destructed
	 */
	public function __destruct()
	{
		print ("KalturaTestResult destructed\n");
	}
	
	/**
	 * 
	 * checks if the listener exists
	 * @param string $listenerType
	 */
	public function isListenerExists($listenerType)
	{
		foreach ($this->listeners as $listener)
		{
			$type = get_class($listener);
			if($type == $listenerType)
				return true;
		}
		
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestResult::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time)
	{
		print("In KalturaTestResult::endTest\n");
		$result = parent::endTest($test, $time);
		
		if (!$this->lastTestFailed && $test instanceof PHPUnit_Framework_TestCase) {
            $class = get_class($test);
            $trimmedTestName = $test->getName();
            $teseName= $test->getName();
            $trimmedTestName = KalturaTestCaseBase::trimTestInstanceName($teseName);

            $this->passed[ $class.'::'.$trimmedTestName] = $test->getResult();
            $this->time                                              += $time;
        }
        
        return $result; 
	}
}