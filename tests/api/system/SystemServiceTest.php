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
	 * @dataProvider provideData
	 */
	public function testPing()
	{
		$resultObject = $this->client->system->ping();
		$this->assertType('bool', $resultObject);
	}

}
