<?php
class SphinxSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaCriteriaFactory
{
	const PLUGIN_NAME = 'sphinx_search';
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
			
		return null;
	}
}
