<?php
/**
 * @package plugins.caption
 * @subpackage model.enum
 */ 
class CaptionPermissionName implements IKalturaPluginEnum, PermissionName
{
	const IMPORT_REMOTE_CAPTION_FOR_INDEXING = 'IMPORT_REMOTE_CAPTION_FOR_INDEXING';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'IMPORT_REMOTE_CAPTION_FOR_INDEXING' => self::IMPORT_REMOTE_CAPTION_FOR_INDEXING,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
