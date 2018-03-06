<?php
/**
 * @package plugins.viewHistory
 * @subpackage model.filters
 */
class kCatalogItemAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var VendorCatalogItemFilter
	 */
	public $baseFilter;
	
	public function getBaseFilter()
	{
		return $this->baseFilter;
	}
	
	public function setBaseFilter($baseFilter)
	{
		$this->baseFilter = $baseFilter;
	}
}
