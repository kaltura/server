<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/DocumentsServiceBaseTest.php');

/**
 * documents service test case.
 */
class DocumentsServiceTest extends DocumentsServiceBaseTest
{
	/**
	 * Tests documents->addFromUploadedFile action
	 * @param KalturaDocumentEntry $documentEntry
	 * @param string $uploadTokenId
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaDocumentEntry $documentEntry, $uploadTokenId, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->addFromUploadedFile($documentEntry, $uploadTokenId, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests documents->addFromEntry action
	 * @param string $sourceEntryId
	 * @param KalturaDocumentEntry $documentEntry
	 * @param int $sourceFlavorParamsId
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = null, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->addFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests documents->addFromFlavorAsset action
	 * @param string $sourceFlavorAssetId
	 * @param KalturaDocumentEntry $documentEntry
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddFromFlavorAsset($sourceFlavorAssetId, KalturaDocumentEntry $documentEntry = null, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->documents->addFromFlavorAsset($sourceFlavorAssetId, $documentEntry, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests documents->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null, $reference)
	{
		$resultObject = $this->client->documents->convert($entryId, $conversionProfileId, $dynamicConversionAttributes, $reference);
		$this->assertType('int', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($entryId, $version = -1, KalturaDocumentEntry $reference)
	{
		parent::validateGet($entryId, $version, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($entryId, KalturaDocumentEntry $documentEntry, KalturaDocumentEntry $reference)
	{
		parent::validateUpdate($entryId, $documentEntry, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($entryId)
	{
		parent::validateDelete($entryId);
		// TODO - add your own validations here
	}

	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests documents->upload action
	 * @param file $fileData
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData, $reference)
	{
		$resultObject = $this->client->documents->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests documents->convertPptToSwf action
	 * @param string $entryId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testConvertPptToSwf($entryId, $reference)
	{
		$resultObject = $this->client->documents->convertPptToSwf($entryId, $reference);
		$this->assertType('string', $resultObject);
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
