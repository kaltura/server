<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/FlavorParamsServiceBaseTest.php');

/**
 * flavorParams service test case.
 */
class FlavorParamsServiceTest extends FlavorParamsServiceBaseTest
{
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
	 * Tests flavorParams->getByConversionProfileId action
	 * @param int $conversionProfileId
	 * @dataProvider provideData
	 */
	public function testGetByConversionProfileId($conversionProfileId)
	{
		$resultObject = $this->client->flavorParams->getByConversionProfileId($conversionProfileId);
		$this->assertType('KalturaFlavorParamsArray', $resultObject);
	}

}
