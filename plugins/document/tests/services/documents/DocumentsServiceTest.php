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
	 * @dataProvider provideData
	 */
	public function testAddFromUploadedFile(KalturaDocumentEntry $documentEntry, $uploadTokenId)
	{
		$resultObject = $this->client->documents->addFromUploadedFile($documentEntry, $uploadTokenId);
		$this->assertType('KalturaDocumentEntry', $resultObject);
	}

	/**
	 * Tests documents->addFromEntry action
	 * @param string $sourceEntryId
	 * @param KalturaDocumentEntry $documentEntry
	 * @param int $sourceFlavorParamsId
	 * @dataProvider provideData
	 */
	public function testAddFromEntry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = null)
	{
		$resultObject = $this->client->documents->addFromEntry($sourceEntryId, $documentEntry, $sourceFlavorParamsId);
		$this->assertType('KalturaDocumentEntry', $resultObject);
	}

	/**
	 * Tests documents->addFromFlavorAsset action
	 * @param string $sourceFlavorAssetId
	 * @param KalturaDocumentEntry $documentEntry
	 * @dataProvider provideData
	 */
	public function testAddFromFlavorAsset($sourceFlavorAssetId, KalturaDocumentEntry $documentEntry = null)
	{
		$resultObject = $this->client->documents->addFromFlavorAsset($sourceFlavorAssetId, $documentEntry);
		$this->assertType('KalturaDocumentEntry', $resultObject);
	}

	/**
	 * Tests documents->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null)
	{
		$resultObject = $this->client->documents->convert($entryId, $conversionProfileId, $dynamicConversionAttributes);
		$this->assertType('int', $resultObject);
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
	 * Tests documents->upload action
	 * @param file $fileData
	 * @dataProvider provideData
	 */
	public function testUpload(file $fileData)
	{
		$resultObject = $this->client->documents->upload($fileData);
		$this->assertType('string', $resultObject);
	}

	/**
	 * Tests documents->convertPptToSwf action
	 * @param string $entryId
	 * @dataProvider provideData
	 */
	public function testConvertPptToSwf($entryId)
	{
		$resultObject = $this->client->documents->convertPptToSwf($entryId);
		$this->assertType('string', $resultObject);
	}

}
