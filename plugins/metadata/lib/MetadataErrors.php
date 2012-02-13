<?php
/**
 * @package plugins.metadata
 * @subpackage errors
 */
class MetadataErrors extends KalturaErrors
{
	const INVALID_METADATA_PROFILE = "INVALID_METADATA_PROFILE,invalid metadata profile [%s]";
	
	const INVALID_METADATA_OBJECT = "INVALID_METADATA_OBJECT,invalid metadata object [%s]";
	
	const INVALID_METADATA_VERSION = "INVALID_METADATA_VERSION,invalid metadata version [%s]";
	
	const INVALID_METADATA_DATA = "INVALID_METADATA_DATA,invalid metadata data: %s";
	
	const METADATA_FILE_NOT_FOUND = "METADATA_FILE_NOT_FOUND,Metadata file not found [%s]";
	
	const METADATA_TRANSFORMING = "METADATA_TRANSFORMING,Metadata profile is currently transforming";
	
	const METADATA_UNABLE_TO_TRANSFORM = "METADATA_UNABLE_TO_TRANSFORM,Unable to transform metadata [%s]";
	
	const METADATA_ALREADY_EXISTS = "METADATA_ALREADY_EXISTS,Metadata already exists id [%s]";
	
	const EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT = "EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT,exceeded number of account searchable int/date fields, allowed number is [%d]";
	
	const XSLT_VALIDATION_ERROR = "XSLT_VALIDATION_ERROR,XSLT validation error [%s]";
}