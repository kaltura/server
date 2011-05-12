<?php

require_once(dirname(__FILE__) . '/../../../../../tests/base/bootstrap.php');
require_once(dirname(__FILE__) . '/AnnotationServiceBaseTest.php');

/**
 * annotation service test case.
 */
class AnnotationServiceTest extends AnnotationServiceBaseTest
{
	/**
	 * Validates testList results
	 */
	protected function validateList(KalturaAnnotationFilter $filter = null, KalturaFilterPager $pager = null, KalturaAnnotationListResponse $reference)
	{
		parent::validateList($filter, $pager, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testAdd results
	 */
	protected function validateAdd(KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
		parent::validateAdd($annotation, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testGet results
	 */
	protected function validateGet($id, KalturaAnnotation $reference)
	{
		parent::validateGet($id, $reference);
		// TODO - add your own validations here
	}

	/**
	 * Validates testDelete results
	 */
	protected function validateDelete($id)
	{
		parent::validateDelete($id);
		// TODO - add your own validations here
	}

	/**
	 * Validates testUpdate results
	 */
	protected function validateUpdate($id, KalturaAnnotation $annotation, KalturaAnnotation $reference)
	{
		parent::validateUpdate($id, $annotation, $reference);
		// TODO - add your own validations here
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
