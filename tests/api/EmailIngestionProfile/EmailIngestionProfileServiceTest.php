<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/EmailIngestionProfileServiceBaseTest.php');

/**
 * EmailIngestionProfile service test case.
 */
class EmailIngestionProfileServiceTest extends EmailIngestionProfileServiceBaseTest
{
	/**
	 * Tests EmailIngestionProfile->getByEmailAddress action
	 * @param string $emailAddress
	 * @dataProvider provideData
	 */
	public function testGetByEmailAddress($emailAddress)
	{
		$resultObject = $this->client->EmailIngestionProfile->getByEmailAddress($emailAddress);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
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
	 * Tests EmailIngestionProfile->addMediaEntry action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $uploadTokenId
	 * @param int $emailProfId
	 * @param string $fromAddress
	 * @param string $emailMsgId
	 * @dataProvider provideData
	 */
	public function testAddMediaEntry(KalturaMediaEntry $mediaEntry, $uploadTokenId, $emailProfId, $fromAddress, $emailMsgId)
	{
		$resultObject = $this->client->EmailIngestionProfile->addMediaEntry($mediaEntry, $uploadTokenId, $emailProfId, $fromAddress, $emailMsgId);
		$this->assertType('KalturaMediaEntry', $resultObject);
	}

}
