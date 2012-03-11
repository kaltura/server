<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
class MetadataConditionType implements IKalturaPluginEnum, ConditionType
{
	const METADATA_FIELD_MATCH = 'FieldMatch';
	const METADATA_FIELD_COMPARE = 'FieldCompare';
	
	public static function getAdditionalValues()
	{
		return array(
			'METADATA_FIELD_MATCH' => self::METADATA_FIELD_MATCH,
			'METADATA_FIELD_COMPARE' => self::METADATA_FIELD_COMPARE,
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
