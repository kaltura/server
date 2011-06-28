<?php

class KalturaTestResult extends PHPUnit_Framework_TestResult
{
	/**
	 * 
	 * Enter description here ...
	 */
	public function __destruct()
	{
		print ("KalturaTestResult descructed\n");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestResult::endTest()
	 */
	public function endTest(PHPUnit_Framework_Test $test, $time)
	{
		print("In KalturaTestResult::endTest\n");
		parent::endTest($test, $time);
		
		if (!$this->lastTestFailed && $test instanceof PHPUnit_Framework_TestCase) {
            $class = get_class($test);
            $trimmedTestName = $test->getName();
            $teseName= $test->getName();
            $trimmedTestName = KalturaTestCaseBase::trimTestInstanceName($teseName);

            $this->passed[ $class.'::'.$trimmedTestName] = $test->getResult();
            $this->time                                              += $time;
        }
	}
}