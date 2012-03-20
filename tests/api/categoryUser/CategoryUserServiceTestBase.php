<?php

/**
 * categoryUser service base test case.
 */
abstract class CategoryUserServiceTestBase extends KalturaApiTestCase
{
	/**
	 * Tests categoryUser->add action
	 * @param KalturaCategoryUser $categoryUser 
	 * @param KalturaCategoryUser $reference
	 * @return KalturaCategoryUser
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaCategoryUser $categoryUser, KalturaCategoryUser $reference)
	{
		$resultObject = $this->client->categoryUser->add($categoryUser);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaCategoryUser', $resultObject);
		else
			$this->assertType('KalturaCategoryUser', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($resultObject);
		
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaCategoryUser $resultObject
	 */
	protected function validateAdd(KalturaCategoryUser $resultObject){}

	/**
	 * Tests categoryUser->get action
	 * @param int $categoryId 
	 * @param string $userId 
	 * @param KalturaCategoryUser $reference
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet($categoryId, $userId, KalturaCategoryUser $reference)
	{
		$resultObject = $this->client->categoryUser->get($categoryId, $userId);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaCategoryUser', $resultObject);
		else
			$this->assertType('KalturaCategoryUser', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateGet($resultObject);
	}

	/**
	 * Validates testGet results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaCategoryUser $resultObject
	 */
	protected function validateGet(KalturaCategoryUser $resultObject){}

	/**
	 * Tests categoryUser->update action
	 * @param int $categoryId 
	 * @param string $userId 
	 * @param KalturaCategoryUser $categoryUser 
	 * @param bool $override - to override manual changes
	 * @param KalturaCategoryUser $reference
	 * @depends testAdd with data set #1
	 * @dataProvider provideData
	 */
	public function testUpdate($categoryId, $userId, KalturaCategoryUser $categoryUser, $override, KalturaCategoryUser $reference)
	{
		$resultObject = $this->client->categoryUser->update($categoryId, $userId, $categoryUser, $override);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaCategoryUser', $resultObject);
		else
			$this->assertType('KalturaCategoryUser', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateUpdate($resultObject);
	}

	/**
	 * Validates testUpdate results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaCategoryUser $resultObject
	 */
	protected function validateUpdate(KalturaCategoryUser $resultObject){}

	/**
	 * Tests categoryUser->delete action
	 * @param int $categoryId 
	 * @param string $userId 
	 * @depends testAdd with data set #2
	 * @dataProvider provideData
	 */
	public function testDelete($categoryId, $userId)
	{
		$resultObject = $this->client->categoryUser->delete($categoryId, $userId);
	}

	/**
	 * Tests categoryUser->listAction action
	 * @param KalturaCategoryUserFilter $filter
	 * @param KalturaFilterPager $pager
	 * @param KalturaCategoryUserListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaCategoryUserFilter $filter = null, KalturaFilterPager $pager = null, KalturaCategoryUserListResponse $reference)
	{
		$resultObject = $this->client->categoryUser->listAction($filter, $pager);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaCategoryUserListResponse', $resultObject);
		else
			$this->assertType('KalturaCategoryUserListResponse', $resultObject);
		$this->assertAPIObjects($reference, $resultObject, array('createdAt', 'updatedAt', 'id', 'thumbnailUrl', 'downloadUrl', 'rootEntryId', 'operationAttributes', 'deletedAt', 'statusUpdatedAt', 'widgetHTML', 'totalCount', 'objects', 'cropDimensions', 'dataUrl', 'requiredPermissions', 'confFilePath', 'feedUrl'));
		$this->validateListAction($resultObject);
	}

	/**
	 * Validates testListAction results
	 * Hook to be overriden by the extending class
	 * 
	 * @param KalturaCategoryUserListResponse $resultObject
	 */
	protected function validateListAction(KalturaCategoryUserListResponse $resultObject){}

}
