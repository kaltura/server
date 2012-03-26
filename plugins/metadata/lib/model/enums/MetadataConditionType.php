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
		return array(
			MetadataPlugin::getApiValue(self::METADATA_FIELD_COMPARE) => 'Validate that all metadata elements number compared correctly to all listed numeric values.',
			MetadataPlugin::getApiValue(self::METADATA_FIELD_MATCH) => 'Validate that any of metadata elements text matches any of listed textual values.',
		);
	}
}
