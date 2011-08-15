
<?php 

//Includes all needed data from the Server / API (Use a subset if you wish)
require_once(dirname(__FILE__) . "/../../bootstrap.php");

/**
 * Represents an example unit test
 */
class ExampleUnitTest extends KalturaTestCaseBase
{
	/**
	 * a Test with a data provider
	 * @dataProvider provider
	 */
	public function testNothing($unitTestData)
	{
		//unmark to see exactly what is the unit test data object retrieved from the data provider
//		var_dump($unitTestData);
		
		//An empty assert always true
		$this->assertEquals(true, true);
	}
		
	/**
	 * An example test with test dependency
	 * Test dependency can't be with data provider!!!
	 */
	public function testDependentSuccess()
	{
		$this->assertEquals(true, true);
		
		//This generate the data for the second test only if this succeed the second test will run
		return "WOW i can pass parameters between tests";
	}
	
	/**
	 * This test depends on the result of the previous test
	 * @depends testDependentSuccess
	 */
	public function testDependsOnSuccess($dataForSecondTest)
	{
		$this->assertEquals("WOW i can pass parameters between tests", $dataForSecondTest);
	}
	
	/**
	 * An exmaple of a failed test to dependent on (if this failes then the dependent will be skipped)
	 */
	public function testDependentFail()
	{
		//If unmark this will fail
		//$this->assertEquals(true, false);
	}
	
}
