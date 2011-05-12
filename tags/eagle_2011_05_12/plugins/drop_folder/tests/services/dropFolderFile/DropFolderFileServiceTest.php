<?php

require_once(dirname(__FILE__) . '/../../../../../../../tests/base/bootstrap.php');

/**
 * dropFolderFile service test case.
 */
class DropFolderFileServiceTest extends DropFolderFileServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
		parent::validateAdd($dropFolderFile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($dropFolderFileId, KalturaDropFolderFile $reference)
	{
		parent::validateGet($dropFolderFileId, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($dropFolderFileId, KalturaDropFolderFile $dropFolderFile, KalturaDropFolderFile $reference)
	{
		parent::validateUpdate($dropFolderFileId, $dropFolderFile, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($dropFolderFileId)
	{
		parent::validateDelete($dropFolderFileId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDropFolderFileFilter $filter = null, KalturaFilterPager $pager = null, KalturaDropFolderFileListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests dropFolderFile->ignore action
	 * @param int $dropFolderFileId
	 * @param KalturaDropFolderFile $reference
	 * @dataProvider provideData
	 */
	public function testIgnore($dropFolderFileId, KalturaDropFolderFile $reference)
	{
		$resultObject = $this->client->dropFolderFile->ignore($dropFolderFileId, $reference);
		$this->assertType('KalturaDropFolderFile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testUpdate - TODO: replace testUpdate with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

}
