<?php

/**
 * metadataProfile service base test case.
 */
abstract class MetadataProfileServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Tests metadataProfile->listAction action
	 * @param KalturaMetadataProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaMetadataProfileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testListAction(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataProfileListResponse $reference)
	{
		$resultObject = $this->client->metadataProfile->listAction($filter, $pager);
		$this->assertType('KalturaMetadataProfileListResponse', $resultObject);
		$this->validateListAction($filter, $pager, $reference);
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMetadataProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaMetadataProfileListResponse $reference)
	{
	}

	/**
	 * Tests metadataProfile->add action
	 * @param KalturaMetadataProfile $metadataProfile 
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @param KalturaMetadataProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null, KalturaMetadataProfile $reference)
	{
		$resultObject = $this->client->metadataProfile->add($metadataProfile, $xsdData, $viewsData);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($metadataProfile, $xsdData, $viewsData, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaMetadataProfile $metadataProfile, $xsdData, $viewsData = null, KalturaMetadataProfile $reference)
	{
	}

	/**
	 * Tests metadataProfile->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->metadataProfile->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests metadataProfile->get action
	 * @param KalturaMetadataProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaMetadataProfile $reference, $id)
	{
		$resultObject = $this->client->metadataProfile->get($id);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaMetadataProfile $reference, $id)
	{
	}

	/**
	 * Tests metadataProfile->update action
	 * @param KalturaMetadataProfile $metadataProfile 
	 * @param string $xsdData XSD metadata definition
	 * @param string $viewsData UI views definition
	 * @param KalturaMetadataProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null, KalturaMetadataProfile $reference, $id)
	{
		$resultObject = $this->client->metadataProfile->update($id, $metadataProfile, $xsdData, $viewsData);
		$this->assertType('KalturaMetadataProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($metadataProfile, $xsdData, $viewsData, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaMetadataProfile $metadataProfile, $xsdData = null, $viewsData = null, KalturaMetadataProfile $reference, $id)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
