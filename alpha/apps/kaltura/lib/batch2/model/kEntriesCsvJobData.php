<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kEntriesCsvJobData extends kMappedObjectsCsvJobData
{
	/**
	 * The filter should return the list of entries that need to be specified in the csv.
	 * @var entryFilter
	 */
	protected $filter;

	/**
	 * @return entryFilter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param entryFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}
}
