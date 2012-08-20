<?php

require_once(dirname(__FILE__) . '/../../../bootstrap.php');
require_once(dirname(__FILE__) . '/DocumentsServiceTestBase.php');

/**
 * documents service test case.
 */
class DocumentsServiceTest extends DocumentsServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
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

}

