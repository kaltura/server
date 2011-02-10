<?php
/**
 * @package plugins.mySqlSearch
 */
class MySqlSearchPlugin extends KalturaPlugin implements IKalturaEventConsumers, IKalturaCriteriaFactory
{
	const PLUGIN_NAME = 'mySqlSearch';
	const MYSQL_SEARCH_MANAGER = 'kMySqlSearchManager';
	
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
			self::MYSQL_SEARCH_MANAGER,
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
			return new MySqlEntryCriteria();
			
		return null;
	}
}
