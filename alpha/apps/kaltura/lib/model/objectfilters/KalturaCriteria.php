<?php

class KalturaCriteria extends Criteria
{
	/**
	 * @var array<baseObjectFilter>
	 */
	protected $filters = array();
	
	/**
	 * Store the filter as is
	 * Later the filter fields and the advanced search will be used to attach additional criterions
	 * 
	 * @param baseObjectFilter $filter
	 */
	public function attachFilter(baseObjectFilter $filter)
	{
		$this->filters[] = $filter;
	}
	
	/**
	 * Applies all filter on this criteria
	 */
	public function applyFilters()
	{
		foreach($this->filters as $filter)
			$filter->attachToFinalCriteria($this);
	}
	
	/**
	 * Creates a new KalturaCriteria for the given object name
	 * 
	 * @param string $objectType object type to create Criteria for.
	 * @return KalturaCriteria derived object
	 */
	public static function create($objectType)
	{
		$searchPluginName = kConf::get("search_plugin");
		
		$searchPlugin = KalturaPluginManager::getPluginInstance($searchPluginName);
			
		$criteriaFactory = $searchPlugin->getInstance('IKalturaCriteriaFactory');
		if (!$criteriaFactory)
		{
			KalturaLog::err("KalturaCriteria IKalturaCriteriaFactory not found for [$searchPluginName] plugin");
			die;
		}
			
		return $criteriaFactory->getKalturaCriteria($objectType);
	}
}