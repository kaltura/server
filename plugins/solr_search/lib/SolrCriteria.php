<?php

class SolrCriteria extends Criteria
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
}