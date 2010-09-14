<?php
require_once("tests/bootstrapTests.php");

class AccessControlTests extends PHPUnit_Framework_TestCase 
{
	private $createdAccessControls = array();
	
	public function setUp() 
	{
	}
	
	public function tearDown() 
	{
		parent::tearDown();
		
		foreach($this->createdAccessControls as $accessControlId)
		{
			$accessControl = accessControlPeer::retrieveByPKNoFilter($accessControlId);
			if ($accessControl)
				$accessControl->delete();
		}
		$this->createdAccessControls = array();
	}
	
	public function testAdd()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$this->assertAccessControl($accessControl, $newAccessControl);
	}
	
	public function testUpdate()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$accessControl = $this->prepareAccessControl($newAccessControl);
		$originalAccessControl = clone $accessControl;
		
		$accessControl->id = null;
		$accessControl->partnerId = null;
		$accessControl->createdAt = null;
		$updatedAccessControl = $accessControlService->updateAction($originalAccessControl->id, clone $accessControl);
		
		$this->assertAccessControl($originalAccessControl, $updatedAccessControl);
		
		$this->assertNotEquals($updatedAccessControl->name, $newAccessControl->name);
		$this->assertNotEquals($updatedAccessControl->description, $newAccessControl->description);
	}
	
	public function testUpdateId()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$accessControl = new KalturaAccessControl();
		$accessControl->id = KalturaTestsHelpers::getRandomNumber(1, 999999);
		
		try{
			$updatedAccessControl = $accessControlService->updateAction($newAccessControl->id, $accessControl);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->fail('Update id should not work');
	}
	
	public function testUpdatePartnerId()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$accessControl = new KalturaAccessControl();
		$accessControl->partnerId = KalturaTestsHelpers::getRandomNumber(1, 999999);
		
		try{
			$updatedAccessControl = $accessControlService->updateAction($newAccessControl->id, $accessControl);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->fail('Update partnerId should not work');
	}

	
	public function testUpdateCreatedAt()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$accessControl = new KalturaAccessControl();
		$accessControl->createdAt = time() + 100;
		
		try{
			$updatedAccessControl = $accessControlService->updateAction($newAccessControl->id, $accessControl);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->fail('Update createdAt should not work');
	}
	
	public function testGet()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		
		$getAccessControl = $accessControlService->getAction($newAccessControl->id);
		
		$this->assertAccessControl($accessControl, $getAccessControl);
	}
	
	public function testDelete()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "add", null, null, KalturaTestsHelpers::getAdminKs());
		
		$accessControl = $this->prepareAccessControl();
		$newAccessControl = $accessControlService->addAction(clone $accessControl);
		$this->createdAccessControls[] = $newAccessControl->id;
		$accessControlService->deleteAction($newAccessControl->id);
	
		try{
			$getAccessControl = $accessControlService->getAction($newAccessControl->id);
		}
		catch(KalturaAPIException $e)
		{
			return;
		}
		
		$this->assertNull($getAccessControl);
	}
	
	public function testList()
	{
		$accessControlService = KalturaTestsHelpers::getServiceInitializedForAction("accesscontrol", "list", null, null, KalturaTestsHelpers::getAdminKs());

		$accessControlIds = array();
		for($i = 0; $i < 5; $i++)
		{
			$newAccessControl = $accessControlService->addAction(clone $this->prepareAccessControl());
			$this->createdAccessControls[] = $newAccessControl->id;
			$accessControlIds[$newAccessControl->id] = null;
		}
		
		$listResult = $accessControlService->listAction();
		$this->assertEquals(5, $listResult->totalCount);
		
		foreach($listResult->objects as $accessControl)
		{
			$this->assertArrayHasKey($accessControl->id, $accessControlIds);
		}
	}
	
	// finds specific values that enable comparison between different KalturaBaseRestriction items
	// the search result is not important as long as it will always returns the same order
	private function findRestrictionSortable($restriction)
	{
		if($restriction instanceof KalturaSiteRestriction)
			return $restriction->siteList;
		if($restriction instanceof KalturaSessionRestriction)
			return 0;
		if($restriction instanceof KalturaPreviewRestriction)
			return $restriction->previewLength;
		if($restriction instanceof KalturaDirectoryRestriction)
			return $restriction->directoryRestrictionType;
		if($restriction instanceof KalturaCountryRestriction)
			return $restriction->countryList;
	}
	
	private function sortRestriction($a, $b)
	{
		if(!($a instanceof KalturaBaseRestriction))
			return 0;
			
		if(!($b instanceof KalturaBaseRestriction))
			return 0;
		
		$sortable_a = $this->findRestrictionSortable($a);
		$sortable_b = $this->findRestrictionSortable($b);
		
		if($sortable_a == $sortable_b)
			return 0;
		
		if($sortable_a > $sortable_b)
			return 1;
			
		return -1;
	}
	
	// converts KalturaAccessControlArray to array to enable sorting
	private function restrictionsToArray($restrictions)
	{
		$arr = array();
		foreach($restrictions as $restriction)
			$arr[] = $restriction;
			
		return $arr;
	}
	
	private function assertAccessControl($expectedAccessControl, $actualAccessControl)
	{
		$this->assertEquals($expectedAccessControl->partnerId, $actualAccessControl->partnerId);
		$this->assertEquals($expectedAccessControl->name, $actualAccessControl->name);
		$this->assertEquals($expectedAccessControl->description, $actualAccessControl->description);
		$this->assertEquals($expectedAccessControl->createdAt, $actualAccessControl->createdAt);
		
		// indexes are not saved in the db, this sorting enable comparison of the values
		$expectedRestrictions = $this->restrictionsToArray($expectedAccessControl->restrictions);
		$actualRestrictions = $this->restrictionsToArray($actualAccessControl->restrictions);
		usort($expectedRestrictions, array($this, 'sortRestriction'));
		usort($actualRestrictions, array($this, 'sortRestriction'));
		
		$this->assertEquals($expectedRestrictions, $actualRestrictions);
	}
	
	private function prepareAccessControl($accessControl = null)
	{
		if(is_null($accessControl))
		{
			$accessControl = new KalturaAccessControl();
			$accessControl->partnerId = KalturaTestsHelpers::getPartner()->getId();
			$accessControl->createdAt = time();
		}
		else
		{
			$accessControl = clone $accessControl;
		}
		
		$accessControl->name = KalturaTestsHelpers::getRandomString(12);
		$accessControl->description = KalturaTestsHelpers::getRandomString(30);
		$accessControl->restrictions = AccessControlTestsHelpers::getDummyRestrictions();
		
		return $accessControl; 
	}
}

?>