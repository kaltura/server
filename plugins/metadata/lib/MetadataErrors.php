<?php
/**
 * @package plugins.metadata
 * @subpackage errors
 */
class MetadataErrors extends KalturaErrors
{
	const METADATA_PROFILE_NOT_FOUND = "METADATA_PROFILE_NOT_FOUND;ID;metadata profile not found, id [@ID@]";
	
	const INVALID_METADATA_PROFILE = "INVALID_METADATA_PROFILE;PROFILE_ID;invalid metadata profile [@PROFILE_ID@]";
	
	const INVALID_METADATA_PROFILE_SCHEMA = "INVALID_METADATA_PROFILE_SCHEMA;ERR_MSG;invalid metadata profile schema: @ERR_MSG@";
	
	const INVALID_METADATA_PROFILE_TYPE = "INVALID_METADATA_PROFILE_TYPE;TYPE;invalid metadata profile type: @TYPE@";
	
	const INVALID_METADATA_OBJECT = "INVALID_METADATA_OBJECT;OBJECT;invalid metadata object [@OBJECT@]";
	
	const INVALID_METADATA_VERSION = "INVALID_METADATA_VERSION;VERSION;invalid metadata version [@VERSION@]";
	
	const INVALID_METADATA_DATA = "INVALID_METADATA_DATA;ERR_MSG;invalid metadata data: @ERR_MSG@";
	
	const METADATA_NOT_FOUND = "METADATA_NOT_FOUND;ID;metadata not found, id [@ID@]";
	
	const METADATA_FILE_NOT_FOUND = "METADATA_FILE_NOT_FOUND;FILE_NAME;Metadata file not found [@FILE_NAME@]";
	
	const EMPTY_VIEWS_DATA_PROVIDED = "EMPTY_VIEWS_DATA_PROVIDED;FILE_NAME;empty views data file [@FILE_NAME@] provided";
	
	const EMPTY_XSLT_DATA_PROVIDED = "EMPTY_XSLT_DATA_PROVIDED;FILE_NAME;empty xslt data file [@FILE_NAME@] provided";
	
	const METADATA_TRANSFORMING = "METADATA_TRANSFORMING;;Metadata profile is currently transforming";
	
	const METADATA_UNABLE_TO_TRANSFORM = "METADATA_UNABLE_TO_TRANSFORM;ERR_MSG;Unable to transform metadata [@ERR_MSG@]";
	
	const METADATA_ALREADY_EXISTS = "METADATA_ALREADY_EXISTS;ID;Metadata already exists id [@ID@]";
	
	const EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT = "EXCEEDED_ADDITIONAL_SEARCHABLE_FIELDS_LIMIT;NUM;exceeded number of account searchable int/date fields, allowed number is [@NUM@]";
	
	const XSLT_VALIDATION_ERROR = "XSLT_VALIDATION_ERROR;ERR_MSG;XSLT validation error [@ERR_MSG@]";
	
	const MUST_FILTER_ON_OBJECT_ID = "MUST_FILTER_ON_OBJECT_ID;;Must filter on object id";

	const MUST_FILTER_ON_OBJECT_TYPE = "MUST_FILTER_ON_OBJECT_TYPE;;Must filter on object type";
	
	const INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE = "INCOMPATIBLE_METADATA_PROFILE_OBJECT_TYPE;OBJ_TYPE,META_OBJ_TYPE;Metadata Profile object type is @OBJ_TYPE@, metadata object type is @META_OBJ_TYPE@";
	
	const METADATA_PROFILE_FILE_NOT_FOUND = "METADATA_PROFILE_FILE_NOT_FOUND;FILE_NAME;Metadata profile file not found [@FILE_NAME@]";

	const METADATA_PROFILE_REFERENCE_EXISTS = "METADATA_PROFILE_REFERENCE_EXISTS;ID,FIELD;Metadata profile reference exists in profile [@ID@] on field [@FIELD@]";

	const METADATA_NO_PERMISSION_ON_ENTRY = "METADATA_NO_PERMISSION_ON_ENTRY;ID;No permissions to add metadata for entry [@ID@]";

	const METADATA_PROFILE_NOT_SPECIFIED = "METADATA_PROFILE_NOT_SPECIFIED;;Metadata Profile need to be specified";
}