<?php
require_once("tests/bootstrapTests.php");

class SessionTest extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
	}
	
	public function testStartUserSession() 
	{
		$partner = KalturaTestsHelpers::getPartner();
		$secret = $partner->getSecret();
		
		$sessionService = KalturaTestsHelpers::getServiceInitializedForAction("session", "start");
		$ks = $sessionService->startAction($secret, "user" . time(), KalturaSessionType::USER, $partner->getId());
		
		$this->assertNotNull($ks);
	}
	
	public function testStartUserWrongSecret() 
	{
		$partner = KalturaTestsHelpers::getPartner();
		$secret = KalturaTestsHelpers::getRandomString(30);
		
		$sessionService = KalturaTestsHelpers::getServiceInitializedForAction("session", "start");
		
		try{
			$ks = $sessionService->startAction($secret, "user" . time(), KalturaSessionType::USER, $partner->getId());
		}catch(KalturaAPIException $e){
			return;
		}

		$this->fail('Started user session with wrong secret');
	}
	
	public function testStartAdminSession() 
	{
		$partner = KalturaTestsHelpers::getPartner();
		$secret = $partner->getAdminSecret();
		
		$sessionService = KalturaTestsHelpers::getServiceInitializedForAction("session", "start");
		$ks = $sessionService->startAction($secret, "admin" . time(), KalturaSessionType::ADMIN, $partner->getId());
		
		$this->assertNotNull($ks);
	}
	
	public function testStartAdminWrongSecret() 
	{
		$partner = KalturaTestsHelpers::getPartner();
		$secret = $partner->getSecret();
		
		$sessionService = KalturaTestsHelpers::getServiceInitializedForAction("session", "start");
		
		try{
			$ks = $sessionService->startAction($secret, "admin" . time(), KalturaSessionType::ADMIN, $partner->getId());
		}catch(KalturaAPIException $e){
			return;
		}

		$this->fail('Started admin session with user secret');
	}
	
}


?>