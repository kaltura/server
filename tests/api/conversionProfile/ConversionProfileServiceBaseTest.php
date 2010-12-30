<?php

/**
 * conversionProfile service base test case.
 */
abstract class ConversionProfileServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests conversionProfile->add action
	 * @param KalturaConversionProfile $conversionProfile
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaConversionProfile $conversionProfile)
	{
		$resultObject = $this->client->conversionProfile->add($conversionProfile);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests conversionProfile->get action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->conversionProfile->get($id);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Tests conversionProfile->update action
	 * @param KalturaConversionProfile $conversionProfile
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testAdd with data set #0
	 * @dataProvider provideData
	 */
	public function testUpdate(KalturaConversionProfile $conversionProfile, $id)
	{
		$resultObject = $this->client->conversionProfile->update($id, $conversionProfile);
		$this->assertType('KalturaConversionProfile', $resultObject);
		$this->assertNotNull($resultObject->id);
		return $resultObject->id;
	}

	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

	/**
	 * Tests conversionProfile->delete action
	 * @param int id - returned from testAdd
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->conversionProfile->delete($id);
	}

	/**
	 * Tests conversionProfile->list action
	 * @param KalturaConversionProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @dataProvider provideData
	 */
	public function testList(KalturaConversionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$resultObject = $this->client->conversionProfile->listAction($filter, $pager);
		$this->assertType('KalturaConversionProfileListResponse', $resultObject);
		$this->assertNotEquals($resultObject->totalCount, 0);
	}

}
