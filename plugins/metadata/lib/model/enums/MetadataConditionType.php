<?php
/**
 * @package plugins.metadata
 * @subpackage model.enum
 */
class MetadataConditionType implements IKalturaPluginEnum, ConditionType
{
	const METADATA_FIELD_MATCH = 'FieldMatch';
	const METADATA_FIELD_COMPARE = 'FieldCompare';
	const METADATA_FIELD_CHANGED = 'FieldChanged';
	
	public static function getAdditionalValues()
	{
		return array(
			'METADATA_FIELD_MATCH' => self::METADATA_FIELD_MATCH,
			'METADATA_FIELD_COMPARE' => self::METADATA_FIELD_COMPARE,
			'METADATA_FIELD_CHANGED' => self::METADATA_FIELD_CHANGED,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			MetadataPlugin::getApiValue(self::METADATA_FIELD_COMPARE) => 'Validate that all metadata elements number compared correctly to all listed numeric values.',
			MetadataPlugin::getApiValue(self::METADATA_FIELD_MATCH) => 'Validate that any of the metadata elements text matches any of listed textual values.',
			MetadataPlugin::getApiValue(self::METADATA_FIELD_CHANGED) => 'Check if metadata element changed between metadata versions.',
		);
	}
}
