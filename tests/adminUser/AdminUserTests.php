<?php
require_once("tests/bootstrapTests.php");

class AdminUserTests extends PHPUnit_Framework_TestCase 
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
	
	public function testUpdatePassword() 
	{
		$newPassword = KalturaTestsHelpers::getRandomString(10);
		$newEmail = 'test' . KalturaTestsHelpers::getRandomString(10) . '@kaltura.com';;
		
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("adminuser", "updatePassword", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$adminUser = $adminUserService->updatePasswordAction($this->partnerEmail, $this->partnerPassword, $newEmail, $newPassword);
		
		$this->assertEquals($newPassword, $adminUser->password);
		$this->assertEquals($newEmail, $adminUser->email);
		
		$this->partnerEmail = $adminUser->email;
		$this->partnerPassword = $adminUser->password;
	}
	
	public function testResetPassword() 
	{
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("adminuser", "resetPassword", $this->partner->getId(), null, KalturaTestsHelpers::getAdminKs($this->partner->getId()));
		$adminUserService->resetPasswordAction($this->partnerEmail);
	}
	
	public function testLogin() 
	{
		$adminUserService = KalturaTestsHelpers::getServiceInitializedForAction("adminuser", "login");
		$ks = $adminUserService->loginAction($this->partnerEmail, $this->partnerPassword);
		
		$this->assertNotNull($ks);
	}
}


?>