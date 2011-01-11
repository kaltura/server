<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/SystemServiceBaseTest.php');

/**
 * system service test case.
 */
class SystemServiceTest extends SystemServiceBaseTest
{
	/**
	 * Tests system->ping action
	 * @param bool $reference
	 * @dataProvider provideData
	 */
	public function testPing($reference)
	{
		$resultObject = $this->client->system->ping($reference);
		$this->assertType('bool', $resultObject);
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
