<?php
/**
 * @package plugins.caption
 * @subpackage api.errors
 */
class KalturaCaptionErrors extends KalturaErrors
{
	const CAPTION_ASSET_ALREADY_EXISTS = "CAPTION_ASSET_ALREADY_EXISTS;ASSET_ID,PARAM_ID;Caption asset \"@ASSET_ID@\" already exists for params id \"@PARAM_ID@\"";
	const CAPTION_ASSET_PARAMS_ID_NOT_FOUND = "CAPTION_ASSET_PARAMS_ID_NOT_FOUND;PARAM_ID;Caption params id \"@PARAM_ID@\" not found";
	const CAPTION_ASSET_ID_NOT_FOUND = "CAPTION_ASSET_ID_NOT_FOUND;ASSET_ID;Caption asset id \"@ASSET_ID@\" not found";
	const CAPTION_ASSET_IS_NOT_READY = "CAPTION_ASSET_IS_NOT_READY;ASSET_ID;Caption asset \"@ASSET_ID@\" is not ready";
	const CAPTION_ASSET_IS_DEFAULT = "CAPTION_ASSET_IS_DEFAULT;ASSET_ID;Caption asset \"@ASSET_ID@\" is default";
	const CAPTION_ASSET_DOWNLOAD_FAILED = "CAPTION_ASSET_DOWNLOAD_FAILED;URL;Caption asset download from URL \"@URL@\" failed";
	const CAPTION_ASSET_FILE_NOT_FOUND = "CAPTION_ASSET_FILE_NOT_FOUND;ASSET_ID;Caption asset \"@ASSET_ID@\" file not found";
	const CAPTION_ASSET_INVALID_FORMAT = "CAPTION_ASSET_INVALID_FORMAT;ASSET_ID;Unsupported caption asset format for id \"@ASSET_ID@\"";
	const CAPTION_ASSET_PARSING_FAILED = "CAPTION_ASSET_PARSING_FAILED;ASSET_ID;Failed to parse caption asset \"@ASSET_ID@\"";
	const CAPTION_ASSET_UNSUPPORTED_FORMAT = "CAPTION_ASSET_UNSUPPORTED_FORMAT;FORMAT;Unsupported caption asset format \"@FORMAT@\"";
	const CAPTION_ASSET_ENTRY_ID_NOT_FOUND = "CAPTION_ASSET_ENTRY_ID_NOT_FOUND;ENTRY_ID;Entry ID \"@ENTRY_ID@\" which this caption asset is associated with not found";
}