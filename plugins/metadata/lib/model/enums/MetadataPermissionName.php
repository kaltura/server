<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */ 
class MetadataPermissionName implements IKalturaPluginEnum, PermissionName
{
	const FEATURE_METADATA_NO_VALIDATION = 'FEATURE_METADATA_NO_VALIDATION';
	
	public static function getAdditionalValues()
	{
		return array
		(
			'FEATURE_METADATA_NO_VALIDATION' => self::FEATURE_METADATA_NO_VALIDATION,
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
