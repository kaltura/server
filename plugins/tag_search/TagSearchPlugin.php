<?php
class TagSearchPlugin extends KalturaPlugin implements  IKalturaCriteriaFactory, IKalturaSphinxConfiguration, IKalturaEventConsumers, IKalturaServices, IKalturaConfigurator
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
		    'object_type' => SphinxFieldType::RT_ATTR_UINT,
		    'partner_id' => SphinxFieldType::RT_ATTR_BIGINT,
		    'instance_count' => SphinxFieldType::RT_ATTR_BIGINT,
		    'created_at' => SphinxFieldType::RT_ATTR_TIMESTAMP,
		);
	}
	
	public static function getSphinxSchema ()
	{
	    return array(
			kSphinxSearchManager::getSphinxIndexName(TagSearchPlugin::INDEX_NAME) => array (	
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
	
	
}