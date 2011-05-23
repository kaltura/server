<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * document service test case.
 */
class DocumentServiceTest extends DocumentServiceBaseTest
{
	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaDocumentEntry $entry, KalturaResource $resource = null, KalturaMediaEntry $reference)
	{
		parent::validateAdd($entry, $resource, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests document->addfromuploadedfile action
	 * @param KalturaDocumentEntry $documentEntry
	 * @param string $uploadTokenId
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromuploadedfile(KalturaDocumentEntry $documentEntry, $uploadTokenId, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->document->addfromuploadedfile($documentEntry, $uploadTokenId, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests document->addfromentry action
	 * @param string $sourceEntryId
	 * @param KalturaDocumentEntry $documentEntry
	 * @param int $sourceFlavorParamsId
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromentry($sourceEntryId, KalturaDocumentEntry $documentEntry = null, $sourceFlavorParamsId = null, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->document->addfromentry($sourceEntryId, $documentEntry, $sourceFlavorParamsId, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests document->addfromflavorasset action
	 * @param string $sourceFlavorAssetId
	 * @param KalturaDocumentEntry $documentEntry
	 * @param KalturaDocumentEntry $reference
	 * @dataProvider provideData
	 */
	public function testAddfromflavorasset($sourceFlavorAssetId, KalturaDocumentEntry $documentEntry = null, KalturaDocumentEntry $reference)
	{
		$resultObject = $this->client->document->addfromflavorasset($sourceFlavorAssetId, $documentEntry, $reference);
		$this->assertType('KalturaDocumentEntry', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests document->convert action
	 * @param string $entryId
	 * @param int $conversionProfileId
	 * @param KalturaConversionAttributeArray $dynamicConversionAttributes
	 * @param int $reference
	 * @dataProvider provideData
	 */
	public function testConvert($entryId, $conversionProfileId = null, KalturaConversionAttributeArray $dynamicConversionAttributes = null, $reference)
	{
		$resultObject = $this->client->document->convert($entryId, $conversionProfileId, $dynamicConversionAttributes, $reference);
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
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaDocumentEntryFilter $filter = null, KalturaFilterPager $pager = null, KalturaDocumentListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Tests document->upload action
	 * @param file $fileData
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testUpload($fileData, $reference)
	{
		$resultObject = $this->client->document->upload($fileData, $reference);
		$this->assertType('string', $resultObject);
		// TODO - add here your own validations
	}

	/**
	 * Tests document->convertppttoswf action
	 * @param string $entryId
	 * @param string $reference
	 * @dataProvider provideData
	 */
	public function testConvertppttoswf($entryId, $reference)
	{
		$resultObject = $this->client->document->convertppttoswf($entryId, $reference);
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
