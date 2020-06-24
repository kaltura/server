<?php
/**
 * @package plugins.thumbnail
 * @subpackage api.errors
 */
class KalturaThumbnailErrors extends KalturaErrors
{
	const FAILED_TO_PARSE_ACTION = "FAILED_TO_PARSE_ACTION;actionString;Failed to parse action \"@actionString@\"";
	const FAILED_TO_PARSE_SOURCE = "FAILED_TO_PARSE_SOURCE;sourceString;Failed to parse source \"@sourceString@\"";
	const MISSING_SOURCE_ACTIONS_FOR_TYPE = "MISSING_SOURCE_ACTIONS_FOR_TYPE;entryType;Missing source actions for type \"@entryType@\"";
	const EMPTY_IMAGE_TRANSFORMATION = 'EMPTY_IMAGE_TRANSFORMATION;;No steps in the transformation';
	const FIRST_STEP_CANT_USE_COMP_ACTION = 'FIRST_STEP_CANT_USE_COMP_ACTION;;The first step in the transformation cant use composite action';
	const MISSING_COMPOSITE_ACTION = 'MISSING_COMPOSITE_ACTION;;Missing composite action for multiply steps transformation';
	const TRANSFORMATION_RUNTIME_ERROR = 'TRANSFORMATION_RUNTIME_ERROR;;There was an error running the image transformation';
	const BAD_QUERY = "BAD_QUERY;errorString;Bad query \"@errorString@\"";
	const ACTION_FAILED = "ACTION_FAILED;errorString;Action failed \"@errorString@\"";
	const NOT_ALLOWED_PARAMETER = 'NOT_ALLOWED_PARAMETER;;The provided parameter is not allowed';
	const MUST_HAVE_VIDEO_SOURCE = 'MUST_HAVE_VIDEO_SOURCE;;The following transformation must have video source';
	const MISSING_S3_CONFIGURATION = 'MISSING_S3_CONFIGURATION;;Missing S3 configuration';
	const CACHE_ERROR = 'CACHE_ERROR;;Cache error';
}