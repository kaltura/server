<?php
/**
 * @package plugins.attachment
 * @subpackage api.errors
 */
class KalturaAttachmentErrors extends KalturaErrors
{
	const ATTACHMENT_ASSET_ID_NOT_FOUND = "ATTACHMENT_ASSET_ID_NOT_FOUND,Attachment asset id \"%s\" not found";
	const ATTACHMENT_ASSET_DOWNLOAD_FAILED = "ATTACHMENT_ASSET_DOWNLOAD_FAILED,Attachment asset download from URL \"%s\" failed";
}