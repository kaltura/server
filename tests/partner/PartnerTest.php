<?php
require_once("tests/bootstrapTests.php");

class PartnerTest extends PHPUnit_Framework_TestCase 
{
	private $partner;
	private $partnerEmail;
	private $partnerName = 'Unit Test Partner';
	private $partnerPassword;
	
	public function setUp() 
	{
		$this->partnerEmail = 'test' . KalturaTestsHelpers::getRandomString(10) . '@kaltura.com';
		list($partnerId, $subPartnerId, $this->partnerPassword) = KalturaTestsHelpers::createPartner($this->partnerName, $this->partnerEmail);

		$this->partner = KalturaTestsHelpers::getPartner($partnerId);
	}
	
	public function tearDown() 
	{
		$this->partner->delete();
	}
	
	public function testGetSecrets() 
	{
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("partner", "getSecrets", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$partner = $adminUserService->getSecretsAction($this->partner->getId(), $this->partnerEmail, $this->partnerPassword);
		
		$this->assertNotNull($partner->secret);
	}
	
	public function testRegister() 
	{
		$partner = new KalturaPartner();
		$partner->fromPartner($this->partner);
		$partner->commercialUse = 'commercial_use';
		
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("partner", "register", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$partner = $adminUserService->registerAction($partner, $this->partnerPassword);
		
		$this->assertNotNull($partner);
	}
	
	public function testUpdate() 
	{
		$partner = new KalturaPartner();
		$partner->fromPartner($this->partner);
		
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("partner", "update", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$updatedPartner = $adminUserService->updateAction(clone $partner);
		
		$this->assertPartner($partner, $updatedPartner);
	}
	
	private function assertPartner($expectedPartner, $actualPartner)
	{
		$this->assertEquals($expectedPartner->id, $actualPartner->id);
		$this->assertEquals($expectedPartner->createdAt, $actualPartner->createdAt);
		$this->assertEquals($expectedPartner->status, $actualPartner->status);
		$this->assertEquals($expectedPartner->partnerPackage, $actualPartner->partnerPackage);
		$this->assertEquals($expectedPartner->secret, $actualPartner->secret);
		$this->assertEquals($expectedPartner->adminSecret, $actualPartner->adminSecret);
		$this->assertEquals($expectedPartner->cmsPassword, $actualPartner->cmsPassword);
		$this->assertEquals($expectedPartner->name, $actualPartner->name);
		$this->assertEquals($expectedPartner->website, $actualPartner->website);
		$this->assertEquals($expectedPartner->notificationUrl, $actualPartner->notificationUrl);
		$this->assertEquals($expectedPartner->appearInSearch, $actualPartner->appearInSearch);
		$this->assertEquals($expectedPartner->adminName, $actualPartner->adminName);
		$this->assertEquals($expectedPartner->adminEmail, $actualPartner->adminEmail);
		$this->assertEquals($expectedPartner->description, $actualPartner->description);
		$this->assertEquals($expectedPartner->commercialUse, $actualPartner->commercialUse);
		$this->assertEquals($expectedPartner->landingPage, $actualPartner->landingPage);
		$this->assertEquals($expectedPartner->userLandingPage, $actualPartner->userLandingPage);
		$this->assertEquals($expectedPartner->contentCategories, $actualPartner->contentCategories);
		$this->assertEquals($expectedPartner->type, $actualPartner->type);
		$this->assertEquals($expectedPartner->phone, $actualPartner->phone);
		$this->assertEquals($expectedPartner->describeYourself, $actualPartner->describeYourself);
		$this->assertEquals($expectedPartner->adultContent, $actualPartner->adultContent);
		$this->assertEquals($expectedPartner->defConversionProfileType, $actualPartner->defConversionProfileType);
		$this->assertEquals($expectedPartner->notify, $actualPartner->notify);
		$this->assertEquals($expectedPartner->allowQuickEdit, $actualPartner->allowQuickEdit);
		$this->assertEquals($expectedPartner->mergeEntryLists, $actualPartner->mergeEntryLists);
		$this->assertEquals($expectedPartner->notificationsConfig, $actualPartner->notificationsConfig);
		$this->assertEquals($expectedPartner->maxUploadSize, $actualPartner->maxUploadSize);
	}
	
	public function testGetInfo() 
	{
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("partner", "getInfo", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$adminUserService->getInfoAction();
	}
	
	public function testGetUsage() 
	{
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("partner", "getUsage", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$partnerUsage = $adminUserService->getUsageAction();
		
		$this->assertNotNull($partnerUsage);
	}
}	


?>