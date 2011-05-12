<?php

/**
 * dropFolder service base test case.
 */
abstract class DropFolderServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests dropFolder->add action
	 * @param KalturaDropFolder $dropFolder 
	 * @param KalturaDropFolder $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
		$resultObject = $this->client->dropFolder->add($dropFolder);
		$this->assertType('KalturaDropFolder', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($dropFolder, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
	}

	/**
	 * Tests dropFolder->get action
	 * @param int $dropFolderId 
	 * @param KalturaDropFolder $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($dropFolderId, KalturaDropFolder $reference)
	{
		$resultObject = $this->client->dropFolder->get($dropFolderId);
		$this->assertType('KalturaDropFolder', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($dropFolderId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($dropFolderId, KalturaDropFolder $reference)
	{
	}

	/**
	 * Tests dropFolder->update action
	 * @param int $dropFolderId 
	 * @param KalturaDropFolder $dropFolder Id
	 * @param KalturaDropFolder $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($dropFolderId, KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
		$resultObject = $this->client->dropFolder->update($dropFolderId, $dropFolder);
		$this->assertType('KalturaDropFolder', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($dropFolderId, $dropFolder, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($dropFolderId, KalturaDropFolder $dropFolder, KalturaDropFolder $reference)
	{
	}

	/**
	 * Tests dropFolder->delete action
	 * @param int $dropFolderId 
	 * @dataProvider provideData
	 */
	public function testDelete($dropFolderId)
	{
		$resultObject = $this->client->dropFolder->delete($dropFolderId);
		$this->validateDelete($dropFolderId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($dropFolderId)
	{
	}

	/**
	 * Tests dropFolder->listAction action
	 * @param KalturaDropFolderFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDropFolderListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderListResponse $reference)
	{
		$resultObject = $this->client->dropFolder->listAction($filter, $pager);
		$this->assertType('KalturaDropFolderListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDropFolderFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
