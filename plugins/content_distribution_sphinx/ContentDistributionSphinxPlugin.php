<?php
/**
 * Enable indexing and searching cue point objects in sphinx
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
	public static function getSphinxSchema(){
		return ContentDistributionSphinxConfiguration::getConfiguration();
	}
	
	/**
	 * 
	 * return field name as appears in sphinx schema
	 * @param string $fieldName
	 */
	public static function getSphinxFieldName($fieldName){
		return self::PLUGIN_NAME . '_' . $fieldName;
	}
	

}
