<?php
class SolrSearchPlugin extends KalturaPlugin
{
	const PLUGIN_NAME = 'solr_search';
	const SOLR_SEARCH_MANAGER = 'kSolrSearchManager';
	
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
		if ($objectType == "entry")
			return new SolrEntryCriteria();
			
		return null;
	}
}
