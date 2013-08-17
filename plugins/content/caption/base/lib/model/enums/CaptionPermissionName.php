<?php
/**
 * @package plugins.caption
 * @subpackage model.enum
 */ 
class CaptionPermissionName implements IKalturaPluginEnum, PermissionName
{
	const IMPORT_REMOTE_CAPTION_FOR_INDEXING = 'IMPORT_REMOTE_CAPTION_FOR_INDEXING';
	const FEATURE_GENERATE_WEBVTT_CAPTIONS = 'FEATURE_GENERATE_WEBVTT_CAPTIONS';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'IMPORT_REMOTE_CAPTION_FOR_INDEXING' => self::IMPORT_REMOTE_CAPTION_FOR_INDEXING,
			'FEATURE_GENERATE_WEBVTT_CAPTIONS' => self::FEATURE_GENERATE_WEBVTT_CAPTIONS,
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
