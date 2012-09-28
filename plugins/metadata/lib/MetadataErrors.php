<?php
/**
 * @package plugins.metadata
 * @subpackage errors
 */
class MetadataErrors extends KalturaErrors
{
	const METADATA_PROFILE_NOT_FOUND = "METADATA_PROFILE_NOT_FOUND,metadata profile not found, id [%s]";
	
	const INVALID_METADATA_PROFILE = "INVALID_METADATA_PROFILE,invalid metadata profile [%s]";
	
	const INVALID_METADATA_PROFILE_SCHEMA = "INVALID_METADATA_PROFILE_SCHEMA,invalid metadata profile schema: %s";
	
	const INVALID_METADATA_PROFILE_TYPE = "INVALID_METADATA_PROFILE_TYPE,invalid metadata profile type: %s";
	
	const INVALID_METADATA_OBJECT = "INVALID_METADATA_OBJECT,invalid metadata object [%s]";
	
	const INVALID_METADATA_VERSION = "INVALID_METADATA_VERSION,invalid metadata version [%s]";
	
	const INVALID_METADATA_DATA = "INVALID_METADATA_DATA,invalid metadata data: %s";
	
	const METADATA_NOT_FOUND = "METADATA_NOT_FOUND,metadata not found, id [%s]";
	
	const METADATA_FILE_NOT_FOUND = "METADATA_FILE_NOT_FOUND,Metadata file not found [%s]";
	
	const METADATA_TRANSFORMING = "METADATA_TRANSFORMING,Metadata profile is currently transforming";
	
	const METADATA_UNABLE_TO_TRANSFORM = "METADATA_UNABLE_TO_TRANSFORM,Unable to transform metadata [%s]";
	
	const METADATA_ALREADY_EXISTS = "METADATA_ALREADY_EXISTS,Metadata already exists id [%s]";
	
	const EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT = "EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT,exceeded number of account searchable int/date fields, allowed number is [%d]";
	
	const XSLT_VALIDATION_ERROR = "XSLT_VALIDATION_ERROR,XSLT validation error [%s]";
	
	const MUST_FILTER_ON_OBJECT_ID = "MUST_FILTER_ON_OBJECT_ID,Must filter on obejct id";
	
	const INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE = "INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE,Metadata Profile object type is %s, metadata object type is %s";
}