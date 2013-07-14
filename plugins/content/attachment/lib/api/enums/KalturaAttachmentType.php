<?php
/**
 * @package plugins.attachment
 * @subpackage api.enum
 */
class KalturaAttachmentType extends KalturaDynamicEnum implements AttachmentType
{
	public static function getEnumClass()
	{
		return 'AttachmentType';
	}
}