<?php
require_once("tests/bootstrapTests.php");

class InvokeTests extends PHPUnit_Framework_TestCase
{
	public function testBasicInvoke()
	{
		$requestParams = array(
			"ks" => KalturaTestsHelpers::getAdminKs()
		);
		
		$dispatcher = KalturaDispatcher::getInstance();
		$partner = $dispatcher->dispatch("partner", "getInfo", $requestParams);
		$this->assertEquals(KalturaTestsHelpers::getPartner()->getId(), $partner->id);
	}
	
	public function testInvokeOfInvalidServiceOrAction()
	{
		$dispatcher = KalturaDispatcher::getInstance();
		try 
		{
			$dispatcher->dispatch("noservice", "noaction", array());
		}
		catch(KalturaAPIException $ex)
		{
			$dummyEx = new KalturaAPIException(KalturaErrors::SERVICE_DOES_NOT_EXISTS, "noservice");
			$this->assertSame($dummyEx->getCode(), $ex->getCode());
			return; 
		}
		
		$this->fail("Expecting exception");
	}
}