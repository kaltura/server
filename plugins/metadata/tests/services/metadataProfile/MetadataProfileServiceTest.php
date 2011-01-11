<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataProfileServiceBaseTest.php');

/**
 * metadataProfile service test case.
 */
class MetadataProfileServiceTest extends MetadataProfileServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataProfileListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests metadataProfile->listFields action
	 * @param int $metadataProfileId
	 * @param KalturaMetadataProfileFieldListResponse $reference
	 * @dataProvider provideData
	 */
	public function testListFields($metadataProfileId, KalturaMetadataProfileFieldListResponse $reference)
	{
		$resultObject = $this->client->metadataProfile->listFields($metadataProfileId, $reference);
		$this->assertType('KalturaMetadataProfileFieldListResponse', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null, KalturaMetadataProfile $reference)
	{
		parent::validateAdd($metadataProfile, $xsdData, $viewsData, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests metadataProfile->addFromFile action
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param file $xsdFile
	 * @param file $viewsFile
	 * @param KalturaMetadataProfile $reference
	 * @dataProvider provideData
	 */
	public function testAddFromFile(KalturaMetadataProfile $metadataProfile, file $xsdFile, file $viewsFile = null, KalturaMetadataProfile $reference)
	{
		$resultObject = $this->client->metadataProfile->addFromFile($metadataProfile, $xsdFile, $viewsFile, $reference);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaMetadataProfile $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null, KalturaMetadataProfile $reference, $id)
	{
		parent::validateUpdate($metadataProfile, $xsdData, $viewsData, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests metadataProfile->revert action
	 * @param int $toVersion
	 * @param KalturaMetadataProfile $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testRevert($toVersion, KalturaMetadataProfile $reference, $id)
	{
		$resultObject = $this->client->metadataProfile->revert($id, $toVersion, $reference);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataProfile->updateDefinitionFromFile action
	 * @param file $xsdFile
	 * @param KalturaMetadataProfile $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateDefinitionFromFile(file $xsdFile, KalturaMetadataProfile $reference, $id)
	{
		$resultObject = $this->client->metadataProfile->updateDefinitionFromFile($id, $xsdFile, $reference);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests metadataProfile->updateViewsFromFile action
	 * @param file $viewsFile
	 * @param KalturaMetadataProfile $reference
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateViewsFromFile(file $viewsFile, KalturaMetadataProfile $reference, $id)
	{
		$resultObject = $this->client->metadataProfile->updateViewsFromFile($id, $viewsFile, $reference);
		$this->assertType('KalturaMetadataProfile', $resultObject);
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
