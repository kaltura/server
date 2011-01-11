<?php

/**
 * conversionProfile service base test case.
 */
abstract class ConversionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests conversionProfile->add action
	 * @param KalturaConversionProfile $conversionProfile 
	 * @param KalturaConversionProfile $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
		$resultObject = $this->client->conversionProfile->add($conversionProfile);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateAdd($conversionProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference)
	{
	}

	/**
	 * Tests conversionProfile->get action
	 * @param KalturaConversionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testGet(KalturaConversionProfile $reference, $id)
	{
		$resultObject = $this->client->conversionProfile->get($id);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateGet($reference);
		return $resultObject->id;
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaConversionProfile $reference, $id)
	{
	}

	/**
	 * Tests conversionProfile->update action
	 * @param KalturaConversionProfile $conversionProfile 
	 * @param KalturaConversionProfile $reference 
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference, $id)
	{
		$resultObject = $this->client->conversionProfile->update($id, $conversionProfile);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		$this->validateUpdate($conversionProfile, $reference);
		return $resultObject->id;
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaConversionProfile $conversionProfile, KalturaConversionProfile $reference, $id)
	{
	}

	/**
	 * Tests conversionProfile->delete action
	 * @param int id - returned from testAdd
	 * @depends testFinished
	 * @dataProvider provideData
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->conversionProfile->delete($id);
		$this->validateDelete();
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
	}

	/**
	 * Tests conversionProfile->list action
	 * @param KalturaConversionProfileFilter $filter 
	 * @param KalturaFilterPager $pager 
	 * @param KalturaConversionProfileListResponse $reference 
	 * @dataProvider provideData
	 */
	public function testList(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaConversionProfileListResponse $reference)
	{
		$resultObject = $this->client->conversionProfile->list($filter, $pager);
		$this->assertType('KalturaConversionProfileListResponse', $resultObject);
		$this->validateList($filter, $pager, $reference);
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null, KalturaConversionProfileListResponse $reference)
	{
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
