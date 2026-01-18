<?php
/**
 * @package plugins.attachment
 * @subpackage lib
 */
abstract class kAttachmentContentManager
{

	public static function getCoreContentManager($type)
	{
		switch($type)
		{
			case AttachmentType::MARKDOWN:
				return mdAttachmentContentManager::get();

			default:
				return KalturaPluginManager::loadObject('kAttachmentContentManager', $type);
		}
	}
}
