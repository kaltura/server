<?php
/**
 * Enable indexing and searching cue point objects in sphinx
 * @package plugins.cuePoint
 */
class CuePointSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaSphinxConfiguration
{
	const PLUGIN_NAME = 'cuePointSphinx';
	
	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaCriteriaFactory::getKalturaCriteria()
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == "CuePoint")
			return new SphinxCuePointCriteria();
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSphinxConfiguration::getSphinxSchema()
	 */
	public static function getSphinxSchema()
	{
		return array(
			kSphinxSearchManager::getSphinxIndexName('cue_point') => array (	
				'path'		=> '/sphinx/kaltura_cue_point_rt',
				'fields'	=> array (
					'parent_id' => SphinxFieldType::RT_FIELD,
					'entry_id' => SphinxFieldType::RT_FIELD,
					'name' => SphinxFieldType::RT_FIELD,
					'system_name' => SphinxFieldType::RT_FIELD,
					'text' => SphinxFieldType::RT_FIELD,
					'tags' => SphinxFieldType::RT_FIELD,
					'roots' => SphinxFieldType::RT_FIELD,
					
					'int_cue_point_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'cue_point_int_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'partner_id' => SphinxFieldType::RT_ATTR_BIGINT,
					'start_time' => SphinxFieldType::RT_ATTR_BIGINT,
					'end_time' => SphinxFieldType::RT_ATTR_BIGINT,
					'duration' => SphinxFieldType::RT_ATTR_BIGINT,
					'cue_point_status' => SphinxFieldType::RT_ATTR_BIGINT,
					'cue_point_type' => SphinxFieldType::RT_FIELD,
					'sub_type' => SphinxFieldType::RT_ATTR_BIGINT,
					'kuser_id' => SphinxFieldType::RT_FIELD,
					'partner_sort_value' => SphinxFieldType::RT_ATTR_BIGINT,
					'force_stop' => SphinxFieldType::RT_ATTR_UINT,
					
					'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
					'updated_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
	
					'str_cue_point_id' => SphinxFieldType::RT_ATTR_STRING
				)
			)
		);
	}
}
