<?php

require_once(dirname(__FILE__) . '/../../base/bootstrap.php');
require_once(dirname(__FILE__) . '/EmailIngestionProfileServiceBaseTest.php');

/**
 * EmailIngestionProfile service test case.
 */
class EmailIngestionProfileServiceTest extends EmailIngestionProfileServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference)
	{
		parent::validateAdd($EmailIP, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests EmailIngestionProfile->getByEmailAddress action
	 * @param string $emailAddress
	 * @param KalturaEmailIngestionProfile $reference
	 * @dataProvider provideData
	 */
	public function testGetByEmailAddress($emailAddress, KalturaEmailIngestionProfile $reference)
	{
		$resultObject = $this->client->EmailIngestionProfile->getByEmailAddress($emailAddress, $reference);
		$this->assertType('KalturaEmailIngestionProfile', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet(KalturaEmailIngestionProfile $reference, $id)
	{
		parent::validateGet($reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate(KalturaEmailIngestionProfile $EmailIP, KalturaEmailIngestionProfile $reference, $id)
	{
		parent::validateUpdate($EmailIP, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete();
		// TODO - add your own validations here
	}

	/**
	 * Tests EmailIngestionProfile->addMediaEntry action
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $uploadTokenId
	 * @param int $emailProfId
	 * @param string $fromAddress
	 * @param string $emailMsgId
	 * @param KalturaMediaEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddMediaEntry(KalturaMediaEntry $mediaEntry, $uploadTokenId, $emailProfId, $fromAddress, $emailMsgId, KalturaMediaEntry $reference)
	{
		$resultObject = $this->client->EmailIngestionProfile->addMediaEntry($mediaEntry, $uploadTokenId, $emailProfId, $fromAddress, $emailMsgId, $reference);
		$this->assertType('KalturaMediaEntry', $resultObject);
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
