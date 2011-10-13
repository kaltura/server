<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');
require_once(dirname(__FILE__) . '/MediaInfoServiceTestBase.php');

/**
 * mediaInfo service test case.
 */
class MediaInfoServiceTest extends MediaInfoServiceTestBase
{
	/**
	 * Set up the test initial data
	 */
	protected function setUp()
	{
		parent::setUp();
	}

	/**
	 * Validates testListAction results
	 */
	protected function validateListAction(KalturaMediaInfoFilter $filter = null, KalturaFilterPager $pager = null, KalturaMediaInfoListResponse $reference)
	{
		parent::validateListAction($filter, $pager, $reference);
		// TODO - add your own validations here
	}

}

