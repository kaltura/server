<?php
class KalturaTestSuite extends PHPUnit_Framework_TestSuite
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestSuite::run()
	 */
	public function run(PHPUnit_Framework_TestResult $result = NULL, $filter = FALSE, array $groups = array(), array $excludeGroups = array(), $processIsolation = FALSE)
	{
		$name = $this->getName();
		print("In KalturaTestSuite::run() for suite [$name ]\n");
		
		return parent::run($result, $filter, $groups, $excludeGroups, $processIsolation);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestSuite::createResult()
	 */
	protected function createResult()
	{
		return new KalturaTestResult();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestSuite::runTest()
	 */
	public function runTest(PHPUnit_Framework_Test $test, PHPUnit_Framework_TestResult $result)
	{
		print("In KalturaTestSuite::runTest() for test [" . $test->getName() ."]\n");
		
		return parent::runTest($test, $result);
	}
}