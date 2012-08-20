<?php

/**
 * accessControlProfile service base test case.
 */
abstract class AccessControlProfileServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests accessControlProfile->add action
	 * @param KalturaAccessControlProfile $accessControlProfile 
	 * @param KalturaAccessControlProfile $reference
	 * @return KalturaAccessControlProfile
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaAccessControlProfile $accessControlProfile, KalturaAccessControlProfile $reference)
	{
		$resultObject = $this->client->accessControlProfile->add($accessControlProfile);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $resultObject);
		else
			$this->assertType('KalturaAccessControlProfile', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaAccessControlProfile $resultObject
	 */
	protected function validateAdd(KalturaAccessControlProfile $resultObject){}

	/**
	 * Tests accessControlProfile->get action
	 * @param int $id 
	 * @param KalturaAccessControlProfile $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($id, KalturaAccessControlProfile $reference)
	{
		$resultObject = $this->client->accessControlProfile->get($id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $resultObject);
		else
			$this->assertType('KalturaAccessControlProfile', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($resultObject);
	}

	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaAccessControlProfile $resultObject
	 */
	protected function validateGet(KalturaAccessControlProfile $resultObject){}

	/**
	 * Tests accessControlProfile->update action
	 * @param int $id 
	 * @param KalturaAccessControlProfile $accessControlProfile 
	 * @param KalturaAccessControlProfile $reference
	 * @depends testAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testUpdate($id, KalturaAccessControlProfile $accessControlProfile, KalturaAccessControlProfile $reference)
	{
		$resultObject = $this->client->accessControlProfile->update($id, $accessControlProfile);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $resultObject);
		else
			$this->assertType('KalturaAccessControlProfile', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($resultObject);
	}

	/**
	 * Validates testUpdate results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaAccessControlProfile $resultObject
	 */
	protected function validateUpdate(KalturaAccessControlProfile $resultObject){}

	/**
	 * Tests accessControlProfile->delete action
	 * @param int $id 
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->accessControlProfile->delete($id);
	}

	/**
	 * Tests accessControlProfile->listAction action
	 * @param KalturaAccessControlProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaAccessControlProfileListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaAccessControlProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaAccessControlProfileListResponse $reference)
	{
		$resultObject = $this->client->accessControlProfile->listAction($filter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfileListResponse', $resultObject);
		else
			$this->assertType('KalturaAccessControlProfileListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaAccessControlProfileListResponse $resultObject
	 */
	protected function validateListAction(KalturaAccessControlProfileListResponse $resultObject){}

}
