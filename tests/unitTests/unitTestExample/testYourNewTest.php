
<?php 

//Includes all needed data from the Server / API (Use a subset if you wish)
require_once(dirname(__FILE__) . "/../../bootstrap.php");

/**
 * Represents an example unit test
 */
class ExampleUnitTest extends UnitTestBase
{
	/**
	 * a Test with a data provider
	 * @dataProvider providerTestNothing
	 * Data provided test can't have dependency!!! 
	 */
	public function testNothing($unitTestData)
	{
		//unmark to see exactly what is the unit test data object retrieved from the data provider
//		var_dump($unitTestData);
		
		//An empty assert always true
		$this->assertEquals(true, true);
	}
	
	/**
	 * Provides the data for the nothing unit test
	 * Negates test dependency!!!
	 * @returns array<array<>>
	 * 
	 */
	public function providerTestNothing()
	{
		$inputs = parent::provider(dirname(__FILE__) . "/testsData/exampleTest.Data");
		return $inputs; //return array<array<>>
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
		var_dump($dataForSecondTest);
		$this->assertEquals("WOW i can pass parameters between tests", $dataForSecondTest);
	}
	
	/**
	 * An exmaple of a failed test to dependent on (if this failes then the dependent will be skipped)
	 */
	public function testDependentFail()
	{
		$this->assertEquals(true, false);
	}
	
	/**
	 * This test depends on the result of the previous test
	 * @depends testDependentFail
	 */
	public function testDependsOnFailed($dataForSecondTest)
	{
		//You will not get here... :(
		throw new Exception("This test will be skipped as his dependency failes");
		
	}
}
