<?php
require_once("tests/bootstrapTests.php");

class UserTests extends PHPUnit_Framework_TestCase 
{
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
		// clean up all users for the testing partner
		$c = new Criteria();
		$c->add(kuserPeer::PARTNER_ID, KalturaTestsHelpers::getPartner()->getId());
		$kusers = kuserPeer::doSelect($c);
		foreach($kusers as $kuser)
		{
			$kuser->delete();
		}
	}
	
	public function testDefaultScreenNameIsTheId() 
	{
	    $userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
	    $user = new KalturaUser();
	    $user->id = KalturaTestsHelpers::getRandomString();
		$newUser = $userService->addAction(clone $user);
		
		$this->assertEquals($user->id, $newUser->screenName);
	}
	
	public function testAdd()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$user = $this->prepareUser();
		$newUser = $userService->addAction(clone $user);
		
		$this->assertUser($user, $newUser);
	}
	
	public function testUpdate()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$user = $this->prepareUser();
		$newUser = $userService->addAction(clone $user);
		
		$user = $this->prepareUser();
		$user->id = $newUser->id;
		
		$updatedUser = $userService->updateAction($user->id, clone $user);
		
		$this->assertUser($user, $updatedUser);
		
		$this->assertNotEquals($updatedUser->screenName, $newUser->screenName);
		$this->assertNotEquals($updatedUser->fullName, $newUser->fullName);
		$this->assertNotEquals($updatedUser->email, $newUser->email);
		$this->assertNotEquals($updatedUser->dateOfBirth, $newUser->dateOfBirth);
		$this->assertNotEquals($updatedUser->description, $newUser->description);
		$this->assertNotEquals(trim($updatedUser->tags), trim($newUser->tags));
		$this->assertNotEquals($updatedUser->partnerData, $newUser->partnerData);
	}
	
	public function testUpdateId()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$user = $this->prepareUser();
		$oldId = $user->id;
		$userService->addAction(clone $user);
		
		$user = new KalturaUser();
		$user->id = KalturaTestsHelpers::getRandomString(6);
		
		$updatedUser = $userService->updateAction($oldId, $user);
		
		$this->assertNotEquals($updatedUser->id, $oldId);
		$this->assertEquals($user->id, $updatedUser->id);
	}
	
	public function testGet()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$user = $this->prepareUser();
		$userService->addAction(clone $user);
		
		$getUser = $userService->getAction($user->id);
		
		$this->assertUser($user, $getUser);
	}
	
	public function testDelete()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$user = $this->prepareUser();
		$userService->addAction(clone $user);
		$userService->deleteAction($user->id);
		$getUser = $userService->getAction($user->id);
		
		$this->assertEquals(KalturaUserStatus::DELETED, $getUser->status);
	}
	
	public function testList()
	{
		$userService = KalturaTestsHelpers::getServiceInitializedForAction("user", "list", null, null, KalturaTestsHelpers::getAdminKs());

		$userIds = array();
		for($i = 0; $i < 5; $i++)
		{
			$newUser = $userService->addAction(clone $this->prepareUser());
			$userIds[$newUser->id] = null;
		}
		
		$listResult = $userService->listAction();
		$this->assertEquals(5, $listResult->totalCount);
		
		foreach($listResult->objects as $user)
		{
			$this->assertArrayHasKey($user->id, $userIds);
		}
	}
	
	private function assertUser($expectedUser, $actualUser)
	{
		$this->assertEquals($expectedUser->id, $actualUser->id);
		$this->assertEquals($expectedUser->screenName, $actualUser->screenName);
		$this->assertEquals($expectedUser->fullName, $actualUser->fullName);
		$this->assertEquals($expectedUser->email, $actualUser->email);
		$this->assertEquals($expectedUser->dateOfBirth, $actualUser->dateOfBirth);
		$this->assertEquals($expectedUser->country, $actualUser->country);
		$this->assertEquals($expectedUser->state, $actualUser->state);
		$this->assertEquals($expectedUser->city, $actualUser->city);
		$this->assertEquals($expectedUser->zip, $actualUser->zip);
		$this->assertEquals($expectedUser->thumbnailUrl, $actualUser->thumbnailUrl);
		$this->assertEquals($expectedUser->description, $actualUser->description);
		$this->assertEquals(trim($expectedUser->tags), trim($actualUser->tags));
		$this->assertEquals($expectedUser->gender, $actualUser->gender);
		$this->assertEquals($expectedUser->partnerData, $actualUser->partnerData);
	}
	
	private function prepareUser()
	{
		$user = new KalturaUser();
		$user->id = KalturaTestsHelpers::getRandomString(5);
		$user->screenName = KalturaTestsHelpers::getRandomString(10);
		$user->fullName = KalturaTestsHelpers::getRandomString(12);
		$user->email = KalturaTestsHelpers::getRandomEmail();
		$user->dateOfBirth = KalturaTestsHelpers::getRandomDateAsTimeStamp(true);
		$user->country = KalturaTestsHelpers::getRandomString(2);
		$user->state = KalturaTestsHelpers::getRandomString(2);
		$user->state = KalturaTestsHelpers::getRandomString(4);
		$user->zip = KalturaTestsHelpers::getRandomString(5);
		$user->thumbnailUrl = KalturaTestsHelpers::getRandomString(20);
		$user->description = KalturaTestsHelpers::getRandomText(50);
		$user->tags = KalturaTestsHelpers::getRandomTags(5);
		$user->gender = KalturaGender::FEMALE;
		$user->partnerData = KalturaTestsHelpers::getRandomText(5);
		return $user; 
	}
}
