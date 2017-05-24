<?php
/**
 * @package plugins.sphinxSearch
 */
class SphinxSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaCriteriaFactory, IKalturaPending
{
	const PLUGIN_NAME = 'sphinxSearch';
	const SPHINX_SEARCH_MANAGER = 'kSphinxSearchManager';
	
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @return array
	 */
	public static function getEventConsumers()
	{
		return array(
			self::SPHINX_SEARCH_MANAGER,
		);
	}
	
	/**
	 * Creates a new KalturaCriteria for the given object name
	 * 
	 * @param string $objectType object type to create Criteria for.
	 * @return KalturaCriteria derived object
	 */
	public static function getKalturaCriteria($objectType)
	{
		if ($objectType == "entry")
			return new SphinxEntryCriteria();
			
		if ($objectType == "category")
			return new SphinxCategoryCriteria();
			
		if ($objectType == "kuser")
			return new SphinxKuserCriteria();
		
		if ($objectType == "categoryKuser")
			return new SphinxCategoryKuserCriteria();
			
		return null;
	}

	/**
	 * Returns a Kaltura dependency object that defines the relationship between two plugins.
	 *
	 * @return array<KalturaDependency> The Kaltura dependency object
	 */
	public static function dependsOn()
	{
		$searchDependency = new KalturaDependency(SearchPlugin::getPluginName());
		return array($searchDependency);
	}
}
