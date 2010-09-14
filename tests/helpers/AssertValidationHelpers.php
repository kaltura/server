<?php
class AssertValidationHelpers
{
	static function assertNotUpdatable(KalturaAPIException $ex, $fieldName)
	{
		$dummyEx = new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $fieldName);
		PHPUnit_Framework_Assert::assertEquals($ex->getMessage(), $dummyEx->getMessage());
	}
}