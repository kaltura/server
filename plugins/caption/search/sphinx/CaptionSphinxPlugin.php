<?php
/**
 * Enable indexing and searching caption asset objects in sphinx
 * @package plugins.contentDistribution
 */
class CaptionSphinxPlugin extends KalturaPlugin implements IKalturaCriteriaFactory, IKalturaSphinxConfiguration
{
	const PLUGIN_NAME = 'captionSphinx';
	const INDEX_NAME = 'caption_item';
	
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
		if ($objectType == "CaptionAssetItem")
			return new SphinxCaptionAssetItemCriteria();
			
		return null;
	}
	
	public static function getSphinxSchemaFields()
	{
		return  array(
			'entry_id' => SphinxFieldType::RT_FIELD,
			'caption_asset_id' => SphinxFieldType::RT_FIELD,
			'tags' => SphinxFieldType::RT_FIELD,
			'content' => SphinxFieldType::RT_FIELD,
			'partner_description' => SphinxFieldType::RT_FIELD,
			'language' => SphinxFieldType::RT_FIELD,
			'label' => SphinxFieldType::RT_FIELD,
			'format' => SphinxFieldType::RT_FIELD,
			
			'int_caption_asset_id' => SphinxFieldType::RT_ATTR_BIGINT,
			'caption_params_id' => SphinxFieldType::RT_ATTR_BIGINT,
			'partner_id' => SphinxFieldType::RT_ATTR_BIGINT,
			'version' => SphinxFieldType::RT_ATTR_BIGINT,
			'caption_asset_status' => SphinxFieldType::RT_ATTR_BIGINT,
			'size' => SphinxFieldType::RT_ATTR_BIGINT,
			'is_default' => SphinxFieldType::RT_ATTR_BIGINT,
			'start_time' => SphinxFieldType::RT_ATTR_BIGINT,
			'end_time' => SphinxFieldType::RT_ATTR_BIGINT,
			
			'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
			'updated_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
			'deleted_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
			
			'str_entry_id' => SphinxFieldType::RT_ATTR_STRING,
			'str_caption_asset_id' => SphinxFieldType::RT_ATTR_STRING,
			'str_content' => SphinxFieldType::RT_ATTR_STRING,
		);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaSphinxConfiguration::getSphinxSchema()
	 */
	public static function getSphinxSchema()
	{
		return array(
			kSphinxSearchManager::getSphinxIndexName('entry') => array (
				'fields' => array(
					CaptionSphinxPlugin::getSphinxFieldName(CaptionPlugin::SEARCH_FIELD_DATA) => SphinxFieldType::RT_FIELD,
				)
			),
			kSphinxSearchManager::getSphinxIndexName(CaptionSphinxPlugin::INDEX_NAME) => array (	
				'path'		=> '/sphinx/kaltura_caption_item_rt',
				'fields'	=> self::getSphinxSchemaFields(),
			)
		);
	}
	
	/**
	 * return field name as appears in sphinx schema
	 * @param string $fieldName
	 */
	public static function getSphinxFieldName($fieldName){
		return CaptionPlugin::getPluginName() . '_' . $fieldName;
	}
}
