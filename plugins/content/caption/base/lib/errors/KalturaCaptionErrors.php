<?php
/**
 * @package plugins.caption
 * @subpackage api.errors
 */
class KalturaCaptionErrors extends KalturaErrors
{
	const CAPTION_ASSET_ALREADY_EXISTS = "CAPTION_ASSET_ALREADY_EXISTS,Caption asset \"%s\" already exists for params id \"%s\"";
	const CAPTION_ASSET_PARAMS_ID_NOT_FOUND = "CAPTION_ASSET_PARAMS_ID_NOT_FOUND,Caption params id \"%s\" not found";
	const CAPTION_ASSET_ID_NOT_FOUND = "CAPTION_ASSET_ID_NOT_FOUND,Caption asset id \"%s\" not found";
	const CAPTION_ASSET_IS_NOT_READY = "CAPTION_ASSET_IS_NOT_READY,Caption asset \"%s\" is not ready";
	const CAPTION_ASSET_IS_DEFAULT = "CAPTION_ASSET_IS_DEFAULT,Caption asset \"%s\" is default";
	const CAPTION_ASSET_DOWNLOAD_FAILED = "CAPTION_ASSET_DOWNLOAD_FAILED,Caption asset download from URL \"%s\" failed";
	const CAPTION_ASSET_FILE_NOT_FOUND = "CAPTION_ASSET_FILE_NOT_FOUND,Caption asset \"%s\" file not found";
	const CAPTION_ASSET_INVALID_FORMAT = "CAPTION_ASSET_INVALID_FORMAT,Unsupported caption asset format for id \"%s\"";
	const CAPTION_ASSET_PARSING_FAILED = "CAPTION_ASSET_PARSING_FAILED,Failed to parse caption asset \"%s\"";
}