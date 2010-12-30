<?php

/**
 * flavorAsset service base test case.
 */
abstract class FlavorAssetServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Tests flavorAsset->get action
	 * @param string $id
	 * @return int
	 * @depends testAdd with data set #0
	 */
	public function testGet($id)
	{
		$resultObject = $this->client->flavorAsset->get($id);
		$this->assertType('KalturaFlavorAsset', $resultObject);
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
	 * Tests flavorAsset->delete action
	 * @param string $id
	 * @return int
	 * @depends testFinished
	 */
	public function testDelete($id)
	{
		$resultObject = $this->client->flavorAsset->delete($id);
	}

}
