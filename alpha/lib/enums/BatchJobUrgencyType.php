<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BatchJobUrgencyType extends BaseEnum
{
	// TODO (batch) in the future we would like to have these enum values configurable
	const TOP_URGENCY			    = 0;
	const REQUIRED_REGULAR_UPLOAD 	= 1;
	const REQUIRED_BULK_UPLOAD 		= 2;
	const OPTIONAL_REGULAR_UPLOAD	= 3;
	const OPTIONAL_BULK_UPLOAD		= 4;
	const DEFAULT_URGENCY			= 5;	
	const MIGRATION_URGENCY			= 10;

	
	// Urgencies for file sync import Jobs
   	const FILE_SYNC_SOURCE = 1;
   	const FILE_SYNC_NOT_SOURCE = 2;
}
