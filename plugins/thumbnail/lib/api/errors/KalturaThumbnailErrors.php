<?php
/**
 * @package plugins.thumbnail
 * @subpackage api.errors
 */
class KalturaThumbnailErrors extends KalturaErrors
{
	const MISSING_PARTNER_PARAMETER_IN_URL = "Missing partner parameter in url";
	const FAILED_TO_PARSE_ACTION = "Failed to parse action \"@actionString@\"";
	const FAILED_TO_PARSE_SOURCE = "Failed to parse source \"@sourceString@\"";
	const MISSING_SOURCE_ACTIONS_FOR_TYPE = "Missing source actions for type \"@entryType@\"";
	const EMPTY_IMAGE_TRANSFORMATION = "No steps in the transformation";
	const FIRST_STEP_CANT_USE_COMP_ACTION = "The first step in the transformation cant use composite action";
	const MISSING_COMPOSITE_ACTION = "Missing composite action for multiply steps transformation";
	const BAD_QUERY = "Bad query \"@errorString@\"";
}