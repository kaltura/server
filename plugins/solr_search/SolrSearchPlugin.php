<?php
class SolrSearchPlugin extends KalturaPlugin implements KalturaEventConsumersPlugin
{
	const PLUGIN_NAME = 'solr_search';
	const SOLR_SEARCH_MANAGER = 'kSolrSearchManager';
	
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
			self::SOLR_SEARCH_MANAGER,
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
		if ($objectType == entryPeer::OM_CLASS)
			return new SolrEntryCriteria();
			
		return null;
	}
}
