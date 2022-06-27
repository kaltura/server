<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCategoriesCsvJobData extends kMappedObjectsCsvJobData
{
	/**
	 * The filter should return the list of categories that need to be specified in the csv.
	 * @var categoryFilter
	 */
	protected $filter;

	/**
	 * @return categoryFilter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param categoryFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}
}
