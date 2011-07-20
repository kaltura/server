<?php
class MetadataErrors extends KalturaErrors
{
	const INVALID_METADATA_PROFILE = "INVALID_METADATA_PROFILE,invalid metadata profile [%s]";
	
	const INVALID_METADATA_OBJECT = "INVALID_METADATA_OBJECT,invalid metadata object [%s]";
	
	const INVALID_METADATA_DATA = "INVALID_METADATA_DATA,invalid metadata data: %s";
	
	const METADATA_FILE_NOT_FOUND = "METADATA_FILE_NOT_FOUND,Metadata file not found [%s]";
	
	const METADATA_TRANSFORMING = "METADATA_TRANSFORMING,Metadata profile is currently transforming";
	
	const METADATA_UNABLE_TO_TRANSFORM = "METADATA_UNABLE_TO_TRANSFORM,Unable to transform metadata [%s]";
	
	const METADATA_ALREADY_EXISTS = "METADATA_ALREADY_EXISTS,Metadata already exists id [%s]";
	
	const EXCEEDED_LIMIT_SEARCHABLE_DATES = "EXCEEDED_LIMIT_SEARCHABLE_DATES,exceeded number of searchable dates fields";
	
	const EXCEEDED_LIMIT_SEARCHABLE_INTS = "EXCEEDED_LIMIT_SEARCHABLE_INTS,exceeded number of searchable int fields";
}