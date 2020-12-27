<?php
/**
 * @package plugins.thumbnail
 * @subpackage model.errors
 */

class kThumbnailException extends kCoreException
{
	const FAILED_TO_PARSE_ACTION = 'Failed to parse action';
	const FAILED_TO_PARSE_SOURCE = 'Failed to parse source';
	const MISSING_SOURCE_ACTIONS_FOR_TYPE = 'Missing source actions for type';
	const EMPTY_IMAGE_TRANSFORMATION = 'No steps in the transformation';
	const FIRST_STEP_CANT_USE_COMP_ACTION = 'The first step in the transformation cant use composite action';
	const MISSING_COMPOSITE_ACTION = 'Missing composite action for multiply steps transformation';
	const TRANSFORMATION_RUNTIME_ERROR = 'There was an error running the image transformation';
	const BAD_QUERY = 'Bad query';
	const ACTION_FAILED = 'Action failed';
	const NOT_ALLOWED_PARAMETER = 'The provided parameter is not allowed';
	const MUST_HAVE_VIDEO_SOURCE = 'The following transformation must have video source';
	const MISSING_S3_CONFIGURATION = 'Missing S3 configuration';
	const CACHE_ERROR = 'Cache error';
	const ENTRY_NOT_FOUND = 'Entry not found';
	const PLAYLIST_ENTRY_NOT_FOUND = 'playlist entry not found';
}