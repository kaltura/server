<?php
/**
 * @package plugins.attachment
 * @subpackage api.errors
 */
class KalturaAttachmentErrors extends KalturaErrors
{
	const ATTACHMENT_ASSET_ID_NOT_FOUND = "ATTACHMENT_ASSET_ID_NOT_FOUND;ASSET_ID;Attachment asset id \"@ASSET_ID@\" not found";
	const ATTACHMENT_ASSET_IS_NOT_READY = "ATTACHMENT_ASSET_IS_NOT_READY;ASSET_ID;Attachment asset \"@ASSET_ID@\" is not ready";
	const ATTACHMENT_ASSET_DOWNLOAD_FAILED = "ATTACHMENT_ASSET_DOWNLOAD_FAILED;URL;Attachment asset download from URL \"@URL@\" failed";
}