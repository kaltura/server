<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kUsersCsvJobData extends kMappedObjectsCsvJobData
{
	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 * @var kuserFilter
	 */
	protected $filter;

	/**
	 *
	 * @return kuserFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}
	
	/**
	 * @param kuserFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}
}
