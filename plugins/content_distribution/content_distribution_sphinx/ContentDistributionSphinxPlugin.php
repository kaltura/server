<?php
/**
 * Enable indexing and searching ntry distribution objects in sphinx
 * @package plugins.contentDistribution
 */
class ContentDistributionSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaSphinxConfiguration
{
	const PLUGIN_NAME = 'contentDistributionSphinx';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * Creates a new KalturaCriteria for the given object name
	 * 
	 * @param string $objectType object type to create Criteria for.
	 * @return KalturaCriteria derived object
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == "EntryDistribution")
			return new SphinxEntryDistributionCriteria();
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSphinxConfiguration::getSphinxSchema()
	 */
	public static function getSphinxSchema()
	{
		return array(
			kSphinxSearchManager::getSphinxIndexName('entry_distribution') => array (	
				'path'		=> '/sphinx/kaltura_distribution_rt',
				'fields'	=> array(
					'entry_id' => SphinxFieldType::RT_FIELD,
					'thumb_asset_ids' => SphinxFieldType::RT_FIELD,
					'flavor_asset_ids' => SphinxFieldType::RT_FIELD,
					'remote_id' => SphinxFieldType::RT_FIELD,
					
					'int_entry_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'entry_distribution_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'partner_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'distribution_profile_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'entry_distribution_status' => SphinxFieldType::RT_ATTR_BIGINT,
					'dirty_status' => SphinxFieldType::RT_ATTR_BIGINT,
					'sun_status' => SphinxFieldType::RT_ATTR_BIGINT,
					'plays' => SphinxFieldType::RT_ATTR_BIGINT,
					'views' => SphinxFieldType::RT_ATTR_BIGINT,
					'error_type' => SphinxFieldType::RT_ATTR_BIGINT,
					'error_number' => SphinxFieldType::RT_ATTR_BIGINT,
					
					'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'updated_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'submitted_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'sunrise' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'sunset' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'last_report' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'next_report' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					
					'str_entry_id' => SphinxFieldType::RT_ATTR_STRING
				)
			)
		);
	}
	
	/**
	 * return field name as appears in sphinx schema
	 * @param string $fieldName
	 */
	public static function getSphinxFieldName($fieldName){
		if ($fieldName == ContentDistributionPlugin::SPHINX_EXPANDER_FIELD_DATA)
			return 'plugins_data';
			
		return 'content_d_' . $fieldName;
	}
}
