<?php
/**
 * @package plugins.tagSearch
 */
class TagSearchPlugin extends KalturaPlugin implements  IKalturaCriteriaFactory, IKalturaSphinxConfiguration, IKalturaEventConsumers, IKalturaServices, IKalturaConfigurator, IKalturaEnumerator, IKalturaObjectLoader
{
    const PLUGIN_NAME = "tagSearch";
    
    const INDEX_NAME = "tag";
    
    const MIN_TAG_SEARCH_LENGTH = 3;
    
    public static function getPluginName ()
    {
        return self::PLUGIN_NAME;
    }
    
    public static function getSphinxSchemaFields()
	{
		return  array(
		    'int_id' =>SphinxFieldType::RT_ATTR_BIGINT,
		    'tag' => SphinxFieldType::RT_FIELD,
		    'object_type' => SphinxFieldType::RT_FIELD,
		    'partner_id' => SphinxFieldType::RT_FIELD,
			'privacy_context' => SphinxFieldType::RT_FIELD,
		    'instance_count' => SphinxFieldType::RT_ATTR_BIGINT,
		    'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
		);
	}
	
	public static function getSphinxSchema ()
	{
	    return array(
			kSphinxSearchManager::getSphinxIndexName(self::INDEX_NAME) => array (	
				'path'		=> '/sphinx/kaltura_tag_rt',
				'fields'	=> self::getSphinxSchemaFields(),
			    'dict'      => 'keywords',
                'min_prefix_len' => self::MIN_TAG_SEARCH_LENGTH,
                'enable_star' => '1',
			
			)
		);
	}
    
	public static function getEventConsumers()
	{
	    return array('kTagFlowManager');
	}
	
	public static function getKalturaCriteria($objectType)
	{
	    if ($objectType == TagPeer::OM_CLASS)
			return new SphinxTagCriteria();
			
		return null;
	}
	
	public static function getServicesMap ()
	{
	    $map = array(
			'tag' => 'TagService',
		);
		return $map;
	}
	

	/* (non-PHPdoc)
	 * @see IKalturaConfigurator::getConfig()
	 */
	public static function getConfig($configName)
	{
		if($configName == 'generator')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/generator.ini');
			
		if($configName == 'testme')
			return new Zend_Config_Ini(dirname(__FILE__) . '/config/testme.ini');
			
		return null;
	}
	

	/* (non-PHPdoc)
	 * @see IKalturaEnumerator::getEnums()
	 */
	public static function getEnums($baseEnumName = null)
	{
		if(is_null($baseEnumName))
			return array('TagResolveBatchJobType', 'IndexTagsByPrivacyContextJobType');
		
		if($baseEnumName == 'BatchJobType')
			return array('TagResolveBatchJobType', 'IndexTagsByPrivacyContextJobType');
			
		return array();
	}
	
	/**
	 * @return int id of dynamic enum in the DB.
	 */
	public static function getBatchJobTypeCoreValue($valueName)
	{
		$value = self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
		return kPluginableEnumsManager::apiToCore('BatchJobType', $value);
	}
	
	/**
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::loadObject()
	 */
	public static function loadObject($baseClass, $enumValue, array $constructorArgs = null)
	{
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(IndexTagsByPrivacyContextJobType::INDEX_TAGS))
			return new kIndexTagsByPrivacyContextJobData();
	
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(IndexTagsByPrivacyContextJobType::INDEX_TAGS))
			return new KalturaIndexTagsByPrivacyContextJobData();
		
		return null;
	}
	
	
	/* (non-PHPdoc)
	 * @see IKalturaObjectLoader::getObjectClass()
	 */
	public static function getObjectClass($baseClass, $enumValue)
	{
		if($baseClass == 'kJobData' && $enumValue == self::getBatchJobTypeCoreValue(IndexTagsByPrivacyContextJobType::INDEX_TAGS))
			return new kIndexTagsByPrivacyContextJobData();
	
		if($baseClass == 'KalturaJobData' && $enumValue == self::getApiValue(IndexTagsByPrivacyContextJobType::INDEX_TAGS))
			return new KalturaIndexTagsByPrivacyContextJobData();
		
		return null;
	}
	
}