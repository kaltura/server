<?php
/**
 * @package plugins.attachment
 * @subpackage lib.enum
 */
class AttachmentAssetType implements IKalturaPluginEnum, assetType
{
	const ATTACHMENT = 'Attachment';
	
	public static function getAdditionalValues()
	{
		return array(
			'ATTACHMENT' => self::ATTACHMENT,
		);
	}
}
