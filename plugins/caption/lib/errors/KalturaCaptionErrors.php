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
	const CAPTION_ASSET_IS_DEFAULT = "CAPTION_ASSET_IS_DEFAULT,Caption asset \"%s\" is default";
	const CAPTION_ASSET_DOWNLOAD_FAILED = "CAPTION_ASSET_DOWNLOAD_FAILED,Caption asset download from URL \"%s\" failed";
}