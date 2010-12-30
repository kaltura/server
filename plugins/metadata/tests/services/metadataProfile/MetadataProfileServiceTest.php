<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/MetadataProfileServiceBaseTest.php');

/**
 * metadataProfile service test case.
 */
class MetadataProfileServiceTest extends MetadataProfileServiceBaseTest
{
	/**
	 * Tests metadataProfile->listFields action
	 * @param int $metadataProfileId
	 * @dataProvider provideData
	 */
	public function testListFields($metadataProfileId)
	{
		$resultObject = $this->client->metadataProfile->listFields($metadataProfileId);
		$this->assertType('KalturaMetadataProfileFieldListResponse', $resultObject);
	}

	/**
	 * Tests metadataProfile->addFromFile action
	 * @param KalturaMetadataProfile $metadataProfile
	 * @param file $xsdFile
	 * @param file $viewsFile
	 * @dataProvider provideData
	 */
	public function testAddFromFile(KalturaMetadataProfile $metadataProfile, file $xsdFile, file $viewsFile = null)
	{
		$resultObject = $this->client->metadataProfile->addFromFile($metadataProfile, $xsdFile, $viewsFile);
		$this->assertType('KalturaMetadataProfile', $resultObject);
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 * @depends testFunction - TODO: replace testFunction with last test function that uses that id
	 */
	public function testFinished($id)
	{
		return $id;
	}

	/**
	 * Tests metadataProfile->revert action
	 * @param int $toVersion
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testRevert($toVersion, $id)
	{
		$resultObject = $this->client->metadataProfile->revert($id, $toVersion);
		$this->assertType('KalturaMetadataProfile', $resultObject);
	}

	/**
	 * Tests metadataProfile->updateDefinitionFromFile action
	 * @param file $xsdFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateDefinitionFromFile(file $xsdFile, $id)
	{
		$resultObject = $this->client->metadataProfile->updateDefinitionFromFile($id, $xsdFile);
		$this->assertType('KalturaMetadataProfile', $resultObject);
	}

	/**
	 * Tests metadataProfile->updateViewsFromFile action
	 * @param file $viewsFile
	 * @param int id - returned from testAdd
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdateViewsFromFile(file $viewsFile, $id)
	{
		$resultObject = $this->client->metadataProfile->updateViewsFromFile($id, $viewsFile);
		$this->assertType('KalturaMetadataProfile', $resultObject);
	}

}
