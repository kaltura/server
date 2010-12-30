<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/ThumbParamsServiceBaseTest.php');

/**
 * thumbParams service test case.
 */
class ThumbParamsServiceTest extends ThumbParamsServiceBaseTest
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
	 * Tests thumbParams->getByConversionProfileId action
	 * @param int $conversionProfileId
	 * @dataProvider provideData
	 */
	public function testGetByConversionProfileId($conversionProfileId)
	{
		$resultObject = $this->client->thumbParams->getByConversionProfileId($conversionProfileId);
		$this->assertType('KalturaThumbParamsArray', $resultObject);
	}

}
