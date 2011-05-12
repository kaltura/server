<?php

/**
 * dropFolderFile service base test case.
 */
abstract class DropFolderFileServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests dropFolderFile->add action
	 * @param KalturaDropFolderFile $dropFolderFile 
	 * @param KalturaDropFolderFile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
		$resultObject = $this->client->dropFolderFile->add($dropFolderFile);
		$this->assertType('KalturaDropFolderFile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($dropFolderFile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
	}

	/**
	 * Tests dropFolderFile->get action
	 * @param int $dropFolderFileId 
	 * @param KalturaDropFolderFile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testGet($dropFolderFileId, KalturaDropFolderFile $reference)
	{
		$resultObject = $this->client->dropFolderFile->get($dropFolderFileId);
		$this->assertType('KalturaDropFolderFile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($dropFolderFileId, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($dropFolderFileId, KalturaDropFolderFile $reference)
	{
	}

	/**
	 * Tests dropFolderFile->update action
	 * @param int $dropFolderFileId 
	 * @param KalturaDropFolderFile $dropFolderFile Id
	 * @param KalturaDropFolderFile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testUpdate($dropFolderFileId, KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
		$resultObject = $this->client->dropFolderFile->update($dropFolderFileId, $dropFolderFile);
		$this->assertType('KalturaDropFolderFile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($dropFolderFileId, $dropFolderFile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($dropFolderFileId, KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
	}

	/**
	 * Tests dropFolderFile->delete action
	 * @param int $dropFolderFileId 
	 * @dataProvider provideData
	 */
	public function testDelete($dropFolderFileId)
	{
		$resultObject = $this->client->dropFolderFile->delete($dropFolderFileId);
		$this->validateDelete($dropFolderFileId);
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($dropFolderFileId)
	{
	}

	/**
	 * Tests dropFolderFile->listAction action
	 * @param KalturaDropFolderFileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaDropFolderFileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderFileListResponse $reference)
	{
		$resultObject = $this->client->dropFolderFile->listAction($filter, $pager);
		$this->assertType('KalturaDropFolderFileListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderFileListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
