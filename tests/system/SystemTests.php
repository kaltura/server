<?php
require_once("tests/bootstrapTests.php");

class SystemTests extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testPing()
	{
		$systemService = KalturaTestsHelpers::getServiceInitializedForAction("system", "ping");
		
		$result = $systemService->pingAction();
		$this->assertTrue($result);
	}
}


