<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
abstract class BatchJobUrgencyType implements BaseEnum
{
	const REQUIRED_REGULAR_UPLOAD 	= 1;
	const REQUIRED_BULK_UPLOAD 		= 2;
	const OPTIONAL_REGULAR_UPLOAD	= 3;
	const OPTIONAL_BULK_UPLOAD		= 4;
	const DEFAULT_URGENCY			= 5;		

	public static function getDefaultUrgency(BatchJob $batchJob) {
		return ($batchJob->getBulkJobId() === NULL) ? BatchJobUrgencyType::REQUIRED_REGULAR_UPLOAD : BatchJobUrgencyType::REQUIRED_BULK_UPLOAD;
	}
	
	public static function isBulkUpload($urgency) {
		return (($urgency % 2) == 0);
	}
}
