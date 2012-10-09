<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface BatchJobUrgencyType extends BaseEnum
{
	// TODO (batch) in the future we would like to have these enum values configurable
	const REQUIRED_REGULAR_UPLOAD 	= 1;
	const REQUIRED_BULK_UPLOAD 		= 2;
	const OPTIONAL_REGULAR_UPLOAD	= 3;
	const OPTIONAL_BULK_UPLOAD		= 4;
	const DEFAULT_URGENCY			= 5;		
}
